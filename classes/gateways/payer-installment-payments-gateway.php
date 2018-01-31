<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Installment_Payments_Gateway extends Payer_Factory_Gateway {
	public function __construct() {
		parent::__construct();

		$this->id                   = 'payer_installment_payment';
		$this->method_title         = __( 'Payer Installment', 'payer-for-woocommerce' );
		$this->method_description   = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       		    = $this->get_option( 'title' );
		$this->description 		    = $this->get_option( 'description' );
		$this->test_mode            = $this->get_option( 'test_mode' );
		$this->debug_mode           = $this->get_option( 'debug_mode' );
		$this->icon_url				= $this->get_option( 'payer_installment_payment_icon' );		
		$this->icon					= $this->set_icon();

		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_filter( 'woocommerce_page_wc-settings', array( $this, 'show_keys_in_settings' ) );				
	}

	public function show_keys_in_settings() {
		if ( isset( $_GET['section'] ) ) {
			if ( $this->id === $_GET['section'] ) {
				payer_show_credentials_form();
			}
		}
	}

	public function is_available() {
		
		switch ( get_woocommerce_currency() ) {
			case 'SEK' :
				$amount_limit = 999;
				break;
			case 'NOK' :
				$amount_limit = 999;
				break;
			case 'EUR' :
				$amount_limit = 99;
				break;
			default :
				return false;
		}

		if ( $amount_limit < $this->get_order_total() ) {
			return true;
		}

		return false;
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_payer_installment_gateway' );

function add_payer_installment_gateway( $methods ) {
	if ( ! defined( 'UNSET_PAYER_INSTALLMENT_PAYMENTS' ) ) {		
		$methods[] = 'Payer_Installment_Payments_Gateway';
	}
	return $methods;
}