<?php
/**
 * General functions.
 *
 * @package Currency_Switcher/Includes/General
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get customer currency session.
 *
 * @return string|false The customer currency code or false if not set.
 */
function get_customer_currency() {
	if ( isset( $_SESSION['customer_currency'] ) ) {
		return sanitize_text_field( $_SESSION['customer_currency'] );
	}
	return false;
}

/**
 * If set, convert price to custom customer currency.
 *
 * @param float $price The original price.
 * @return float The converted price.
 */
function convert_price( $price ) {
	$customer_code = get_customer_currency();

	if ( $customer_code && $price > 0 ) {
		$customer_rate = get_currency_rate( $customer_code );
		if ( $customer_rate && '' !== $customer_rate ) {
			$price = $price * floatval( $customer_rate );
		}
	}

	return $price;
}

/**
 * Print currency switcher to DOM.
 */
function print_currency_switcher() {
	$currencies        = get_currencies();
	$customer_code     = get_customer_currency();
	$customer_currency = false;
	$base_currency     = false;
	?>

	<div id="currency-switcher" name="currency-switcher">
		<p><?php echo esc_html__( 'Switch Currency' ); ?></p>
		<ul id="currency-list">
			<?php
			foreach ( $currencies as $currency ) {

				if ( '1' === $currency->rate ) {
					$base_currency = $currency;
				}

				if ( $customer_code === $currency->code ) {
					$customer_currency = $currency;
				}
				?>

				<li data-code="<?php echo esc_attr( $currency->code ); ?>" class="currency-item"><span
							class="currency-symbol"><?php echo esc_html( $currency->symbol ); ?></span><?php echo esc_html( $currency->code ); ?></li>

				<?php
			}
			?>
		</ul>
		<?php
		if ( ! $customer_currency ) {
			$customer_currency = $base_currency;
		}
		?>
	</div>
	<div id="currency-overlay"></div>
	<div id="loading-currency">
		<div class="spin-loader"></div>
		<p><?php echo esc_html__( 'Loading new currency...' ); ?></p>
	</div>
	<div id="active-customer-currency"><span
				class="currency-symbol"><?php echo esc_html( $customer_currency->symbol ); ?></span><span
				class="currency-code"><?php echo esc_html( $customer_currency->code ); ?></span></div>
	<?php
}
add_action( 'wp_body_open', 'print_currency_switcher' );

/**
 * Start session if not already started & check store currency.
 */
function start_session() {
	if ( ! session_id() ) {
		session_start();
	}

	check_store_currency();
}
add_action( 'init', 'start_session' );

/**
 * If store currency has been changed, update rates to use the new store currency as the base currency.
 */
function check_store_currency() {
	$store_currency = get_woocommerce_store_currency();

	if ( isset( $_SESSION['store_currency'] ) && $store_currency !== $_SESSION['store_currency'] ) {
		update_rates( $new_currency );
	}

	$_SESSION['store_currency'] = $store_currency;
}

/**
 * Set customer currency session.
 *
 * @param string $code The currency code.
 */
function set_customer_currency( $code ) {
	if ( $code && '' !== $code ) {
		$_SESSION['customer_currency'] = sanitize_text_field( $code );
	}
}

/**
 * Decide the correct symbol placement for the currency.
 *
 * @param string $currency_code The currency code.
 * @return string The symbol placement.
 */
function get_symbol_placement( $currency_code ) {

	switch ( $currency_code ) {
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
