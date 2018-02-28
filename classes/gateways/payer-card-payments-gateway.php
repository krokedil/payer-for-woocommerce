<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Card_Payments_Gateway extends Payer_Factory_Gateway {
	public function __construct() {
		parent::__construct();

		$this->id                   = 'payer_card_payment';
		$this->method_title         = __( 'Payer Card', 'payer-for-woocommerce' );
		$this->method_description   = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       		    = $this->get_option( 'title' );
		$this->description 		    = $this->get_option( 'description' );
		$this->payer_agent_id       = $this->get_option( 'payer_agent_id' );
		$this->payer_password       = $this->get_option( 'payer_password' );
		$this->test_mode            = $this->get_option( 'test_mode' );
		$this->debug_mode           = $this->get_option( 'debug_mode' );
		$this->icon_url				= $this->get_option( 'payer_card_payment_icon' );	
		$this->icon					= $this->set_icon();				
		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();
		
		$support_array = array(
			'products'
		);
        $payer_settings = get_option( 'woocommerce_payer_card_payment_settings' );
        $order_management = $payer_settings['order_management'];
        if( 'yes' === $order_management ) {
			array_push( $support_array, 'refunds' );
		}
		$this->supports = $support_array;


		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_payer_card_gateway' );

function add_payer_card_gateway( $methods ) {
	$methods[] = 'Payer_Card_Payments_Gateway';

	return $methods;
}
