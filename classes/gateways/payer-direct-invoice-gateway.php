<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Direct_Invoice_Gateway extends Payer_Factory_Gateway {
    public function __construct() {
		parent::__construct();

		$this->id                   = 'payer_direct_invoice_gateway';
		$this->method_title         = __( 'Payer Direct Invoice Payments', 'payer-for-woocommerce' );
		$this->method_description   = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       		    = $this->get_option( 'title' );
		$this->description 		    = $this->get_option( 'description' );
		$this->test_mode            = $this->get_option( 'test_mode' );
		$this->debug_mode           = $this->get_option( 'debug_mode' );

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

		// Create an order
		Payer_Create_Order::create_order( $order_id );
		// Commit an order to get invoice_number to save as post meta to order.
		Payer_Commit_Order::commit_order( $order_id );

		$order->payment_complete();

		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_order_received_url(),
		);
	}

	public function show_keys_in_settings() {
		if ( isset( $_GET['section'] ) ) {
			if ( $this->id === $_GET['section'] ) {
				payer_show_credentials_form();
			}
		}
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_payer_direct_invoice_gateway' );

function add_payer_direct_invoice_gateway( $methods ) {
	$methods[] = 'Payer_Direct_Invoice_Gateway';

	return $methods;
}
