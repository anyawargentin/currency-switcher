<?php
/**
 * Manage database connections.
 *
 * @package Currency_Switcher/Includes/Db
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup the currency tables.
 */
function setup_tables() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'currency_switcher';

	$sql = "
		CREATE TABLE IF NOT EXISTS $table_name (
			id int NOT NULL AUTO_INCREMENT,
			code varchar(255) NOT NULL,
			rate varchar(255),
			symbol TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
			symbol_placement varchar(255) DEFAULT 'left',
			updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
	";
	// WPCS: False positive, query is prepared.
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$wpdb->query( $sql );
}

/**
 * Drop the currency table.
 */
function cleanup_tables() {
	global $wpdb;
	$wpdb->query( "DROP TABLE {$wpdb->prefix}currency_switcher" );
}

/**
 * Fetch currencies from API & insert to table, then update rates.
 */
function setup_currencies() {
	global $wpdb;
	$currencies = fetch_api_currencies();

	if ( ! $currencies ) {
		return false;
	}

	foreach ( $currencies as $currency ) {

		$already_exists = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}currency_switcher WHERE code = %s", sanitize_text_field( $currency->code ) ) );

		if ( $already_exists ) {
			continue;
		}

		$symbol_placement = get_symbol_placement( $currency->code );
		$wpdb->insert(
			$wpdb->prefix . 'currency_switcher',
			array(
				'code'             => sanitize_text_field( $currency->code ),
				'symbol'           => sanitize_text_field( $currency->symbol_native ),
				'symbol_placement' => $symbol_placement,
			),
			array( '%s', '%s', '%s' )
		);
	}

	update_rates();
}

/**
 * Fetch current rates from API & update currency table.
 *
 * @param string|false $store_currency The store currency code.
 */
function update_rates( $store_currency = false ) {
	global $wpdb;
	if ( ! $store_currency ) {
		$store_currency = get_woocommerce_store_currency();
	}
	$currencies = fetch_api_rates( $store_currency );

	if ( ! $currencies ) {
		return;
	}

	foreach ( $currencies as $key => $value ) {

		$wpdb->update(
			$wpdb->prefix . 'currency_switcher',
			array( 'rate' => sanitize_text_field( $value ) ),
			array( 'code' => sanitize_text_field( $key ) ),
			array( '%s' ),
			array( '%s' )
		);
	}
}

/**
 * Get base currency.
 */
function get_base_currency() {
	global $wpdb;

	$base_currency_code = $wpdb->get_var( "SELECT code FROM {$wpdb->prefix}currency_switcher WHERE rate = '1'" );

	if ( $base_currency_code ) {
		return $base_currency_code;
	}

	return false;
}

/**
 * Get currency rate by currency code from database.
 *
 * @param string $code The currency code.
 * @return string|false The currency rate or false if not found.
 */
function get_currency_rate( $code ) {
	global $wpdb;

	$sql = $wpdb->prepare(
		"
		SELECT rate
		FROM {$wpdb->prefix}currency_switcher 
		WHERE code = %s
	",
		sanitize_text_field( $code )
	);
	// WPCS: False positive, query is prepared.
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_var( $sql );
}

/**
 * Get currency symbol by currency code from database.
 *
 * @param string $code The currency code.
 * @return string|false The currency symbol or false if not found.
 */
function get_currency_symbol( $code ) {
	global $wpdb;

	$sql = $wpdb->prepare(
		"
		SELECT symbol
		FROM {$wpdb->prefix}currency_switcher 
		WHERE code = %s
	",
		sanitize_text_field( $code )
	);
	// WPCS: False positive, query is prepared.
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_var( $sql );
}

/**
 * Get currency symbol placement by currency code from database.
 *
 * @param string $code The currency code.
 * @return string|false The symbol placement or false if not found.
 */
function get_currency_symbol_placement( $code ) {
	global $wpdb;

	$sql = $wpdb->prepare(
		"
		SELECT symbol_placement
		FROM {$wpdb->prefix}currency_switcher 
		WHERE code = %s
	",
		sanitize_text_field( $code )
	);
	// WPCS: False positive, query is prepared.
	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->get_var( $sql );
}

/**
 * Get all currencies.
 *
 * @return array The currencies.
 */
function get_currencies() {
	global $wpdb;
	return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}currency_switcher" );
}
