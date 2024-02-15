<?php
/**
 * AJAX functions.
 *
 * @package Currency_Switcher/Includes/Ajax
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update currency rates via AJAX.
 */
function ajax_update_currencies() {
	if ( isset( $_POST['nonce'] ) ) {
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'currency_switcher_admin_nonce' ) ) {
			die( 'Invalid nonce' );
		}
	}

	if ( isset( $_POST['update'] ) ) {
		update_rates();
		$currencies    = get_currencies();
		$currency_data = '';

		if ( ! $currencies ) {
			wp_send_json_error();
		}

		foreach ( $currencies as $currency ) {
			$rate = ( '1' === $currency->rate ) ? 'base' : $currency->rate;
			$currency_data .= $currency->code . '&' . $rate . '&' . $currency->updated . '||';
		}

		wp_send_json_success( rtrim( $currency_data, '||' ) );
	}

	wp_send_json_error();
}
add_action( 'wp_ajax_update_currencies', 'ajax_update_currencies' );
add_action( 'wp_ajax_nopriv_update_currencies', 'ajax_update_currencies' );

/**
 * Update customer currency via AJAX.
 */
function ajax_update_customer_currency() {
	if ( isset( $_POST['nonce'] ) ) {
		$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'currency_switcher_nonce' ) ) {
			die( 'Invalid nonce' );
		}
	}

	if ( isset( $_POST['code'] ) ) {
		set_customer_currency( sanitize_text_field( wp_unslash( $_POST['code'] ) ) );
	}

	wp_die();
}
add_action( 'wp_ajax_update_customer_currency', 'ajax_update_customer_currency' );
add_action( 'wp_ajax_nopriv_update_customer_currency', 'ajax_update_customer_currency' );
