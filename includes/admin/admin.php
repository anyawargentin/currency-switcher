<?php
/**
 * Add Currency features to the WordPress Admin.
 *
 * @package Currency_Switcher/Includes/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the currency overview page.
 */
function register_currency_overview_page() {
	add_menu_page(
		esc_html__( 'Currencies' ),
		esc_html__( 'Currencies' ),
		'manage_options',
		'currencies-overview',
		'currency_overview_content',
		'dashicons-money-alt',
		2
	);
}
add_action( 'admin_menu', 'register_currency_overview_page' );

/**
 * The currency overview content.
 */
function currency_overview_content() {
	global $woocommerce;
	$currencies = get_currencies();

	if ( $currencies ) { ?>
		<form name="currency-overview" id="currency-overview">
			<div class="fullwidth">
				<h1><?php echo esc_html__( 'Currencies Overview' ); ?></h1>
				<p><?php echo esc_html__( 'Currencies are automatically updated every hour.' ); ?> <?php echo esc_html__( 'Last update was run ' ); ?> <strong id="updated-timestamp"><?php echo esc_html( $currencies[0]->updated ); ?></strong>.</p>
				<p><?php echo esc_html__( 'The set base currency is ' ) . '<strong id="base-currency">' . esc_html( get_woocommerce_store_currency() ) . '</strong>.'; ?></p> 
			</div>
			<?php
			foreach ( $currencies as $currency ) {

				if ( '1' === $currency->rate ) {
					continue;
				}

				$currency_id = 'currency-' . strtolower( $currency->code );
				?>
				<div class="currency">
					<label for="<?php echo esc_attr( $currency_id ); ?>"><?php echo esc_html( $currency->code ); ?></label>
					<input disabled id="<?php echo esc_attr( $currency_id ); ?>" name="<?php echo esc_attr( $currency_id ); ?>" type="text" value="<?php echo esc_attr( $currency->rate ); ?>"/>
				</div>
				<?php
			}
			?>

			<div class="fullwidth">
				<div class="spin-loader"></div>
				<input class="btn" type="submit" name="update-currencies" id="update-currencies" value="<?php echo esc_html__( 'Update currencies' ); ?>"/>
			</div>
		</form>
		<?php
	}
}
