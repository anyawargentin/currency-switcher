<?php
/**
 * WooCommerce price modifications.
 *
 * @package Currency_Switcher/Includes/Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert the product price to the set custom currency.
 *
 * @param float  $price The original price.
 * @param object $product The WooCommerce product object.
 * @return float The converted price.
 */
function set_customer_currency_price( $price, $product ) {
	$converted_price = convert_price( $price );
	return (float) $converted_price;
}

// Convert the sales price to custom currency.
add_filter( 'woocommerce_product_get_price', 'set_customer_currency_price', 99, 2 );
// Convert the regular price to custom currency.
add_filter( 'woocommerce_product_get_regular_price', 'set_customer_currency_price', 99, 2 );
// Convert the sales variation price to custom currency.
add_filter( 'woocommerce_product_variation_get_price', 'set_customer_currency_price', 99, 2 );
// Convert the regular variation price to custom currency.
add_filter( 'woocommerce_product_variation_get_regular_price', 'set_customer_currency_price', 99, 2 );

/**
 * Customizes the price range display for variable products.
 *
 * @param string $price The original price.
 * @param object $product The WooCommerce product object.
 * @return string The modified price range.
 */
function set_customer_currency_price_range( $price, $product ) {
	if ( $product->is_type( 'variable' ) ) {
		$variation_prices = array();
		foreach ( $product->get_available_variations() as $variation ) {
			$variation_prices[] = $variation['display_price'];
		}

		sort( $variation_prices, SORT_NUMERIC );
		$lowest_price  = reset( $variation_prices );
		$highest_price = end( $variation_prices );

		$price = wc_price( $lowest_price ) . ' - ' . wc_price( $highest_price );
	}

	return $price;
}
add_filter( 'woocommerce_get_price_html', 'set_customer_currency_price_range', 10, 2 );

/**
 * Sets the custom currency symbol.
 *
 * @param string $currency_symbol The default currency symbol.
 * @param string $currency        The currency code.
 * @return string The modified currency symbol.
 */
function set_customer_currency_symbol( $currency_symbol, $currency ) {
	// Avoid converting currency symbols in the WooCommerce settings.
	if ( ! is_admin() ) {
		$code = get_customer_currency();

		if ( $code ) {
			$custom_symbol = get_currency_symbol( $code );
			if ( $custom_symbol ) {
				$currency_symbol = $custom_symbol;
			}
		}
	}

	return $currency_symbol;
}
add_filter( 'woocommerce_currency_symbol', 'set_customer_currency_symbol', 10, 2 );

/**
 * Prints the set order currency in the order data.
 *
 * @param object $order The WooCommerce order object.
 * @return void
 */
function print_order_currency( $order ) {
	echo '<div><p><strong>' . esc_html( __( 'Order Currency' ) ) . ':</strong> ' . esc_html( $order->get_currency() ) . '</p>';
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'print_order_currency' );

/**
 * Get the currently set WooCommerce shop currency.
 *
 * @return string The currency code.
 */
function get_woocommerce_store_currency() {
	global $wpdb;

	$store_currency = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT option_value 
			FROM {$wpdb->prefix}options 
			WHERE option_name = %s",
			'woocommerce_currency'
		)
	);

	return $store_currency;
}

/**
 * Sets the custom order currency if a custom currency is chosen.
 *
 * @param int $order_id The order ID.
 * @return void
 */
function set_custom_order_currency( $order_id ) {
	$customer_code = get_customer_currency();

	if ( $customer_code ) {
		$order = wc_get_order( $order_id );
		$order->set_currency( $customer_code );
		$order->save();
	}
}
add_action( 'woocommerce_thankyou', 'set_custom_order_currency', 10, 1 );

/**
 * Changes the position of custom currency symbols.
 *
 * @param string $format The original price format.
 * @return string The modified price format.
 */
function custom_currency_symbol_position( $format ) {
	$customer_code = get_customer_currency();

	if ( $customer_code ) {
		$currency_symbol_placement = get_currency_symbol_placement( $customer_code );

		if ( 'space-right' === $currency_symbol_placement ) {
			return '%2$s %1$s';
		}

		if ( 'right' === $currency_symbol_placement ) {
			return '%2$s%1$s';
		}

		if ( 'left' === $currency_symbol_placement ) {
			return '%1$s%2$s';
		}
	}

	return $format;
}
add_filter( 'woocommerce_price_format', 'custom_currency_symbol_position', 10, 2 );
