<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Creates a Payer order.
 *
 * @class    Payer_Create_Order
 * @package  Payer/Classes/Requests
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Create_Order {

	/**
	 * Creates the order with Payer.
	 *
	 * @param int $order_id
	 * @return void
	 */
	public static function create_order( $order_id, $pno = null ) {
		$gateway        = Payer_Create_Client::create_client();
		$data           = Payer_Get_Purchase::get_purchase( $order_id, $pno );
		$order          = new Payer\Sdk\Resource\Order( $gateway );
		$payer_order_id = $order->create( $data );
		update_post_meta( $order_id, '_payer_order_id', $payer_order_id );
		Payer_For_Woocommerce::log( 'Payer Create Order: ' . $order_id . ' $data: ' . var_export( $data, true ) );
		krokedil_log_events( $order_id, 'Payer Create order', $data );

		return $payer_order_id;
	}
}
