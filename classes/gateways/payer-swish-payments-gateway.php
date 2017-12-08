<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Swish_Payments_Gateway extends Payer_Factory_Gateway {
	public function __construct() {
		parent::__construct();

		$this->id                   = 'payer_swish_payment';
		$this->method_title         = __( 'Payer Swish Payments', 'payer-for-woocommerce' );
		$this->method_description   = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       		    = $this->get_option( 'title' );
		$this->description 		    = $this->get_option( 'description' );
		$this->payer_agent_id       = $this->get_option( 'payer_agent_id' );
		$this->payer_password       = $this->get_option( 'payer_password' );
		$this->test_mode            = $this->get_option( 'test_mode' );
		$this->debug_mode           = $this->get_option( 'debug_mode' );

		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_payer_swish_gateway' );

function add_payer_swish_gateway( $methods ) {
	$methods[] = 'Payer_Swish_Payments_Gateway';

	return $methods;
}