<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Creates a Payer purchase.
 *
 * @class    Payer_Create_Purchase
 * @package  Payer/Classes/Requests
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Create_Purchase {
	/**
	 * Creates purchase.
	 *
	 * @param int $order_id
	 * @return void
	 */
	public static function create_purchase( $order_id ) {
		$gateway  = Payer_Create_Client::create_client();
		$data     = array(
			'payment'  => Payer_Get_Payment::get_payment( $order_id ),
			'purchase' => Payer_Get_Purchase::get_purchase( $order_id ),
		);
		$data     = apply_filters( 'payer_create_purchase_data', $data );
		$purchase = new Payer\Sdk\Resource\Purchase( $gateway );
		krokedil_log_events( $order_id, 'Payer Create Purchase', $data );
		Payer_For_Woocommerce::log( 'Payer Create Purchase: ' . $order_id . ' $data: ' . var_export( $data, true ) );
		$response = $purchase->create( $data );
		krokedil_log_response( $order_id, $response );
	}
}
