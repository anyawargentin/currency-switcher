<?php
/*
 * Get the currently set WC shop currency
 */
function get_woocommerce_store_currency() {
    global $wpdb;

    return $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'woocommerce_currency'");
}