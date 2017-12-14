<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Create_Purchase {

    public static function create_purchase( $order_id ) {
        $gateway = Payer_Create_Client::create_client();
        $data = array(
            'payment'   =>  Payer_Get_Payment::get_payment( $order_id ),
            'purchase'  =>  Payer_Get_Purchase::get_purchase( $order_id ),
        );
        $purchase = new Payer\Sdk\Resource\Purchase( $gateway );
        Payer_For_Woocommerce::log( 'Payer Create Purchase: '. $order_id . ' $data: ' . var_export( $data, true ) );
        $purchase->create( $data );
    }
}