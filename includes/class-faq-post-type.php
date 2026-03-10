<?php
/**
 * FAQ Custom Post Type and Taxonomy registration.
 *
 * Registers the `faq` post type and `faq_category` taxonomy,
 * customizes admin columns, and adds ordering support.
 *
 * @package AdvancedSmartFAQ
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ASFAQ_Post_Type
 *
 * Handles the FAQ custom post type and taxonomy.
 *
 * @since 1.0.0
 */
class ASFAQ_Post_Type {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const POST_TYPE = 'faq';

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	const TAXONOMY = 'faq_category';

	/**
	 * Constructor. Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( $this, 'custom_columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'custom_column_content' ), 10, 2 );
		add_filter( 'manage_edit-' . self::POST_TYPE . '_sortable_columns', array( $this, 'sortable_columns' ) );
	}

	/**
	 * Register the FAQ custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'FAQs', 'Post type general name', 'advanced-smart-faq' ),
			'singular_name'         => _x( 'FAQ', 'Post type singular name', 'advanced-smart-faq' ),
			'menu_name'             => _x( 'FAQs', 'Admin Menu text', 'advanced-smart-faq' ),
			'name_admin_bar'        => _x( 'FAQ', 'Add New on Toolbar', 'advanced-smart-faq' ),
			'add_new'               => __( 'Add New', 'advanced-smart-faq' ),
			'add_new_item'          => __( 'Add New FAQ', 'advanced-smart-faq' ),
			'new_item'              => __( 'New FAQ', 'advanced-smart-faq' ),
			'edit_item'             => __( 'Edit FAQ', 'advanced-smart-faq' ),
			'view_item'             => __( 'View FAQ', 'advanced-smart-faq' ),
			'all_items'             => __( 'All FAQs', 'advanced-smart-faq' ),
			'search_items'          => __( 'Search FAQs', 'advanced-smart-faq' ),
			'parent_item_colon'     => __( 'Parent FAQs:', 'advanced-smart-faq' ),
			'not_found'             => __( 'No FAQs found.', 'advanced-smart-faq' ),
			'not_found_in_trash'    => __( 'No FAQs found in Trash.', 'advanced-smart-faq' ),
			'archives'              => __( 'FAQ Archives', 'advanced-smart-faq' ),
			'filter_items_list'     => __( 'Filter FAQs list', 'advanced-smart-faq' ),
			'items_list_navigation' => __( 'FAQs list navigation', 'advanced-smart-faq' ),
			'items_list'            => __( 'FAQs list', 'advanced-smart-faq' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'faq' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-editor-help',
			'supports'           => array( 'title', 'editor', 'page-attributes' ),
			'show_in_rest'       => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Register the FAQ Category taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'                       => _x( 'FAQ Categories', 'Taxonomy general name', 'advanced-smart-faq' ),
			'singular_name'              => _x( 'FAQ Category', 'Taxonomy singular name', 'advanced-smart-faq' ),
			'search_items'               => __( 'Search FAQ Categories', 'advanced-smart-faq' ),
			'all_items'                  => __( 'All FAQ Categories', 'advanced-smart-faq' ),
			'parent_item'                => __( 'Parent FAQ Category', 'advanced-smart-faq' ),
			'parent_item_colon'          => __( 'Parent FAQ Category:', 'advanced-smart-faq' ),
			'edit_item'                  => __( 'Edit FAQ Category', 'advanced-smart-faq' ),
			'update_item'                => __( 'Update FAQ Category', 'advanced-smart-faq' ),
			'add_new_item'               => __( 'Add New FAQ Category', 'advanced-smart-faq' ),
			'new_item_name'              => __( 'New FAQ Category Name', 'advanced-smart-faq' ),
			'menu_name'                  => __( 'Categories', 'advanced-smart-faq' ),
			'not_found'                  => __( 'No FAQ categories found.', 'advanced-smart-faq' ),
			'no_terms'                   => __( 'No FAQ categories', 'advanced-smart-faq' ),
			'items_list_navigation'      => __( 'FAQ Categories list navigation', 'advanced-smart-faq' ),
			'items_list'                 => __( 'FAQ Categories list', 'advanced-smart-faq' ),
			'back_to_items'              => __( '&larr; Back to FAQ Categories', 'advanced-smart-faq' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'faq-category' ),
		);

		register_taxonomy( self::TAXONOMY, self::POST_TYPE, $args );
	}

	/**
	 * Customize admin list columns.
	 *
	 * @since  1.0.0
	 * @param  array $columns Default columns.
	 * @return array Modified columns.
	 */
	public function custom_columns( $columns ) {
		$new_columns = array();

		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;

			// Insert order column after the title.
			if ( 'title' === $key ) {
				$new_columns['faq_order'] = __( 'Order', 'advanced-smart-faq' );
			}
		}

		return $new_columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @since 1.0.0
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 */
	public function custom_column_content( $column, $post_id ) {
		if ( 'faq_order' === $column ) {
			echo esc_html( get_post_field( 'menu_order', $post_id ) );
		}
	}

	/**
	 * Make the order column sortable.
	 *
	 * @since  1.0.0
	 * @param  array $columns Sortable columns.
	 * @return array Modified sortable columns.
	 */
	public function sortable_columns( $columns ) {
		$columns['faq_order'] = 'menu_order';
		return $columns;
	}
}
