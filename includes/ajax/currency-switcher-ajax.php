<?php
add_action('wp_ajax_update_currencies', 'ajax_update_currencies');
add_action('wp_ajax_nopriv_update_currencies', 'ajax_update_currencies');
function ajax_update_currencies() {

    if(isset($_POST['update'])) {
        update_rates();
        $currencies = get_currencies();
        $currency_data = '';

        if(!$currencies) return;
        
        foreach($currencies as $currency) {
            $currency->rate === '1' ? $rate = 'base' : $rate = $currency->rate;
            $currency_data .= $currency->code . '&' . $rate . '&' . $currency->updated . '||';
        }

        echo substr($currency_data, 0, -2);
    }

    die();
}

add_action('wp_ajax_update_customer_currency', 'ajax_update_customer_currency');
add_action('wp_ajax_nopriv_update_customer_currency', 'ajax_update_customer_currency');
function ajax_update_customer_currency() {

    if(isset($_POST['code'])) {
        set_customer_currency($_POST['code']);
    }
    die();
}