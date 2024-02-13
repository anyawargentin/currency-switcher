<?php
/*
*   Start session if not already started & check store currency
*/
function start_session() {
    if(!session_id()) {
        session_start();
    }

    check_store_currency();
}
add_action('init', 'start_session');

/*
* If store currency has been changed, update rates to use the new store currency as base currency
*/
function check_store_currency() {
    $store_currency = get_woocommerce_store_currency();

    if(isset($_SESSION['store_currency']) && $store_currency !== $_SESSION['store_currency']) {
        update_rates($new_currency); 
    }

    $_SESSION['store_currency'] = $store_currency;
}

/*
* Decide the correct symbol placement for the currency
*/
function get_symbol_placement($currency_code) {

    switch ($currency_code) {
        case 'AED':
            return 'right';
        case 'QAR':
            return 'right';
        case 'OMR':
            return 'right';
        case 'SAR':
            return 'right';
        case 'SEK':
            return 'space-right';
        case 'NOK':
            return 'space-right';
        case 'DKK':
            return 'space-right';
        default:
            return 'left';
    }
}

/*
* Print currency switcher to DOM
*/
add_action('wp_body_open', 'print_currency_switcher');
function print_currency_switcher() { 
    $currencies = get_currencies();
    $customer_code = get_customer_currency(); 
    $customer_currency = false; 
    $base_currency = false; ?>

    <id id="currency-switcher" name="currency-switcher">
        <p><?php echo __('Switch Currency'); ?></p> 
        <ul id="currency-list">
            <?php
            foreach($currencies as $currency) { 

                if($currency->rate === '1') $base_currency = $currency;

                if($customer_code === $currency->code) $customer_currency = $currency; ?>

                <li data-code="<?php echo $currency->code; ?>" class="currency-item"><span class="currency-symbol"><?php echo $currency->symbol; ?></span><?php echo $currency->code; ?></li>

            <?php
            } ?>
        </ul>
        <?php
        if(!$customer_currency) $customer_currency = $base_currency; ?>
    </id>
    <div id="currency-overlay"></div>
    <div id="loading-currency"><div class="spin-loader"></div><p><?php echo __('Loading new currency...'); ?></p></div>
    <div id="active-customer-currency"><span class="currency-symbol"><?php echo $customer_currency->symbol; ?></span><span class="currency-code"><?php echo $customer_currency->code; ?></span></div>
<?php
}

/*
* Get customer currency session
*/
function get_customer_currency() {
    if(isset($_SESSION['customer_currency'])) return $_SESSION['customer_currency'];
    return false;
}

/*
* Set customer currency session
*/
function set_customer_currency($code) {
    if($code && $code !== '') $_SESSION['customer_currency'] = sanitize_text_field($code);
}
