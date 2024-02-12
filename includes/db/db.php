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
*   Get all currencies
*/
function get_currencies() {
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM {$wpdb->prefix}currency_switcher");
}