<?php // phpcs:ignore
/**
 * Plugin Name: Currency Switcher for WooCommerce
 * Plugin URI: https://github.com/anyawargentin/currency-switcher/
 * Description: Enables currency switching for WooCommerce.
 * Author: Anya Wargentin
 * Author URI: https://github.com/anyawargentin/
 * Version: 1.0.0
 *
 * @package Currency_Switcher
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include needed files.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/admin/admin.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/general/general.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ajax/currency-switcher-ajax.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/woocommerce/woocommerce.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/api/api.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/db/db.php';

/**
 * Register scripts.
 *
 * @return void
 */
function register_scripts() {
	wp_register_script( 'currency-switcher-script', plugin_dir_url( __FILE__ ) . 'js/currency-switcher.js', array( 'jquery' ), '1.0', true );

	wp_localize_script(
		'currency-switcher-script',
		'currencySwitcherSettings',
		array(
			'nonce'   => wp_create_nonce( 'currency_switcher_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		)
	);

	wp_enqueue_script( 'currency-switcher-script' );
}
add_action( 'wp_enqueue_scripts', 'register_scripts' );

/**
 * Registers styles.
 *
 * @return void
 */
function register_styles() {
	$style_path = plugin_dir_path( __FILE__ ) . 'css/currency-switcher.css';
	$version    = filemtime( $style_path );

	wp_register_style( 'currency-switcher-style', plugin_dir_url( __FILE__ ) . 'css/currency-switcher.css', array(), $version );
	wp_enqueue_style( 'currency-switcher-style' );
}
add_action( 'wp_enqueue_scripts', 'register_styles' );

/**
 * Registers admin styles.
 *
 * @return void
 */
function admin_register_styles() {
	$style_path = plugin_dir_path( __FILE__ ) . 'css/currency-switcher-admin.css';
	$version    = filemtime( $style_path );

	wp_register_style( 'currency-switcher-style-admin', plugin_dir_url( __FILE__ ) . 'css/currency-switcher-admin.css', array(), $version );
	wp_enqueue_style( 'currency-switcher-style-admin' );
}
add_action( 'admin_enqueue_scripts', 'admin_register_styles' );

/**
 * Registers admin scripts.
 *
 * @return void
 */
function admin_register_scripts() {
	wp_register_script( 'currency-switcher-script-admin', plugin_dir_url( __FILE__ ) . 'js/currency-switcher-admin.js', array( 'jquery' ), '1.0', true );

	wp_localize_script(
		'currency-switcher-script-admin',
		'currencySwitcherSettingsAdmin',
		array(
			'nonce'   => wp_create_nonce( 'currency_switcher_admin_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		)
	);

	wp_enqueue_script( 'currency-switcher-script-admin' );
}
add_action( 'admin_enqueue_scripts', 'admin_register_scripts' );

/**
 * Set up tables & currencies & schedule background sync on plugin activation.
 *
 * @return void
 */
function currency_switcher_setup() {
	setup_tables();
	setup_currencies();

	if ( ! wp_next_scheduled( 'hourly_currency_sync' ) ) {
		wp_schedule_event( time(), 'hourly', 'hourly_currency_sync' );
	}
}
register_activation_hook( __FILE__, 'currency_switcher_setup' );

// Add hourly update of currency rates.
add_action( 'hourly_currency_sync', 'update_rates' );

/**
 * Cleans up tables and scheduled events on plugin deactivation.
 *
 * @return void
 */
function currency_switcher_cleanup() {
	cleanup_tables();
	wp_clear_scheduled_hook( 'hourly_currency_sync' );
}
register_deactivation_hook( __FILE__, 'currency_switcher_cleanup' );
