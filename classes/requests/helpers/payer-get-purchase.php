<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Gets Purchase object.
 *
 * @class    Payer_Get_Purchase
 * @package  Payer/Classes/Requests/Helpers
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Get_Purchase {
	/**
	 * Gets purchase object.
	 *
	 * @param int $order_id
	 * @return array
	 */
	public static function get_purchase( $order_id, $pno = null ) {
		$payer_settings = get_option( 'woocommerce_payer_card_payment_settings' );
		if ( 'yes' === $payer_settings['test_mode'] ) {
			$test_mode = true;
		} else {
			$test_mode = false;
		}

		$order = wc_get_order( $order_id );

		return array(
			'reference_id' => $order_id,
			'customer'     => Payer_Get_Customer::get_customer( $order_id, $pno ),
			'items'        => Payer_Get_Items::get_items( $order_id ),
			'currency'     => get_woocommerce_currency(),
			'test_mode'    => $test_mode,
			'client_ip'    => get_post_meta( $order_id, '_customer_ip_address', true ),
			'charset'      => 'UTF-8',
		);
	}
}
