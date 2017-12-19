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
            echo '<object type="image/svg" class="payer_instant_checkout" id="payer_instant_checkout"><img src="https://static.masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" alt="MasterPass"/> </object>';
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
            echo '<object type="image/svg" class="payer_instant_checkout" id="payer_instant_checkout_' . $id . '" data-product_id="' . $id . '"><img src="https://static.masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" alt="MasterPass"/> </object>';
        }
    }
}

new Payer_Masterpass_Functions;