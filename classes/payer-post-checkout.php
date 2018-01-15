<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Post_Checkout {
    public function __construct() {
        add_action( 'woocommerce_order_status_completed', array( $this, 'payer_order_completed' ) );
        add_action( 'woocommerce_order_status_cancelled', array( $this, 'payer_order_cancel' ) );
    }

    public function payer_order_completed( $order_id ) {

    }

    public function payer_order_cancel( $order_id ) {
        
    }
}