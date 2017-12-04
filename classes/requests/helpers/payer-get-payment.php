<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Get_Payment {
    public static function get_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        
        $payment_id = $order->get_payment_method();

        switch( $payment_id ) {
            case 'payer_card_payment' :
                $method = 'card';
                break;
            default :
                $method = 'card';
                break;
        }

        return array(
            'language'  =>  get_locale(),
            'method'    =>  $method,
            'url'       =>  self::get_url( $order ),
        );
    }

    private static function get_url( $order ) {
        return array(
            'authorize' =>  get_site_url() . '/wc-api/Payer_Gateway',
            'redirect'  =>  $order->get_cancel_order_url(),
            'settle'    =>  get_site_url() . '/wc-api/Payer_Gateway',
            'success'   =>  $order->get_checkout_order_received_url(),
        );
    }
}