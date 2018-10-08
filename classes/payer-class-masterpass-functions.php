<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Contains MasterPass functionality.
 *
 * @class    Payer_Masterpass_Functions
 * @package  Payer/Classes
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Masterpass_Functions {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_after_cart_totals', array( $this, 'add_button' ) );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_button' ) );
		add_action( 'woocommerce_after_mini_cart', array( $this, 'add_button' ) );
		add_action( 'woocommerce_cart_calculate_fees', array( $this, 'maybe_add_campaign_fee' ) );
	}

	/**
	 * Adds the "Pay with MasterPass" button.
	 *
	 * @return void
	 */
	public function add_button() {
		$subscription = false;
		if ( class_exists( 'WC_Subscriptions_Cart' ) ) {
			if ( is_cart() && WC_Subscriptions_Cart::cart_contains_subscription() ) {
				$subscription = true;
			} elseif ( is_product() ) {
				global $product;
				if ( WC_Subscriptions_Product::is_subscription( $product ) ) {
					$subscription = true;
				}
			}
		}

		$payer_masterpass_settings = get_option( 'woocommerce_payer_masterpass_settings' );
		if ( 'yes' === $payer_masterpass_settings['instant_masterpass_checkout'] && false === $subscription ) {
			echo '<object type="image/svg" class="payer_instant_checkout"><img id="payer_instant_checkout" class="payer_instant_checkout" src="https://static.masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" alt="MasterPass"/></object>';
			if ( 'yes' === $payer_masterpass_settings['masterpass_campaign'] && is_product() && 'yes' === $payer_masterpass_settings['masterpass_campaign_text'] ) {
				global $product;
				$price            = floatval( $product->get_price() );
				$discount         = $payer_masterpass_settings['masterpass_campaign_amount'];
				$discounted_price = ( ( $price - $discount ) < 0 ) ? 0 : ( $price - $discount );
				echo '<p class="price" style="margin: 0">' . __( 'Campaign ', 'payer-for-woocommerce' ) . wc_price( $discounted_price ) . '</p>';
			}
			if ( '' !== $payer_masterpass_settings['masterpass_instant_purchase_text'] && isset( $payer_masterpass_settings['masterpass_instant_purchase_text'] ) ) {
				echo '<p>' . $payer_masterpass_settings['masterpass_instant_purchase_text'] . '</p>';
			}
			echo '<a href="#" rel="external" onclick="window.open(\'http://www.mastercard.com/mc_us/wallet/learnmore/se\', \'_blank\', \'width=650,height=750,scrollbars=yes\'); return false;"><small>' . __( 'Read more...', 'payer-for-woocommerce' ) . '</small></a>';
		}
	}

	/**
	 * Adds the pay with MasterPass button to the shop. Not used.
	 *
	 * @return void
	 */
	public function add_button_shop() {
		global $product;
		$id = $product->get_id();
		if ( $product->is_type( 'variable' ) ) {
			return false;
		}
		$payer_masterpass_settings = get_option( 'woocommerce_payer_masterpass_settings' );
		if ( 'yes' === $payer_masterpass_settings['instant_masterpass_checkout'] ) {
			echo '<object type="image/svg" class="payer_instant_checkout" id="payer_instant_checkout_' . $id . '"><img class="payer_instant_checkout" data-product_id="' . $id . '" src="https://static.masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" alt="MasterPass"/> </object>';
			echo '<a href="#" rel="external" onclick="window.open(\'http://www.mastercard.com/mc_us/wallet/learnmore/se\', \'_blank\', \'width=650,height=750,scrollbars=yes\'); return false;"><small>' . __( 'Read more...', 'payer-for-woocommerce' ) . '</small></a>';
		}
	}

	/**
	 * Maybe adds campaign fee to order.
	 *
	 * @return void
	 */
	public function maybe_add_campaign_fee() {
		// Check if masterpass is the chosen gateway.
		if ( 'payer_masterpass' === WC()->session->get( 'chosen_payment_method' ) ) {
			$masterpass_settings = get_option( 'woocommerce_payer_masterpass_settings' );
			$campaign            = isset( $masterpass_settings['masterpass_campaign'] ) ? $masterpass_settings['masterpass_campaign'] : 'no';
			// Check if there is a campaign active.
			if ( 'yes' === $campaign ) {
				$amount   = isset( $masterpass_settings['masterpass_campaign_amount'] ) ? floatval( $masterpass_settings['masterpass_campaign_amount'] ) : 0;
				$discount = ( $amount * -1 );
				WC()->cart->add_fee( __( 'MasterPass campaign discount', 'payer-for-woocommerce' ), $discount, true );
			}
		}
	}
}
new Payer_Masterpass_Functions();
