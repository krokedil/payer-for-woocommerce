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
        krokedil_log_events( $order_id, 'Payer Create Purchase', $data );        
        Payer_For_Woocommerce::log( 'Payer Create Purchase: '. $order_id . ' $data: ' . var_export( $data, true ) );
        $response = $purchase->create( $data );
        krokedil_log_response( $order_id, $response );
    }
}