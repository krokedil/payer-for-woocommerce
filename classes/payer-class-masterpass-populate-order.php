<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Masterpass_Populate_Order {
    public static function add_item_to_cart( $product_id, $quantity, $variation_id ) {
        error_log( 'cart should be updated' );
        WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
    }

    public static function set_gateway( $order ) {
        $available_gateways = WC()->payment_gateways->payment_gateways();
        $payment_method = $available_gateways['payer_masterpass'];
        $order->set_payment_method( $payment_method );
        $order->save();
    }
}