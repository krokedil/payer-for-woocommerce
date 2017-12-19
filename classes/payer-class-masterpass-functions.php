<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Masterpass_Functions {
    public function __construct() {
        add_action( 'woocommerce_after_cart_totals', array( $this, 'add_button' ) );
        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_button' ) );
        add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_button_shop' ), 15 );        
    }

    public function add_button() {
        $payer_masterpass_settings = get_option( 'woocommerce_payer_masterpass_settings' ); 
        if ( 'yes' === $payer_masterpass_settings['instant_masterpass_checkout'] ) {
            echo '<button type="button" class="payer_instant_checkout button" id="payer_instant_checkout">Instant Checkout with MasterPass</button>';
        }
    }
    public function add_button_shop() {
        global $product;
        $id = $product->get_id();
        if ( $product->is_type( 'variable' ) ) { 
            return false; 
        }
        $payer_masterpass_settings = get_option( 'woocommerce_payer_masterpass_settings' ); 
        if ( 'yes' === $payer_masterpass_settings['instant_masterpass_checkout'] ) {
            echo '<button type="button" class="payer_instant_checkout button" id="payer_instant_checkout_' . $id . '" data-product_id="' . $id . '">Instant Checkout with MasterPass</button>';
        }
    }
}

new Payer_Masterpass_Functions;