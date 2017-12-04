<?php
/**
 * Payer for WooCommerce
 *
 * @package WC_Payer
 *
 * @wordpress-plugin
 * Plugin Name:     Payer for WooCommerce
 * Plugin URI:      https://krokedil.se/produkt/payer/
 * Description:     Extends WooCommerce. Provides a <a href="https://https://www.payer.se/" target="_blank">Payer</a> checkout for WooCommerce.
 * Version:         0.0.1
 * Author:          Krokedil
 * Author URI:      https://krokedil.se/
 * Developer:       Krokedil
 * Developer URI:   https://krokedil.se/
 * Text Domain:     payer-for-woocommerce	
 * Domain Path:     /languages
 * Copyright:       © 2009-2017 LeaseCloud AB.
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Payer_For_Woocommerce' ) ) {

	class Payer_For_Woocommerce {

		public static $log = '';

		public function __construct() {
			add_action( 'init', array( $this, 'payer_make_purchase' ) );			
			// Initiate the gateway
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			// Load scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		}

		public function payer_make_purchase() {
			if( isset( $_GET['payer-redirect'] ) && '1' === $_GET['payer-redirect'] ) {
				$order_id = $_GET['order_id'];
				Payer_Create_Purchase::create_purchase( $order_id );
				die();
			}
		}

		public function init() {
			// Set definitions
			$this->define();
			// Include the SDK
			require_once( 'vendor/autoload.php' );

			// Include the gateway classes
			include_once( PAYER_PLUGIN_DIR . '/classes/gateways/payer-factory-gateway.php' );
			include_once( PAYER_PLUGIN_DIR . '/classes/gateways/payer-card-payments-gateway.php' );

			// Include request classes
			include_once( PAYER_PLUGIN_DIR . '/classes/requests/payer-create-client.php' );
			include_once( PAYER_PLUGIN_DIR .'/classes/requests/payer-create-purchase.php' );

			// Include request helper classes
			include_once( PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-get-payment.php' );
			include_once( PAYER_PLUGIN_DIR .'/classes/requests/helpers/payer-get-purchase.php' );
			include_once( PAYER_PLUGIN_DIR .'/classes/requests/helpers/payer-get-items.php' );
			include_once( PAYER_PLUGIN_DIR .'/classes/requests/helpers/payer-get-customer.php' );
		}

		public function define() {
			// Set plugin directory
			define( 'PAYER_PLUGIN_DIR' , untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			// Set version number
			define( 'PAYER_VERSION_NUMBER', '0.0.1' );
			// Set path to SDK
			define( 'PAYER_SDK_DIR', '/vendor/' );
		}

		public function load_scripts() {
			if ( ! is_checkout() ) {
				return;
			}

			wp_register_script(
				'payer_checkout',
				plugins_url( 'assets/js/checkout.js', __FILE__ ),
				array( 'jquery', 'wc-cart' ),
				PAYER_VERSION_NUMBER
			);

			$checkout_localize_params = array(

			);

			wp_localize_script( 'payer_checkout', 'payer_checkout_params', $checkout_localize_params );

			wp_enqueue_script( 'payer_checkout' );

			wp_register_style(
				'payer_style',
				plugin_dir_url( __FILE__ ) . '/assets/css/checkout.css'
			);
			wp_enqueue_style( 'payer_style' );
		}

		public static function log( $message ) {
			$payer_settings = get_option( 'woocommerce_payer_settings' );
			if ( 'yes' === $payer_settings['debug_mode'] ) {
				if ( empty( self::$log ) ) {
					self::$log = new WC_Logger();
				}
				self::$log->add( 'leasecloud', $message );
			}
		}
	}
	new Payer_For_Woocommerce();
}