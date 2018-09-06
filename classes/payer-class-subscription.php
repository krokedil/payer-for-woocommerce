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
		add_action( 'woocommerce_scheduled_subscription_payment_payer_direct_invoice_gateway', array( $this, 'handle_direct_invoice_recurring' ), 10, 2 );
		add_action( 'woocommerce_scheduled_subscription_payment_payer_card_payment', array( $this, 'handle_card_recurring' ), 10, 2 );
	}

	public function maybe_handle_subscription( $data ) {
		if ( class_exists( 'WC_Subscriptions_Product' ) ) {
			$subscription = false;
			if ( isset( $data['purchase'] ) ) {
				$purchase = $data['purchase'];
			} else {
				$purchase = $data;
			}
			foreach ( $purchase['items'] as $line_item ) {
				if ( $this->get_product_from_sku( $line_item['article_number'] ) ) {
					$product = $this->get_product_from_sku( $line_item['article_number'] );
				} else {
					$product = $this->get_product_from_id( $line_item['article_number'] );
				}
				if ( $this->check_if_product_is_subscription( $product ) ) {
					$subscription = true;
					break;
				}
			}

			if ( true === $subscription ) {
				$data['payment']['options']['store'] = true;
			}
		}
		return $data;
	}

	private function get_product_from_sku( $sku ) {
		$product_id = wc_get_product_id_by_sku( $sku );

		return wc_get_product( $product_id );
	}

	private function get_product_from_id( $id ) {
		return wc_get_product( $id );

	}

	private function check_if_product_is_subscription( $product ) {
		return WC_Subscriptions_Product::is_subscription( $product );
	}

	public function handle_direct_invoice_recurring( $renewal_total, $renewal_order ) {
		$order_id    = $renewal_order->get_id();
		$billing_pno = get_post_meta( WC_Subscriptions_Renewal_Order::get_parent_order_id( $order_id ), '_billing_pno', true );

		$payer_order_id = Payer_Create_Order::create_order( $order_id, $billing_pno );
		if ( isset( $payer_order_id['order_id'] ) && is_int( $payer_order_id['order_id'] ) ) {
			WC_Subscriptions_Manager::process_subscription_payments_on_order( $renewal_order );
			$renewal_order->add_order_note( __( 'Subscription payment made with Payer', 'payer-for-woocommerce' ) );
			$renewal_order->payment_complete();
		} else {
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $renewal_order );
			$renewal_order->add_order_note( __( 'Subscription payment failed to create with Payer', 'payer-for-woocommerce' ) );
		}
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
			$renewal_order->payment_complete();
		} else {
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $renewal_order );
			$renewal_order->add_order_note( __( 'Subscription payment failed to create with Payer', 'payer-for-woocommerce' ) );
		}
	}
}
new Payer_Subscription();
