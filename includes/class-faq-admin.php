<?php
/**
 * FAQ Admin Settings page.
 *
 * Registers a settings page under Settings → Smart FAQ using the
 * WordPress Settings API. Provides controls for schema, animation
 * speed, default limit, and search toggle.
 *
 * @package AdvancedSmartFAQ
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ASFAQ_Admin
 *
 * Handles the admin settings page and options registration.
 *
 * @since 1.0.0
 */
class ASFAQ_Admin {

	/**
	 * Option name stored in the database.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'asfaq_settings';

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'asfaq-settings';

	/**
	 * Constructor. Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'plugin_action_links_' . ASFAQ_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * Add a "Settings" link to the plugins list page.
	 *
	 * @since  1.0.0
	 * @param  array $links Existing plugin action links.
	 * @return array Modified links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=' . self::PAGE_SLUG ) ),
			esc_html__( 'Settings', 'advanced-smart-faq' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Register the settings page under Settings menu.
	 *
	 * @since 1.0.0
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Smart FAQ Settings', 'advanced-smart-faq' ),
			__( 'Smart FAQ', 'advanced-smart-faq' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueue admin-specific styles on the settings page.
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public function enqueue_admin_styles( $hook_suffix ) {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'asfaq-admin-style',
			ASFAQ_PLUGIN_URL . 'assets/css/faq-style.css',
			array(),
			ASFAQ_VERSION
		);
	}

	/**
	 * Register settings, sections, and fields.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting(
			'asfaq_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => array(
					'enable_schema'   => 1,
					'animation_speed' => 300,
					'default_limit'   => 10,
					'enable_search'   => 1,
				),
			)
		);

		// General settings section.
		add_settings_section(
			'asfaq_general_section',
			__( 'General Settings', 'advanced-smart-faq' ),
			array( $this, 'render_general_section' ),
			self::PAGE_SLUG
		);

		// Enable Schema field.
		add_settings_field(
			'enable_schema',
			__( 'Enable FAQ Schema', 'advanced-smart-faq' ),
			array( $this, 'render_checkbox_field' ),
			self::PAGE_SLUG,
			'asfaq_general_section',
			array(
				'field'       => 'enable_schema',
				'label'       => __( 'Output FAQPage structured data (JSON-LD) for SEO.', 'advanced-smart-faq' ),
			)
		);

		// Animation Speed field.
		add_settings_field(
			'animation_speed',
			__( 'Accordion Animation Speed', 'advanced-smart-faq' ),
			array( $this, 'render_number_field' ),
			self::PAGE_SLUG,
			'asfaq_general_section',
			array(
				'field'       => 'animation_speed',
				'description' => __( 'Duration in milliseconds (e.g., 300).', 'advanced-smart-faq' ),
				'min'         => 0,
				'max'         => 2000,
				'step'        => 50,
			)
		);

		// Default Limit field.
		add_settings_field(
			'default_limit',
			__( 'Default FAQ Limit', 'advanced-smart-faq' ),
			array( $this, 'render_number_field' ),
			self::PAGE_SLUG,
			'asfaq_general_section',
			array(
				'field'       => 'default_limit',
				'description' => __( 'Default number of FAQs to display when no limit is specified in the shortcode.', 'advanced-smart-faq' ),
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
			)
		);

		// Enable Search field.
		add_settings_field(
			'enable_search',
			__( 'Enable FAQ Search', 'advanced-smart-faq' ),
			array( $this, 'render_checkbox_field' ),
			self::PAGE_SLUG,
			'asfaq_general_section',
			array(
				'field'       => 'enable_search',
				'label'       => __( 'Show a search input above the FAQ list for real-time filtering.', 'advanced-smart-faq' ),
			)
		);
	}

	/**
	 * Sanitize settings before saving.
	 *
	 * @since  1.0.0
	 * @param  array $input Raw input values.
	 * @return array Sanitized values.
	 */
	public function sanitize_settings( $input ) {
		$sanitized = array();

		$sanitized['enable_schema']   = ! empty( $input['enable_schema'] ) ? 1 : 0;
		$sanitized['animation_speed'] = isset( $input['animation_speed'] ) ? absint( $input['animation_speed'] ) : 300;
		$sanitized['default_limit']   = isset( $input['default_limit'] ) ? absint( $input['default_limit'] ) : 10;
		$sanitized['enable_search']   = ! empty( $input['enable_search'] ) ? 1 : 0;

		// Clamp values.
		$sanitized['animation_speed'] = min( max( $sanitized['animation_speed'], 0 ), 2000 );
		$sanitized['default_limit']   = min( max( $sanitized['default_limit'], 1 ), 100 );

		return $sanitized;
	}

	/**
	 * Render the general section description.
	 *
	 * @since 1.0.0
	 */
	public function render_general_section() {
		echo '<p>' . esc_html__( 'Configure how the Advanced Smart FAQ plugin behaves on the front end.', 'advanced-smart-faq' ) . '</p>';
	}

	/**
	 * Render a checkbox field.
	 *
	 * @since 1.0.0
	 * @param array $args Field arguments.
	 */
	public function render_checkbox_field( $args ) {
		$options = get_option( self::OPTION_NAME, array() );
		$field   = $args['field'];
		$checked = ! empty( $options[ $field ] ) ? 1 : 0;
		?>
		<label for="asfaq_<?php echo esc_attr( $field ); ?>">
			<input
				type="checkbox"
				id="asfaq_<?php echo esc_attr( $field ); ?>"
				name="<?php echo esc_attr( self::OPTION_NAME . '[' . $field . ']' ); ?>"
				value="1"
				<?php checked( $checked, 1 ); ?>
			/>
			<?php echo esc_html( $args['label'] ); ?>
		</label>
		<?php
	}

	/**
	 * Render a number field.
	 *
	 * @since 1.0.0
	 * @param array $args Field arguments.
	 */
	public function render_number_field( $args ) {
		$options = get_option( self::OPTION_NAME, array() );
		$field   = $args['field'];
		$value   = isset( $options[ $field ] ) ? absint( $options[ $field ] ) : '';
		?>
		<input
			type="number"
			id="asfaq_<?php echo esc_attr( $field ); ?>"
			name="<?php echo esc_attr( self::OPTION_NAME . '[' . $field . ']' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			min="<?php echo esc_attr( $args['min'] ); ?>"
			max="<?php echo esc_attr( $args['max'] ); ?>"
			step="<?php echo esc_attr( $args['step'] ); ?>"
			class="small-text"
		/>
		<?php if ( ! empty( $args['description'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['description'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the settings page HTML.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Show success message after save.
		if ( isset( $_GET['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_settings_error(
				'asfaq_messages',
				'asfaq_message',
				__( 'Settings saved.', 'advanced-smart-faq' ),
				'updated'
			);
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<?php settings_errors( 'asfaq_messages' ); ?>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'asfaq_settings_group' );
				do_settings_sections( self::PAGE_SLUG );
				submit_button( __( 'Save Settings', 'advanced-smart-faq' ) );
				?>
			</form>

			<hr />

			<div class="asfaq-admin-help">
				<h2><?php esc_html_e( 'Shortcode Usage', 'advanced-smart-faq' ); ?></h2>
				<p><?php esc_html_e( 'Use the following shortcode to display FAQs on any page or post:', 'advanced-smart-faq' ); ?></p>
				<code>[smart_faq]</code>

				<h3><?php esc_html_e( 'Available Attributes', 'advanced-smart-faq' ); ?></h3>
				<table class="widefat fixed" style="max-width: 600px;">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Attribute', 'advanced-smart-faq' ); ?></th>
							<th><?php esc_html_e( 'Description', 'advanced-smart-faq' ); ?></th>
							<th><?php esc_html_e( 'Example', 'advanced-smart-faq' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><code>limit</code></td>
							<td><?php esc_html_e( 'Number of FAQs to display.', 'advanced-smart-faq' ); ?></td>
							<td><code>[smart_faq limit="5"]</code></td>
						</tr>
						<tr>
							<td><code>category</code></td>
							<td><?php esc_html_e( 'Filter by FAQ category slug.', 'advanced-smart-faq' ); ?></td>
							<td><code>[smart_faq category="seo"]</code></td>
						</tr>
						<tr>
							<td><code>style</code></td>
							<td><?php esc_html_e( 'Display style (currently: accordion).', 'advanced-smart-faq' ); ?></td>
							<td><code>[smart_faq style="accordion"]</code></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
}
