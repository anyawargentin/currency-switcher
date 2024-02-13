<?php
/*
* @package Currency Switcher
* @version 1.0.0
*/
/*
Plugin Name: Currency Switcher
Plugin URI: 
Description: Just another currency switcher for WooCommerce.
Author: Anya Wargentin
Version: 1.0.0
Author URI: 
*/

/*
*   Include files
*/
include_once plugin_dir_path(__FILE__) . 'includes/admin/admin.php';
include_once plugin_dir_path(__FILE__) . 'includes/general/general.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax/currency-switcher-ajax.php';
include_once plugin_dir_path(__FILE__) . 'includes/woocommerce/woocommerce.php';
include_once plugin_dir_path(__FILE__) . 'includes/api/api.php';
include_once plugin_dir_path(__FILE__) . 'includes/db/db.php';

/*
*   Enqueue scripts & styles
*/
add_action('wp_enqueue_scripts', 'register_scripts');
function register_scripts() {
    wp_register_script('currency-switcher', plugin_dir_url( __FILE__ ) . 'js/currency-switcher.js', array('jquery'));
    wp_enqueue_script('currency-switcher');
}

add_action('wp_enqueue_scripts', 'register_styles');
function register_styles() {
    wp_register_style('currency-switcher', plugin_dir_url( __FILE__ ) . 'css/currency-switcher.css');
    wp_enqueue_style('currency-switcher');
}

/*
*   Enqueue admin scripts & styles
*/
add_action('admin_enqueue_scripts', 'admin_register_styles');
function admin_register_styles() {
    wp_register_style('currency-switcher-admin', plugin_dir_url( __FILE__ ) . 'css/currency-switcher-admin.css');
    wp_enqueue_style('currency-switcher-admin');
}

add_action('admin_enqueue_scripts', 'admin_register_scripts');
function admin_register_scripts() {
    wp_register_script('currency-switcher-admin', plugin_dir_url( __FILE__ ) . 'js/currency-switcher-admin.js', array('jquery'));
    wp_enqueue_script('currency-switcher-admin');
}

/*
*   Setup tables & currencies, schedule background sync on plugin activation
*/
register_activation_hook(__FILE__, 'currency_switcher_setup');
function currency_switcher_setup() { 
    setup_tables();
    setup_currencies();

    if(!wp_next_scheduled('hourly_currency_sync')) {
        wp_schedule_event(time(), 'hourly', 'hourly_currency_sync');
    }
}

add_action('hourly_currency_sync', 'update_rates');

/*
*   Drop tables & schedules events on plugin deactivation
*/
register_deactivation_hook(__FILE__, 'currency_switcher_cleanup');
function currency_switcher_cleanup() { 
    cleanup_tables();
    wp_clear_scheduled_hook('hourly_currency_sync');
}

