<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Masterpass_Functions {
    public function __construct() {
        add_action( 'woocommerce_after_cart_totals', array( $this, 'add_button' ) );
        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_button' ) );
        //if( is_shop() ) {
            add_action( 'woocommerce_after_mini_cart', array( $this, 'add_button' ) );
        //}
        //add_action( 'woocommerce_after_shop_loop_item', array( $this, 'add_button_shop' ), 15 );        
    }

    public function add_button() {
        $payer_masterpass_settings = get_option( 'woocommerce_payer_masterpass_settings' ); 
        if ( 'yes' === $payer_masterpass_settings['instant_masterpass_checkout'] ) {
            echo '<object type="image/svg" class="payer_instant_checkout"><img id="payer_instant_checkout" class="payer_instant_checkout" src="https://static.masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" alt="MasterPass"/></object>';
            echo '<a href="#" rel="external" onclick="window.open(\'http://www.mastercard.com/mc_us/wallet/learnmore/se\', \'_blank\', \'width=650,height=750,scrollbars=yes\'); return false;"><small>'. __( 'Read more...', 'payer-for-woocommerce' ) .'</small></a>';
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
            echo '<object type="image/svg" class="payer_instant_checkout" id="payer_instant_checkout_' . $id . '"><img class="payer_instant_checkout" data-product_id="' . $id . '" src="https://static.masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" alt="MasterPass"/> </object>';
            echo '<a href="#" rel="external" onclick="window.open(\'http://www.mastercard.com/mc_us/wallet/learnmore/se\', \'_blank\', \'width=650,height=750,scrollbars=yes\'); return false;"><small>'. __( 'Read more...', 'payer-for-woocommerce' ) .'</small></a>';            
        }
    }
}
new Payer_Masterpass_Functions;
