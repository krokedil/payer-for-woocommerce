<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Create_Purchase {

    public static function create_purchase( $order_id ) {
        $gateway = Payer_Create_Client::create_client( 'payer_card_payment' );
        $data = array(
            'payment'   =>  Payer_Get_Payment::get_payment( $order_id ),
            'purchase'  =>  Payer_Get_Purchase::get_purchase( $order_id ),
        );
        $purchase = new Payer\Sdk\Resource\Purchase( $gateway );
        $purchase->create( $data );
    }
}