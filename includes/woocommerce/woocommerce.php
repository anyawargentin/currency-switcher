<?php

/*
 * Convert price to set currency
 */
add_filter('woocommerce_product_get_price', 'set_currency_price', 99, 2);
add_filter('woocommerce_product_get_regular_price', 'set_currency_price', 99, 2);
add_filter('woocommerce_product_variation_get_regular_price', 'set_currency_price', 99, 2);
add_filter('woocommerce_product_variation_get_price', 'set_currency_price', 99, 2);
function set_currency_price($price, $product) {
    $converted_price = convert_price($price);
    return (float) $converted_price;
}

/*
 * Convert price ranges to set currency
 */
add_filter('woocommerce_get_price_html', 'custom_price_range', 10, 2);
function custom_price_range($price, $product) {

    if ($product->is_type('variable')) {
        $variation_prices = array();
        foreach ($product->get_available_variations() as $variation) {
            $variation_prices[] = $variation['display_price'];
        }

        sort($variation_prices, SORT_NUMERIC);
        $lowest_price = reset($variation_prices);
        $highest_price = end($variation_prices);

        $price = wc_price($lowest_price) . ' - ' . wc_price($highest_price);
    }

    return $price;
}

/*
 * Set custom currency symbol
 */
add_filter('woocommerce_currency_symbol', 'set_customer_currency_symbol', 10, 2);
function set_customer_currency_symbol($currency_symbol, $currency) {

    // To avoid converting currency symbols in the WooCommerce settings
    if(!is_admin()) {
        $code = get_customer_currency();

        if($code)  {
            $custom_symbol = get_currency_symbol($code);
            if($custom_symbol) $currency_symbol = $custom_symbol;
        } 
    }

    return $currency_symbol;
}

/*
 * Print set order currency in order data
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'print_order_currency' );
function print_order_currency($order) {
    echo '<div><p><strong>' . __('Order Currency') . ':</strong> ' . $order->get_currency() . '</p>';
}

/*
 * Get the currently set WC shop currency
 */
function get_woocommerce_store_currency() {
    global $wpdb;

    return $wpdb->get_var("SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = 'woocommerce_currency'");
}

/*
 * If custom currency is set, change order currency to this
 */
 add_action('woocommerce_thankyou', 'set_custom_order_currency', 10, 1);
 function set_custom_order_currency($order_id) {
    $customer_code = get_customer_currency();

    if($customer_code) {
        $order = wc_get_order($order_id); 
        $order->set_currency($customer_code);
        $order->save();
    }
 }

 /*
 * Change the position of custom currency symbols
 */
 add_filter('woocommerce_price_format', 'custom_currency_symbol_position', 10, 2);
 function custom_currency_symbol_position($format, $currency_pos) {
    $customer_code = get_customer_currency();

    if($customer_code) {
        $currency_symbol_placement = get_currency_symbol_placement($customer_code);

        if($currency_symbol_placement == 'space-right') return '%2$s %1$s';

        if($currency_symbol_placement == 'right') return '%2$s%1$s';

        if($currency_symbol_placement == 'left') return '%1$s%2$s';
    }

    return $format;
}