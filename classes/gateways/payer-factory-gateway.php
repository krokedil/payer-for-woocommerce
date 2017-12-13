<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Factory_Gateway extends WC_Payment_Gateway {
	public function __construct() {
		add_filter( 'woocommerce_checkout_fields' , array( $this, 'add_pno_field' ) );
	}

	public function init_form_fields() {
		$this->form_fields = include( PAYER_PLUGIN_DIR . '/includes/payer-factory-settings.php' );
	}

	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		update_post_meta( $order_id, '_billing_pno', $_POST['billing_pno'] );

		global $woocommerce;
		$checkout_url = $woocommerce->cart->get_checkout_url();

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

	private function clear_sessions() {
		WC()->session->__unset( 'payer_customer_details' );
	}
}