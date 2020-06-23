<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Handles order completion.
 *
 * @class    Payer_Post_Checkout
 * @package  Payer/Classes
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Post_Checkout {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( $this, 'payer_order_completed' ) );
	}

	/**
	 * Completes the order with Payer.
	 *
	 * @param int $order_id
	 * @return void
	 */
	public function payer_order_completed( $order_id ) {
		$payer_settings   = get_option( 'woocommerce_payer_card_payment_settings' );
		$order_management = $payer_settings['order_management'];
		if ( 'yes' === $order_management ) {
			$order = wc_get_order( $order_id );
			if ( ! get_post_meta( $order_id, '_payer_order_completed' ) ) {
				if ( in_array( $order->get_payment_method(), array( 'payer_invoice_payment', 'payer_installment_payment' ) ) ) {
					Payer_Commit_Order::commit_order( $order_id );

					update_post_meta( $order_id, '_payer_order_completed', 'true' );
					$order->add_order_note( __( 'The order has been completed with Payer', 'payer-for-woocommerce' ) );
				}

				// Card payment subscription parent order.
				if ( function_exists( 'wcs_order_contains_subscription' ) && function_exists( 'wcs_is_subscription' ) ) {
					if ( $order->get_payment_method() === 'payer_card_payment' && wcs_order_contains_subscription( $order, 'parent' ) ) {
						$this->make_debit( $order_id );

						update_post_meta( $order_id, '_payer_order_completed', 'true' );
						$order->add_order_note( __( 'The order has been completed with Payer', 'payer-for-woocommerce' ) );
					}
				}

				if ( $order->get_payment_method() === 'payer_direct_invoice_gateway' ) {
					$order_id = $order->get_id();
					$order->set_transaction_id( Payer_Create_Order::create_order( $order_id, get_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), true ) ) );

					Payer_Commit_Order::commit_order( $order_id );

					update_post_meta( $order_id, '_payer_order_completed', 'true' );
					$order->add_order_note( __( 'The order has been completed with Payer', 'payer-for-woocommerce' ) );
				}
			}
		}
	}


	private function make_debit( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! empty( floatval( $order->get_total() ) ) ) {
			$gateway          = Payer_Create_Client::create_client();
			$price_incl_tax   = $order->get_total();
			$price_excl_tax   = $order->get_total() - $order->get_total_tax();
			$price_difference = $price_incl_tax - $price_excl_tax;

			$data     = array(
				'recurring_token' => get_post_meta( $order_id, 'payer_recurring_token', true ),
				'description'     => 'Subscription',
				'amount'          => $order->get_total(),
				'vat_percentage'  => intval( ( $price_difference / $price_excl_tax ) * 100 ),
				'currency'        => $order->get_currency(),
				'reference_id'    => $order_id,
			);
			$purchase = new Payer\Sdk\Resource\Purchase( $gateway );
			$response = $purchase->debit( $data );
			krokedil_log_response( $order_id, $response );
		}
	}
}
new Payer_Post_Checkout();
