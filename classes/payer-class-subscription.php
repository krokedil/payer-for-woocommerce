<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Payer Subscription Class
 */
class Payer_Subscription {
	public function __construct() {
		add_filter( 'payer_create_purchase_data', array( $this, 'maybe_handle_subscription' ) );
		add_filter( 'payer_create_purchase_data', array( $this, 'maybe_handle_card_free_trial' ) );
		add_action( 'woocommerce_scheduled_subscription_payment_payer_direct_invoice_gateway', array( $this, 'handle_direct_invoice_recurring' ), 10, 2 );
		add_action( 'woocommerce_scheduled_subscription_payment_payer_card_payment', array( $this, 'handle_card_recurring' ), 10, 2 );
		add_action( 'woocommerce_scheduled_subscription_payment_payer_rent_payment', array( $this, 'handle_rent_recurring' ), 10, 2 );

	}

	public function maybe_handle_subscription( $data ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) ) {
			$order_id     = $data['purchase']['reference_id'];
			$order        = wc_get_order( $order_id );
			$subscription = wcs_order_contains_subscription( $order );

			if ( true === $subscription ) {
				$data['payment']['options']['store'] = true;
			}
		}
		return $data;
	}

	public function handle_direct_invoice_recurring( $renewal_total, $renewal_order ) {

		$order_id    = $renewal_order->get_id();
		$billing_pno = get_post_meta( WC_Subscriptions_Renewal_Order::get_parent_order_id( $order_id ), apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), true );
		update_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), $billing_pno );

		WC_Subscriptions_Manager::process_subscription_payments_on_order( $renewal_order );
	}

	public function handle_card_recurring( $renewal_total, $renewal_order ) {
		$gateway  = Payer_Create_Client::create_client();
		$order_id = $renewal_order->get_id();

		$subscriptions = wcs_get_subscriptions_for_renewal_order( $renewal_order->get_id() );
		reset( $subscriptions );
		$subscription_id = key( $subscriptions );
		get_post_meta( $order_id, 'payer_recurring_token', true );
		$recurring_token = get_post_meta( $order_id, 'payer_recurring_token', true );

		if ( empty( $recurring_token ) ) {
			$recurring_token = get_post_meta( WC_Subscriptions_Renewal_Order::get_parent_order_id( $order_id ), 'payer_recurring_token', true );
			update_post_meta( $order_id, 'payer_recurring_token', $recurring_token );
		}

		$price_incl_tax   = $renewal_order->get_total();
		$price_excl_tax   = $renewal_order->get_total() - $renewal_order->get_total_tax();
		$price_difference = $price_incl_tax - $price_excl_tax;

		$data = array(
			'recurring_token' => $recurring_token,
			'description'     => 'Subscription',
			'amount'          => $renewal_order->get_total(),
			'vat_percentage'  => intval( ( $price_difference / $price_excl_tax ) * 100 ),
			'currency'        => $renewal_order->get_currency(),
			'reference_id'    => $order_id,
		);

		$purchase = new Payer\Sdk\Resource\Purchase( $gateway );
		$response = $purchase->debit( $data );
		if ( isset( $response['transaction_id'] ) && is_int( $response['transaction_id'] ) ) {
			WC_Subscriptions_Manager::process_subscription_payments_on_order( $renewal_order );
			$renewal_order->add_order_note( __( 'Subscription payment made with Payer', 'payer-for-woocommerce' ) );

			$renewal_order->payment_complete( $order_id );
		} else {
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $renewal_order );
			$renewal_order->add_order_note( __( 'Subscription payment failed to create with Payer', 'payer-for-woocommerce' ) );
		}
	}

	/**
	 * Handles the recurring payment for the rent orders
	 *
	 * @param float    $renewal_total Total of the order
	 * @param WC_Order $renewal_order WooCommerce order object
	 * @return void
	 */
	public function handle_rent_recurring( $renewal_total, $renewal_order ) {
		$order_id = $renewal_order->get_id();

		$pno = get_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), true );

		if ( empty( $pno ) ) {
			$pno = get_post_meta( WC_Subscriptions_Renewal_Order::get_parent_order_id( $order_id ), apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), true );
			update_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), $pno );
		}

		do_action( 'payer_send_rent_mail', $order_id );
		WC_Subscriptions_Manager::process_subscription_payments_on_order( $renewal_order );
		$renewal_order->payment_complete();
	}

	public function maybe_handle_card_free_trial( $data ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) ) {
			$order_id = $data['purchase']['reference_id'];
			$order    = wc_get_order( $order_id );
			if ( empty( floatval( $order->get_total() ) ) ) {
				foreach ( $data['purchase']['items'] as $item ) {
					$x = 0;
					if ( 0 === $item['unit_price'] ) {
						$data['purchase']['items'][ $x ]['unit_price'] = 1;
					}
					$x++;
				}
			}
		}
		return $data;
	}
} new Payer_Subscription();
