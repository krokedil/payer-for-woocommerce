<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Creates a Payer refund.
 * 
 * @class    Payer_Refund_Order
 * @package  Payer/Classes/Requests
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Refund_Order{

    /**
     * Creates the refund.
     *
     * @param int $order_id
     * @param int $amount
     * @param string $reason
     * @return void
     */
    public static function refund_order( $order_id, $amount, $reason ) {
        $payment_id = get_post_meta( $order_id, '_payer_payment_id', true );
        $gateway = Payer_Create_Client::create_client();
        $data = Payer_Create_Refund_Data::create_refund_data( $order_id, $amount, $reason, $payment_id );
        $purchase = new Payer\Sdk\Resource\Purchase( $gateway );
        Payer_For_Woocommerce::log( 'Payer Refund Order Data: '. $order_id . ' $data: ' . var_export( $data, true ) );
        krokedil_log_events( $order_id, 'Payer Refund Order', $data );
        $purchase->refund( $data );
    }
}