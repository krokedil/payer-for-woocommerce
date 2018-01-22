<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Factory_Gateway extends WC_Payment_Gateway {
	public function __construct() {
		add_filter( 'woocommerce_checkout_fields' , array( $this, 'add_pno_field' ) );

		$this->supports = array(
			'products',
			'refunds',
		);
	}

	public function init_form_fields() {
		$this->form_fields = include( PAYER_PLUGIN_DIR . '/includes/payer-factory-settings.php' );
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Check if customer changed any of the data from get_address
		$this->check_posted_data( $order_id );

		update_post_meta( $order_id, '_billing_pno', $_POST['billing_pno'] );

		$checkout_url = WC()->cart->get_checkout_url();

		$redirect_url = add_query_arg(
			array(
				'payer-redirect'	=>	'1',
				'order_id'			=>	$order_id,
			),
			$checkout_url
		);

		$this->clear_sessions();

		return array(
			'result'   => 'success',
			'redirect' => $redirect_url,
		);
	}

	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );
		if( strpos( $order->get_payment_method(), 'payer_' ) ) {
			if( ! get_post_meta( $order_id, '_payer_order_refunded' ) ) {
				Payer_Refund_Order::refund_order( $order_id, $amount, $reason );
				update_post_meta( $order_id, '_payer_order_refunded', 'true' );
				$order->add_order_note( __( 'The order has been refunded with Payer', 'payer-for-woocommerce' ) );
				
				return true;
			} 
		}
	}

	public function add_pno_field( $fields ) {
		$fields['billing']['billing_pno'] = array(
			'label'     	=> __('Personal number', 'payer-for-woocommerce'),
			'placeholder'   => _x('xxxxxx-xxxx', 'placeholder', 'payer-for-woocommerce'),
			'required'  	=> false,
			'class'     	=> array('form-row-wide'),
			'clear'     	=> true
		 );
	
		 return $fields;
	}

	public function set_icon() {
		switch ( $this->id ) {
			case 'payer_bank_payment':
				$default_img = 'payer-icon-payment_method-bank.png';
				break;
			case 'payer_card_payment':
				$default_img = 'payer-icon-payment_method-card_01.png';
				break;
			case 'payer_direct_invoice_gateway':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_einvoice_payment':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_installment_payment':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_invoice_payment':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_masterpass':
				$default_img = 'payer-icon-payment_method-masterpass.png';
				break;
			case 'payer_swish_payment':
				$default_img = 'payer-icon-payment_method-swish.png';
				break;
		}
		if( '' !== $this->icon_url ) {
			return $this->icon_url;
		} else {
			return PAYER_PLUGIN_URL . '/assets/img/' . $default_img;		
		}
	}

	public function check_posted_data( $order_id ) {
		if( WC()->session->get( 'payer_customer_details' ) ) {
			$get_address_data = WC()->session->get( 'payer_customer_details' );
			$order = wc_get_order( $order_id );
			$order_data = array(
				'first_name'	=>	$order->get_billing_first_name(),
				'last_name'		=>	$order->get_billing_last_name(),
				'address_1'		=>  $order->get_billing_address_1(),
				'address_2'		=> 	$order->get_billing_address_2(),
				'company'		=>	$order->get_billing_company(),
				'city'			=>	$order->get_billing_city(),
			);		
			if( $get_address_data != $order_data ) {
				$order->add_order_note( 'The address information was changed by the customer from the get address information.', 'payer-for-woocommerce' );
			}

		}

	}

	private function clear_sessions() {
		WC()->session->__unset( 'payer_customer_details' );
	}
}