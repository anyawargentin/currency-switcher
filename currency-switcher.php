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
include_once plugin_dir_path(__FILE__) . 'includes/db/db.php';

/*
*   Setup tables on plugin activation
*/
register_activation_hook(__FILE__, 'currency_switcher_setup');
function currency_switcher_setup() { 
    setup_tables();
}

/*
*   Drop tables on plugin deactivation
*/
register_deactivation_hook(__FILE__, 'currency_switcher_cleanup');
function currency_switcher_cleanup() { 
    cleanup_tables();
}