<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Post_Checkout {
    public function __construct() {
        add_action( 'woocommerce_order_status_completed', array( $this, 'payer_order_completed' ) );
    }

    public function payer_order_completed( $order_id ) {
        $payer_settings = get_option( 'woocommerce_payer_card_payment_settings' );
        $order_management = $payer_settings['order_management'];
        if( 'yes' === $order_management ) {
            $order = wc_get_order( $order_id );
            if( ! get_post_meta( $order_id, '_payer_order_completed' ) ) {
                if( in_array(  $order->get_payment_method(), array( 'payer_invoice_payment', 'payer_installment_payment', 'payer_direct_invoice_gateway' ) ) ) {
                    Payer_Commit_Order::commit_order( $order_id );
                }
                update_post_meta( $order_id, '_payer_order_completed', 'true' );
                $order->add_order_note( __( 'The order has been completed with Payer', 'payer-for-woocommerce' ) );
            }
        }
    }
}
new Payer_Post_Checkout;