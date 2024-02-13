<?php
/*
* Register the currency overview page
*/
add_action('admin_menu', 'register_currency_overview_page');
function register_currency_overview_page() {
	add_menu_page( 
		__('Currencies'),
		__('Currencies'),
		'manage_options',
		'currencies-overview',
		'currency_overview_content',
		'dashicons-money-alt',
        2
	);
}

/*
* The currency overview content
*/
function currency_overview_content() {
    global $woocommerce;
    $currencies = get_currencies();
    
    if($currencies) { ?>
        <form name="currency-overview" id="currency-overview">
            <div class="fullwidth">
                <h1>Currencies Overview</h1>
                <p><?php echo __('Currencies are automatically updated every hour.'); ?> <?php echo __('Last update was run ') ?> <strong id="updated-timestamp"><?php echo $currencies[0]->updated; ?></strong>.</p>
                <p><?php echo __('The set base currency is ') . '<strong id="base-currency">' . get_woocommerce_store_currency() . '</strong>.'; ?></p> 
            </div>
            <?php
            foreach($currencies as $currency) { 

                if($currency->rate === '1') continue;

                $currency_id = 'currency-' . strtolower($currency->code); ?>
                <div class="currency">
                    <label for="<?php echo $currency_id; ?>"><?php echo $currency->code ?></label>
                    <input disabled id="<?php echo $currency_id; ?>" name="<?php echo $currency_id; ?>" type="text" value="<?php echo $currency->rate; ?>"/>
                </div>
                
            <?php
            } ?>

            <div class="fullwidth"><input class="btn" type="submit" name="update-currencies" id="update-currencies" value="<?php echo __('Update currencies'); ?>"/></div>
        </form>
    <?php
    }
}