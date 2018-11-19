<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Direct Invoice Payments Gateway.
 *
 * @class    Payer_Direct_Invoice_Gateway
 * @package  Payer/Classes/Gateways
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Direct_Invoice_Gateway extends Payer_Factory_Gateway {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->id                 = 'payer_direct_invoice_gateway';
		$this->method_title       = __( 'Payer Direct Invoice', 'payer-for-woocommerce' );
		$this->method_description = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->test_mode   = $this->get_option( 'test_mode' );
		$this->debug_mode  = $this->get_option( 'debug_mode' );
		$this->icon_url    = $this->get_option( 'payer_direct_invoice_payment_icon' );
		$this->icon        = $this->set_icon();

		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();

		$this->supports = array(
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'multiple_subscriptions',
		);

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_filter( 'woocommerce_page_wc-settings', array( $this, 'show_keys_in_settings' ) );
	}

	/**
	 * Handles the payment.
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		update_post_meta( $order_id, '_billing_pno', $_POST['billing_pno'] );

		// Check if customer changed any of the data from get_address
		$this->check_posted_data( $order_id );

		// Create an order
		Payer_Create_Order::create_order( $order_id );

		$order->payment_complete();
		krokedil_set_order_gateway_version( $order_id, PAYER_VERSION_NUMBER );
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_order_received_url(),
		);
	}

	/**
	 * Shows keys from settings on settings page.
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
		if ( 'yes' !== $this->get_option( 'enabled' ) ) {
			return false;
		}

		if ( $this->get_order_total() < 1 ) {
			return false;
		}

		return true;
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_krokedil_payer_direct_invoice_gateway' );
/**
 * Registers the gateway.
 *
 * @param array $methods
 * @return array $methods
 */
function add_krokedil_payer_direct_invoice_gateway( $methods ) {
	if ( ! defined( 'UNSET_PAYER_DIRECT_INVOICE_PAYMENTS' ) ) {
		$methods[] = 'Payer_Direct_Invoice_Gateway';
	}
	return $methods;
}
