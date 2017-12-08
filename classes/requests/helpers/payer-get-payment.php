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
            case 'payer_bank_payment' :
                $method = 'bank';
                break;
            case 'payer_invoice_payment' :
                $method = 'invoice';
                break;
            case 'payer_installment_payment' :
                $method = 'installment';
                break;
            case 'payer_swish_payment' :
                $method = 'swish';
                break;
            case 'payer_einvoice_payment' :
                $method = 'einvoice';
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