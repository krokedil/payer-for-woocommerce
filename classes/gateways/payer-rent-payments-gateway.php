<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Rent Payments Gateway.
 *
 * @class    Payer_Rent_Payments_Gateway
 * @package  Payer/Classes/Gateways
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Rent_Payments_Gateway extends Payer_Factory_Gateway {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->id                 = 'payer_rent_payment';
		$this->method_title       = __( 'Payer Rent', 'payer-for-woocommerce' );
		$this->method_description = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->test_mode   = $this->get_option( 'test_mode' );
		$this->debug_mode  = $this->get_option( 'debug_mode' );
		$this->icon_url    = $this->get_option( 'payer_rent_payment_icon' );
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

		$this->supports = array(
			'products',
			'subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change_customer',
			'subscription_payment_method_change_admin',
			'multiple_subscriptions',
		);

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_filter( 'woocommerce_page_wc-settings', array( $this, 'show_keys_in_settings' ) );

	}

	/**
	 * Processes the payment
	 *
	 * @param string $order_id The WooCommerce order id.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		update_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), apply_filters( 'payer_pno_field_data', $_POST['billing_pno'] ) );
		$order->update_status( 'on-hold' );
		do_action( 'payer_send_rent_mail', $order_id );
		return array(
			'result'   => 'success',
			'redirect' => $order->get_checkout_order_received_url(),
		);
	}

	public function is_available() {
		$order_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_STRING );
		if ( empty( $order_id ) ) {
			$order_id = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_STRING );
		}
		$order = wc_get_order( $order_id );
		if ( 'yes' !== $this->enabled ) {
			return false;
		}

		if ( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) {
			return true;
		}

		if ( $order && class_exists( 'WC_Subscription' ) && wcs_is_subscription( $order ) ) {
			return true;
		}

		return false;
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
}

/**
 * Registers the gateway.
 *
 * @param array $methods
 * @return array $methods
 */
function add_krokedil_payer_rent_gateway( $methods ) {
	if ( ! defined( 'UNSET_PAYER_RENT_PAYMENTS' ) ) {
		$methods[] = 'Payer_Rent_Payments_Gateway';
	}
	return $methods;
}
