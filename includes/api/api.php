<?php
/**
 * Fetch currencies & rates from freecurrencyapi.com.
 *
 * @package Currency_Switcher/Includes/Api
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch API rates from freecurrencyapi.com.
 *
 * @param string $store_currency The base currency code.
 * @return array|bool The fetched rates or false on failure.
 */
function fetch_api_rates( $store_currency ) {
	$url     = 'https://api.freecurrencyapi.com/v1/latest?base_currency=' . sanitize_text_field( $store_currency );
	$headers = array(
		'apikey: fca_live_KzQIgTz80MmaxgJyPbVKgGfilTZXDxxAtShcYvxs',
	);
	$curl    = curl_init( $url );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
	// For testing purposes, remove in production.
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	// For testing purposes, remove in production.
	curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
	$response = curl_exec( $curl );
	curl_close( $curl );

	if ( ! $response ) {
		return false;
	}
	$rates = json_decode( $response )->data;

	return $rates;
}

/**
 * Fetch API currencies from freecurrencyapi.com.
 *
 * @return array|bool The fetched currencies or false on failure.
 */
function fetch_api_currencies() {
	$url     = 'https://api.freecurrencyapi.com/v1/currencies';
	$headers = array(
		'apikey: fca_live_KzQIgTz80MmaxgJyPbVKgGfilTZXDxxAtShcYvxs',
	);
	$curl    = curl_init( $url );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
	// For testing purposes, remove in production.
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
	// For testing purposes, remove in production.
	curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, false );
	$response = curl_exec( $curl );
	curl_close( $curl );

	if ( ! $response ) {
		return false;
	}
	$currencies = json_decode( $response )->data;

	return $currencies;
}
