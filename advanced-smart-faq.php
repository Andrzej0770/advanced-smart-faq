<?php
/**
 * Plugin Name: Advanced Smart FAQ
 * Plugin URI:  https://github.com/Andrzej0770/advanced-smart-faq
 * Description: A modern, powerful FAQ system with accordion UI, SEO schema markup, category filtering, real-time search, and fully customizable settings.
 * Version:     1.0.1
 * Author:      Andrii Petrenko
 * Author URI:  https://github.com/Andrzej0770
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: advanced-smart-faq
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package AdvancedSmartFAQ
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin constants.
 */
define( 'ASFAQ_VERSION', '1.0.0' );
define( 'ASFAQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ASFAQ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ASFAQ_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoload plugin classes.
 */
require_once ASFAQ_PLUGIN_DIR . 'includes/class-faq-post-type.php';
require_once ASFAQ_PLUGIN_DIR . 'includes/class-faq-shortcode.php';
require_once ASFAQ_PLUGIN_DIR . 'includes/class-faq-schema.php';
require_once ASFAQ_PLUGIN_DIR . 'includes/class-faq-admin.php';

/**
 * Main plugin class.
 *
 * Bootstraps all plugin components and manages lifecycle hooks.
 *
 * @since 1.0.0
 */
final class Advanced_Smart_FAQ {

	/**
	 * Singleton instance.
	 *
	 * @var Advanced_Smart_FAQ|null
	 */
	private static $instance = null;

	/**
	 * FAQ Post Type handler.
	 *
	 * @var ASFAQ_Post_Type
	 */
	public $post_type;

	/**
	 * FAQ Shortcode handler.
	 *
	 * @var ASFAQ_Shortcode
	 */
	public $shortcode;

	/**
	 * FAQ Schema handler.
	 *
	 * @var ASFAQ_Schema
	 */
	public $schema;

	/**
	 * FAQ Admin handler.
	 *
	 * @var ASFAQ_Admin
	 */
	public $admin;

	/**
	 * Get singleton instance.
	 *
	 * @since  1.0.0
	 * @return Advanced_Smart_FAQ
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor. Initialize all components.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init_components();
		$this->register_hooks();
	}

	/**
	 * Initialize plugin components.
	 *
	 * @since 1.0.0
	 */
	private function init_components() {
		$this->post_type = new ASFAQ_Post_Type();
		$this->shortcode = new ASFAQ_Shortcode();
		$this->schema    = new ASFAQ_Schema();
		$this->admin     = new ASFAQ_Admin();
	}

	/**
	 * Register plugin-level hooks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Enqueue front-end CSS and JavaScript.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_assets() {
		wp_enqueue_style(
			'asfaq-style',
			ASFAQ_PLUGIN_URL . 'assets/css/faq-style.css',
			array(),
			ASFAQ_VERSION
		);

		$options = get_option( 'asfaq_settings', array() );

		wp_enqueue_script(
			'asfaq-script',
			ASFAQ_PLUGIN_URL . 'assets/js/faq-script.js',
			array(),
			ASFAQ_VERSION,
			true
		);

		wp_localize_script(
			'asfaq-script',
			'asfaqSettings',
			array(
				'animationSpeed' => isset( $options['animation_speed'] ) ? absint( $options['animation_speed'] ) : 300,
				'enableSearch'   => isset( $options['enable_search'] ) ? (bool) $options['enable_search'] : true,
			)
		);
	}

	/**
	 * Plugin activation callback.
	 *
	 * Registers post type and flushes rewrite rules so FAQ permalinks work immediately.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		$post_type = new ASFAQ_Post_Type();
		$post_type->register_post_type();
		$post_type->register_taxonomy();
		flush_rewrite_rules();

		// Set default options on first activation.
		if ( false === get_option( 'asfaq_settings' ) ) {
			update_option( 'asfaq_settings', array(
				'enable_schema'   => 1,
				'animation_speed' => 300,
				'default_limit'   => 10,
				'enable_search'   => 1,
			) );
		}
	}

	/**
	 * Plugin deactivation callback.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Prevent cloning of the singleton.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing of the singleton.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize a singleton.' );
	}
}

// Register activation & deactivation hooks.
register_activation_hook( __FILE__, array( 'Advanced_Smart_FAQ', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Advanced_Smart_FAQ', 'deactivate' ) );

/**
 * Returns the main plugin instance.
 *
 * @since  1.0.0
 * @return Advanced_Smart_FAQ
 */
function advanced_smart_faq() {
	return Advanced_Smart_FAQ::get_instance();
}

// Boot the plugin.
advanced_smart_faq();
