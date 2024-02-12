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
include_once plugin_dir_path(__FILE__) . 'includes/general/general.php';
include_once plugin_dir_path(__FILE__) . 'includes/db/db.php';
include_once plugin_dir_path(__FILE__) . 'includes/api/api.php';
include_once plugin_dir_path(__FILE__) . 'includes/woocommerce/woocommerce.php';

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

