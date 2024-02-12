<?php
/*
*   Setup the currency table
*/
function setup_tables() {
    global $wpdb;

    $sql = "
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}currency_switcher(
            id int NOT NULL AUTO_INCREMENT,
            code varchar(255) NOT NULL,
            rate varchar(255),
            symbol TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
            symbol_placement varchar(255) DEFAULT 'left',
            updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $wpdb->query($sql);
}

/*
*   Drop the currency table
*/
function cleanup_tables() {
    global $wpdb;
    $wpdb->query("DROP TABLE {$wpdb->prefix}currency_switcher");
}

/*
*   Fetch currencies from API & insert to table, then update rates
*/
function setup_currencies() {
    global $wpdb;
    $currencies = fetch_api_currencies();

    if(!$currencies) {
        return false;
    }

    foreach($currencies as $currency) {

        $already_exists = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}currency_switcher WHERE code = %s", sanitize_text_field($currency->code)));

        if($already_exists) continue;

        $symbol_placement = get_symbol_placement($currency->code);
        $wpdb->insert(
            $wpdb->prefix . 'currency_switcher',
            array(
                'code'              => sanitize_text_field($currency->code),
                'symbol'            => sanitize_text_field($currency->symbol),
                'symbol_placement'  => $symbol_placement,
            ),
            array('%s', '%s')
        );
    }

    update_rates();
}

/*
*   Fetch current rates from API & update currency table
*/
function update_rates($store_currency = false) {
    global $wpdb;
    if(!$store_currency) $store_currency = get_woocommerce_store_currency();
    $currencies = fetch_api_rates($store_currency);

    if(!$currencies) return;

    foreach($currencies as $key => $value) {

        $wpdb->update(
            $wpdb->prefix . 'currency_switcher',
            array('rate' => sanitize_text_field($value)),
            array('code' => sanitize_text_field($key)),
            array('%s'),
            array('%s')
        );
    }
}

/*
*   Get all currencies
*/
function get_currencies() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}currency_switcher");
}