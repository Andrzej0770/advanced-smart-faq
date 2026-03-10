<?php
/**
 * FAQ Shortcode handler.
 *
 * Registers the [smart_faq] shortcode and renders the FAQ accordion
 * on the front end with optional category filter, limit, and search.
 *
 * @package AdvancedSmartFAQ
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ASFAQ_Shortcode
 *
 * Handles shortcode registration and front-end rendering.
 *
 * @since 1.0.0
 */
class ASFAQ_Shortcode {

	/**
	 * Constructor. Register the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_shortcode( 'smart_faq', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render the [smart_faq] shortcode output.
	 *
	 * Accepts the following attributes:
	 * - limit    (int)    Number of FAQs to display. Default: from settings or 10.
	 * - category (string) FAQ category slug to filter by.
	 * - style    (string) Display style. Currently supports "accordion" (default).
	 *
	 * @since  1.0.0
	 * @param  array|string $atts Shortcode attributes.
	 * @return string            Rendered HTML.
	 */
	public function render_shortcode( $atts ) {
		$options       = get_option( 'asfaq_settings', array() );
		$default_limit = isset( $options['default_limit'] ) ? absint( $options['default_limit'] ) : 10;
		$enable_search = isset( $options['enable_search'] ) ? (bool) $options['enable_search'] : true;

		$atts = shortcode_atts(
			array(
				'limit'    => $default_limit,
				'category' => '',
				'style'    => 'accordion',
			),
			$atts,
			'smart_faq'
		);

		$query_args = array(
			'post_type'      => ASFAQ_Post_Type::POST_TYPE,
			'posts_per_page' => absint( $atts['limit'] ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		);

		// Filter by category slug if provided.
		if ( ! empty( $atts['category'] ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => ASFAQ_Post_Type::TAXONOMY,
					'field'    => 'slug',
					'terms'    => sanitize_text_field( $atts['category'] ),
				),
			);
		}

		$faq_query = new WP_Query( $query_args );

		if ( ! $faq_query->have_posts() ) {
			return '<p class="asfaq-no-results">' . esc_html__( 'No FAQs found.', 'advanced-smart-faq' ) . '</p>';
		}

		ob_start();

		$this->render_faq_list( $faq_query, $atts, $enable_search );

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Render the FAQ accordion list.
	 *
	 * @since 1.0.0
	 * @param WP_Query $faq_query     The FAQ query object.
	 * @param array    $atts          Shortcode attributes.
	 * @param bool     $enable_search Whether to show the search input.
	 */
	private function render_faq_list( $faq_query, $atts, $enable_search ) {
		$style_class = 'asfaq-style-' . esc_attr( sanitize_html_class( $atts['style'] ) );
		?>
		<div class="asfaq-container <?php echo esc_attr( $style_class ); ?>" role="region" aria-label="<?php esc_attr_e( 'Frequently Asked Questions', 'advanced-smart-faq' ); ?>">

			<?php if ( $enable_search ) : ?>
				<div class="asfaq-search-wrap">
					<label for="asfaq-search-input" class="screen-reader-text">
						<?php esc_html_e( 'Search FAQs', 'advanced-smart-faq' ); ?>
					</label>
					<input
						type="text"
						id="asfaq-search-input"
						class="asfaq-search-input"
						placeholder="<?php esc_attr_e( 'Search FAQs…', 'advanced-smart-faq' ); ?>"
						aria-controls="asfaq-list"
					/>
				</div>
			<?php endif; ?>

			<div id="asfaq-list" class="asfaq-list" role="list">
				<?php
				$index = 0;
				while ( $faq_query->have_posts() ) :
					$faq_query->the_post();
					$faq_id = 'asfaq-item-' . get_the_ID();
					$index++;
					?>
					<div
						class="asfaq-item"
						role="listitem"
						data-question="<?php echo esc_attr( strtolower( get_the_title() ) ); ?>"
						data-answer="<?php echo esc_attr( strtolower( wp_strip_all_tags( get_the_content() ) ) ); ?>"
					>
						<button
							class="asfaq-question"
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $faq_id ); ?>"
							id="<?php echo esc_attr( $faq_id . '-btn' ); ?>"
						>
							<span class="asfaq-question-text"><?php the_title(); ?></span>
							<span class="asfaq-icon" aria-hidden="true">
								<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
							</span>
						</button>
						<div
							class="asfaq-answer"
							id="<?php echo esc_attr( $faq_id ); ?>"
							role="region"
							aria-labelledby="<?php echo esc_attr( $faq_id . '-btn' ); ?>"
							hidden
						>
							<div class="asfaq-answer-inner">
								<?php
								// Use wp_kses_post to allow safe HTML in the answer.
								echo wp_kses_post( apply_filters( 'the_content', get_the_content() ) );
								?>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

			<p class="asfaq-no-search-results" hidden>
				<?php esc_html_e( 'No matching FAQs found.', 'advanced-smart-faq' ); ?>
			</p>

		</div>
		<?php
	}
}
