<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Card Payments Gateway.
 *
 * @class    Payer_Card_Payments_Gateway
 * @package  Payer/Classes/Gateways
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Card_Payments_Gateway extends Payer_Factory_Gateway {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->id                 = 'payer_card_payment';
		$this->method_title       = __( 'Payer Card', 'payer-for-woocommerce' );
		$this->method_description = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title          = $this->get_option( 'title' );
		$this->description    = $this->get_option( 'description' );
		$this->payer_agent_id = $this->get_option( 'payer_agent_id' );
		$this->payer_password = $this->get_option( 'payer_password' );
		$this->test_mode      = $this->get_option( 'test_mode' );
		$this->debug_mode     = $this->get_option( 'debug_mode' );
		$this->icon_url       = $this->get_option( 'payer_card_payment_icon' );
		$this->icon           = $this->set_icon();
		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();

		$support_array    = array(
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
		$payer_settings   = get_option( 'woocommerce_payer_card_payment_settings' );
		$order_management = $payer_settings['order_management'];
		if ( 'yes' === $order_management ) {
			array_push( $support_array, 'refunds' );
		}
		$this->supports = $support_array;

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		// add_action( 'woocommerce_review_order_after_cart_contents', array( $this, 'add_free_trial_text_to_description' ) );
		$this->add_free_trial_text_to_description();
	}

	public function add_free_trial_text_to_description() {
		if ( is_checkout() && class_exists( 'WC_Subscriptions_Cart' ) && empty( floatval( WC()->cart->get_total( 'payer' ) ) ) && WC_Subscriptions_Cart::cart_contains_subscription() ) {
			$description = $this->description;

			$free_trial_message = wc_price( 1 ) . __( ' will be reserved on your card.', 'payer-for-woocommerce' );

			if ( '' !== $description ) {
				$description = $description . '<br>' . $free_trial_message;
			} else {
				$description = $free_trial_message;
			}

			$this->description = $description;
		}
	}

	public function is_available() {
		if ( 'yes' !== $this->enabled ) {
			return false;
		}

		return true;
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_krokedil_payer_card_gateway' );
/**
 * Registers the gateway.
 *
 * @param array $methods
 * @return array $methods
 */
function add_krokedil_payer_card_gateway( $methods ) {
	$methods[] = 'Payer_Card_Payments_Gateway';

	return $methods;
}
