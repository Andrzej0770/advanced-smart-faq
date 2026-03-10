<?php
/**
 * FAQ Schema (JSON-LD) output.
 *
 * Generates FAQPage structured data for SEO following the schema.org specification.
 * The markup is injected into the page <head> via wp_head whenever the [smart_faq]
 * shortcode is present in the current post/page content.
 *
 * @package AdvancedSmartFAQ
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ASFAQ_Schema
 *
 * Outputs FAQPage JSON-LD structured data.
 *
 * @since 1.0.0
 */
class ASFAQ_Schema {

	/**
	 * Constructor. Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'output_schema' ), 99 );
	}

	/**
	 * Determine whether schema output is enabled in settings.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	private function is_schema_enabled() {
		$options = get_option( 'asfaq_settings', array() );
		return ! empty( $options['enable_schema'] );
	}

	/**
	 * Check if the current page contains the [smart_faq] shortcode.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	private function page_has_faq_shortcode() {
		if ( ! is_singular() ) {
			return false;
		}

		global $post;

		if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
			return false;
		}

		return has_shortcode( $post->post_content, 'smart_faq' );
	}

	/**
	 * Retrieve published FAQ posts for schema generation.
	 *
	 * Parses the shortcode attributes from the current post to respect
	 * category and limit settings.
	 *
	 * @since  1.0.0
	 * @return array Array of WP_Post objects.
	 */
	private function get_faq_posts() {
		global $post;

		$options       = get_option( 'asfaq_settings', array() );
		$default_limit = isset( $options['default_limit'] ) ? absint( $options['default_limit'] ) : 10;

		// Try to parse shortcode attributes from the post content.
		$category = '';
		$limit    = $default_limit;

		if ( preg_match( '/\[smart_faq([^\]]*)\]/', $post->post_content, $matches ) ) {
			$raw_atts = shortcode_parse_atts( $matches[1] );

			if ( ! empty( $raw_atts['category'] ) ) {
				$category = sanitize_text_field( $raw_atts['category'] );
			}

			if ( ! empty( $raw_atts['limit'] ) ) {
				$limit = absint( $raw_atts['limit'] );
			}
		}

		$query_args = array(
			'post_type'      => ASFAQ_Post_Type::POST_TYPE,
			'posts_per_page' => $limit,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		);

		if ( ! empty( $category ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => ASFAQ_Post_Type::TAXONOMY,
					'field'    => 'slug',
					'terms'    => $category,
				),
			);
		}

		$query = new WP_Query( $query_args );

		return $query->posts;
	}

	/**
	 * Build the FAQPage schema array.
	 *
	 * @since  1.0.0
	 * @param  array $posts Array of WP_Post objects.
	 * @return array Schema data array.
	 */
	private function build_schema( $posts ) {
		$main_entity = array();

		foreach ( $posts as $faq_post ) {
			$question = wp_strip_all_tags( $faq_post->post_title );
			$answer   = wp_strip_all_tags( apply_filters( 'the_content', $faq_post->post_content ) );

			if ( empty( $question ) || empty( $answer ) ) {
				continue;
			}

			$main_entity[] = array(
				'@type'          => 'Question',
				'name'           => $question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}

		if ( empty( $main_entity ) ) {
			return array();
		}

		return array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => $main_entity,
		);
	}

	/**
	 * Output JSON-LD structured data in the page head.
	 *
	 * @since 1.0.0
	 */
	public function output_schema() {
		if ( ! $this->is_schema_enabled() ) {
			return;
		}

		if ( ! $this->page_has_faq_shortcode() ) {
			return;
		}

		$posts = $this->get_faq_posts();

		if ( empty( $posts ) ) {
			return;
		}

		$schema = $this->build_schema( $posts );

		if ( empty( $schema ) ) {
			return;
		}

		$json = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

		if ( ! $json ) {
			return;
		}

		echo "\n<!-- Advanced Smart FAQ – Structured Data -->\n";
		echo '<script type="application/ld+json">' . "\n";
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD must not be escaped.
		echo $json . "\n";
		echo '</script>' . "\n";
	}
}
