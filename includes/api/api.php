<?php
/**
* Fetch rates from freecurrencyapi.com
*/
function fetch_api_rates($store_currency) {
    $url = 'https://api.freecurrencyapi.com/v1/latest?base_currency=' . sanitize_text_field($store_currency);
    $headers = array(
        'apikey: fca_live_GRI7a71dGGdjfNUwgdTBaXJCrxdwlc2yKp3IF6SP'
    );
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // For testing purposes, remove in production  - -
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // - - - - - - - - - - - - - - - - - - - - - - - -
    $response = curl_exec($curl);
    curl_close($curl);

    if(!$response) {
        return false;
    }
    $rates = json_decode($response)->data;

    return $rates;
}

/**
* Fetch currencies from freecurrencyapi.com
*/
function fetch_api_currencies() {
    $url = 'https://api.freecurrencyapi.com/v1/currencies';
    $headers = array(
        'apikey: fca_live_GRI7a71dGGdjfNUwgdTBaXJCrxdwlc2yKp3IF6SP'
    );
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    // For testing purposes, remove in production  - -
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    // - - - - - - - - - - - - - - - - - - - - - - - -
    $response = curl_exec($curl);
    curl_close($curl);

    if(!$response) {
        return false;
    }
    $currencies = json_decode($response)->data;
    return $currencies;
}