<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Get_Purchase {
    public static function get_purchase( $order_id ) {
        $payer_settings = get_option( 'woocommerce_payer_card_payment_settings' );        
        if ( 'yes' === $payer_settings['test_mode'] ) {
            $test_mode = true;
        } else {
            $test_mode = false;
        }

        $order = wc_get_order( $order_id );

        return array(
            'reference_id'  =>  $order_id,
            'customer'      =>  Payer_Get_Customer::get_customer( $order_id ),
            'items'         =>  Payer_Get_Items::get_items( $order_id ),
            'currency'      =>  get_woocommerce_currency(),
            'test_mode'     =>  $test_mode,
            'client_ip'     =>  get_post_meta( $order_id, '_customer_ip_address', true ),
        );
    }
}
