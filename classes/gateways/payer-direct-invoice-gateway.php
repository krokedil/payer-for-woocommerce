<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Direct_Invoice_Gateway extends Payer_Factory_Gateway {
    public function __construct() {
		parent::__construct();

		$this->id                   = 'payer_direct_invoice_gateway';
		$this->method_title         = __( 'Payer Direct Invoice', 'payer-for-woocommerce' );
		$this->method_description   = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       		    = $this->get_option( 'title' );
		$this->description 		    = $this->get_option( 'description' );
		$this->test_mode            = $this->get_option( 'test_mode' );
		$this->debug_mode           = $this->get_option( 'debug_mode' );
		$this->icon_url				= $this->get_option( 'payer_direct_invoice_payment_icon' );
		$this->icon					= $this->set_icon();

		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_filter( 'woocommerce_page_wc-settings', array( $this, 'show_keys_in_settings' ) );				
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		update_post_meta( $order_id, '_billing_pno', $_POST['billing_pno'] );		

		// Check if customer changed any of the data from get_address
		$this->check_posted_data( $order_id );

		// Create an order
		Payer_Create_Order::create_order( $order_id );

		$order->payment_complete();

		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_order_received_url(),
		);
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_payer_direct_invoice_gateway' );

function add_payer_direct_invoice_gateway( $methods ) {
	if ( ! defined( 'UNSET_PAYER_DIRECT_INVOICE_PAYMENTS' ) ) {		
		$methods[] = 'Payer_Direct_Invoice_Gateway';
	}
	return $methods;
}
