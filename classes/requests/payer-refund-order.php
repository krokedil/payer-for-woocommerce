<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Refund_Order{

    public static function refund_order( $order_id, $amount, $reason ) {
        $gateway = Payer_Create_Client::create_client();
        $data = array(
            'transaction_id'    =>  $order_id,
            'reason'            =>  $reason,
            'amount'            =>  $amount,
            'vat_percentage'    =>  25,
        );
        $purchase = new Payer\Sdk\Resource\Purchase( $gateway );
        $invoice_number = $purchase->refund( $data );
        update_post_meta( $order_id, '_payer_invoice_number', $invoice_number );
    }
}