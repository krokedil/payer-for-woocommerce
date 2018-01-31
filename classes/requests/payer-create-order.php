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
        update_post_meta( $order_id, '_payer_order_id', $payer_order_id );
        Payer_For_Woocommerce::log( 'Payer Create Order: '. $order_id . ' $data: ' . var_export( $data, true ) );
        krokedil_log_events( $order_id, 'Payer Create order', $data );
    }
}