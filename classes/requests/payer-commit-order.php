<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Commit_Order {

    public static function commit_order( $order_id ) {
        $gateway = Payer_Create_Client::create_client();
        $data = array(
            'reference_id'  =>  $order_id,
            'order_id'      =>  get_post_meta( $order_id, 'payer_order_id' ),
        );
        $order = new Payer\Sdk\Resource\Order( $gateway );
        $invoice_number = $order->commit( $data );
        error_log( var_export( $invoice_number, true ) );
        update_post_meta( $order_id, '_payer_invoice_number', $invoice_number );
    }
}