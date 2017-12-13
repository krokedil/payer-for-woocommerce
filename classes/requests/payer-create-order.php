<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Create_Order {

    public static function create_order( $order_id ) {
        $gateway = Payer_Create_Client::create_client();
        $data = Payer_Get_Purchase::get_purchase( $order_id );
        $order = new Payer\Sdk\Resource\Order( $gateway );
        $payer_order_id = $order->create( $data );
        error_log( var_export( $payer_order_id, true ) );                        
        update_post_meta( $order_id, '_payer_order_id', $payer_order_id );
    }
}