<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Commits a Payer order.
 * 
 * @class    Payer_Commit_Order
 * @package  Payer/Classes/Requests
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Commit_Order {

    /**
     * Commits order.
     *
     * @param int $order_id
     * @return bool|void
     */
    public static function commit_order( $order_id ) {
        $gateway = Payer_Create_Client::create_client();
        $data = array(
            'reference_id'  =>  $order_id,
            'order_id'      =>  get_post_meta( $order_id, 'payer_order_id', true ),
        );
        Payer_For_Woocommerce::log( 'Payer Commit order request: '. $order_id . ' $data: ' . var_export( $data, true ) );
        krokedil_log_events( $order_id, 'Payer Commit order request', $data );        
        $order = new Payer\Sdk\Resource\Order( $gateway );
        $invoice_number = $order->commit( $data );
        Payer_For_Woocommerce::log( 'Payer Commit order response: '. $order_id . ' $invoice_number: ' . var_export( $invoice_number, true ) );
        krokedil_log_events( $order_id, 'Payer Commit order response', $invoice_number );
        if( isset( $invoice_number['invoice_number'] ) ) {
            update_post_meta( $order_id, '_payer_invoice_number', $invoice_number['invoice_number'] );
            return true;
        }
    }
}