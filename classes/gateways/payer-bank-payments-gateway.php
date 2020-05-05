<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Bank Payments Gateway.
 *
 * @class    Payer_Bank_Payments_Gateway
 * @package  Payer/Classes/Gateways
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Bank_Payments_Gateway extends Payer_Factory_Gateway {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->id                 = 'payer_bank_payment';
		$this->method_title       = __( 'Payer Bank', 'payer-for-woocommerce' );
		$this->method_description = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->test_mode   = $this->get_option( 'test_mode' );
		$this->debug_mode  = $this->get_option( 'debug_mode' );
		$this->icon_url    = $this->get_option( 'payer_bank_payment_icon' );
		$this->icon        = $this->set_icon();

		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();

		$support_array    = array(
			'products',
		);
		$payer_settings   = get_option( 'woocommerce_payer_card_payment_settings' );
		$order_management = $payer_settings['order_management'];
		if ( 'yes' === $order_management ) {
			array_push( $support_array, 'refunds' );
		}
		$this->supports = $support_array;

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_filter( 'woocommerce_page_wc-settings', array( $this, 'show_keys_in_settings' ) );
	}

	/**
	 * Shows the settings keys on the settings page.
	 *
	 * @return void
	 */
	public function show_keys_in_settings() {
		if ( isset( $_GET['section'] ) ) {
			if ( $this->id === $_GET['section'] ) {
				payer_show_credentials_form();
			}
		}
	}

	public function is_available() {
		if ( 'yes' !== $this->enabled ) {
			return false;
		}

		return true;
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_krokedil_payer_bank_gateway' );
/**
 * Registers the gateway.
 *
 * @param array $methods
 * @return array $methods
 */
function add_krokedil_payer_bank_gateway( $methods ) {
	if ( ! defined( 'UNSET_PAYER_BANK_PAYMENTS' ) ) {
		$methods[] = 'Payer_Bank_Payments_Gateway';
	}
	return $methods;
}
