<?php
/**
 * Payer for WooCommerce
 *
 * @package WC_Payer
 *
 * @wordpress-plugin
 * Plugin Name:     Payer for WooCommerce
 * Plugin URI:      https://krokedil.se/payer/
 * Description:     Extends WooCommerce. Provides a <a href="https://https://www.payer.se/" target="_blank">Payer</a> checkout for WooCommerce.
 * Version:         1.2.0-Beta.2
 * Author:          Krokedil
 * Author URI:      https://krokedil.se/
 * Developer:       Krokedil
 * Developer URI:   https://krokedil.se/
 * Text Domain:     payer-for-woocommerce
 * Domain Path:     /languages
 *
 * WC requires at least: 4.0.0
 * WC tested up to: 7.3.0
 *
 * Copyright:       © Krokedil Produktionsbyrå AB.
 * License:         GNU General Public License v3.0
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Payer_For_Woocommerce' ) ) {

	/**
	 * Payer for WooCommerce.
	 */
	class Payer_For_Woocommerce {

		/**
		 * Log message.
		 *
		 * @var string Log message.
		 */
		public static $log = '';

		/**
		 * Class Constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'payer_make_purchase' ) );
			// Initiate the gateway.
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			// Load scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			add_filter( 'woocommerce_process_checkout_field_billing_first_name', array( $this, 'filter_pre_checked_value' ) );
			add_filter( 'woocommerce_process_checkout_field_billing_last_name', array( $this, 'filter_pre_checked_value' ) );
			add_filter( 'woocommerce_process_checkout_field_billing_address_1', array( $this, 'filter_pre_checked_value' ) );
			add_filter( 'woocommerce_process_checkout_field_billing_address_2', array( $this, 'filter_pre_checked_value' ) );
			add_filter( 'woocommerce_process_checkout_field_billing_postcode', array( $this, 'filter_pre_checked_value' ) );
			add_filter( 'woocommerce_process_checkout_field_billing_city', array( $this, 'filter_pre_checked_value' ) );
			add_filter( 'woocommerce_process_checkout_field_billing_company', array( $this, 'filter_pre_checked_value' ) );
			add_filter( 'woocommerce_default_address_fields', array( $this, 'override_checkout_check' ) );

			// Translations.
			load_plugin_textdomain( 'payer-for-woocommerce', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Trigger Payer purchase.
		 */
		public function payer_make_purchase() {
			if ( isset( $_GET['payer-redirect'] ) && '1' === $_GET['payer-redirect'] ) {
				$order_id = $_GET['order_id'];
				Payer_Create_Purchase::create_purchase( $order_id );
				die();
			}
		}

		/**
		 * Initiate the class.
		 */
		public function init() {
			if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
				return;
			}
			// Set definitions.
			$this->define();

			// Include the SDK.
			require_once 'vendor/autoload.php';

			// Include the gateway classes.
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-factory-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-card-payments-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-bank-payments-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-invoice-payments-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-installment-payments-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-swish-payments-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-einvoice-payments-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-direct-invoice-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-masterpass-gateway.php';
			include_once PAYER_PLUGIN_DIR . '/classes/gateways/payer-rent-payments-gateway.php';

			// Include request classes.
			include_once PAYER_PLUGIN_DIR . '/classes/requests/payer-create-purchase.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/payer-get-address.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/payer-create-order.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/payer-commit-order.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/payer-refund-order.php';

			// Include request helper classes.
			include_once PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-get-payment.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-get-purchase.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-get-items.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-get-customer.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-create-challenge.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-create-client.php';
			include_once PAYER_PLUGIN_DIR . '/classes/requests/helpers/payer-create-refund-data.php';

			// Include classes.
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-callbacks.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-ajax.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-masterpass-populate-order.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-masterpass-functions.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-post-checkout.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-admin-notices.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-gdpr.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-subscription.php';
			include_once PAYER_PLUGIN_DIR . '/classes/payer-class-rent-mail.php';

			// Include function files.
			include_once PAYER_PLUGIN_DIR . '/includes/payer-credentials-form-field.php';
		}

		/**
		 * Sets definitions.
		 */
		public function define() {
			// Set plugin directory.
			define( 'PAYER_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			// Set URL.
			define( 'PAYER_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
			// Set version number.
			define( 'PAYER_VERSION_NUMBER', '1.2.0-Beta.2' );
			// Set path to SDK.
			define( 'PAYER_SDK_DIR', '/vendor/' );
			// Set Krokedil Logger Defines.
			define( 'KROKEDIL_LOGGER_GATEWAY', 'payer_' );
			$payer_settings = get_option( 'woocommerce_payer_card_payment_settings' );
			if ( 'yes' === $payer_settings['debug_mode'] ) {
				define( 'KROKEDIL_LOGGER_ON', true );
			}
		}

		/**
		 * Loads scripts.
		 */
		public function load_scripts() {
			wp_register_script(
				'payer_checkout',
				plugins_url( 'assets/js/checkout.js', __FILE__ ),
				array( 'jquery', 'wc-cart' ),
				PAYER_VERSION_NUMBER
			);
			$payer_settings            = get_option( 'woocommerce_payer_card_payment_settings' );
			$get_address               = $payer_settings['get_address'];
			$payer_masterpass_settings = get_option( 'woocommerce_payer_masterpass_settings' );
			$masterpass_campaign       = false;
			if ( 'yes' === $payer_masterpass_settings['masterpass_campaign'] ) {
				$masterpass_campaign = true;
			}
			$checkout_localize_params = array(
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'locale'              => WC()->customer->get_billing_country(),
				'enable_get_address'  => $get_address,
				'get_address_text'    => __( 'Get Address', 'payer-for-woocommerce' ),
				'get_address'         => WC_AJAX::get_endpoint( 'get_address' ),
				'masterpass_campaign' => $masterpass_campaign,
			);

			wp_localize_script( 'payer_checkout', 'payer_checkout_params', $checkout_localize_params );

			wp_enqueue_script( 'payer_checkout' );

			if ( 'yes' === $payer_masterpass_settings['instant_masterpass_checkout'] && ( is_product() || is_cart() || is_shop() || is_product_category() ) ) {
				wp_register_script(
					'payer_instant_checkout',
					plugins_url( 'assets/js/instant-checkout.js', __FILE__ ),
					array( 'jquery', 'wc-cart' ),
					PAYER_VERSION_NUMBER
				);

				if ( is_product() ) {
					$page_type = 'product';
				} elseif ( is_cart() ) {
					$page_type = 'cart';
				} else {
					$page_type = '';
				}

				$instant_checkout_params = array(
					'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
					'locale'                   => WC()->customer->get_billing_country(),
					'instant_product_purchase' => WC_AJAX::get_endpoint( 'instant_product_purchase' ),
					'instant_cart_purchase'    => WC_AJAX::get_endpoint( 'instant_cart_purchase' ),
					'page_type'                => $page_type,
				);

				wp_localize_script( 'payer_instant_checkout', 'payer_instant_checkout_params', $instant_checkout_params );

				wp_enqueue_script( 'payer_instant_checkout' );
			}

			wp_register_style(
				'payer_style',
				plugin_dir_url( __FILE__ ) . 'assets/css/checkout.css',
				array(),
				PAYER_VERSION_NUMBER
			);
			wp_enqueue_style( 'payer_style' );
		}

		/**
		 * Logs messages.
		 *
		 * @param string $message Log message.
		 */
		public static function log( $message ) {
			$payer_settings = get_option( 'woocommerce_payer_card_payment_settings' );
			if ( 'yes' === $payer_settings['debug_mode'] ) {
				if ( empty( self::$log ) ) {
					self::$log = new WC_Logger();
				}
				self::$log->add( 'payer', $message );
			}
		}

		/**
		 * Filters pre checked values
		 *
		 * @param mixed $value The requested value.
		 *
		 * @return mixed Filtered value.
		 */
		public function filter_pre_checked_value( $value ) {
			$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
			$current_filter        = current_filter();
			$current_field         = str_replace( array( 'woocommerce_process_checkout_field_billing_' ), '', $current_filter );
			if ( strpos( $value, '**' ) !== false ) {
				$customer_details = WC()->session->get( 'payer_customer_details' );
				if ( isset( $customer_details[ $current_field ] ) && '' !== $customer_details[ $current_field ] ) {
					return $customer_details[ $current_field ];
				} else {
					return $value;
				}
			} else {
				return $value;
			}
			return $value;
		}

		/**
		 * Overrides the checkout validation on PostCode.
		 *
		 * @param array $address_fields_array The array with the WooCommerce address fields.
		 *
		 * @return array The edited array with the WooCommerce address fields.
		 */
		public function override_checkout_check( $address_fields_array ) {
			$chosen_payment_method = WC()->session->chosen_payment_method;
			if ( strpos( $chosen_payment_method, 'payer' ) !== false ) {
				unset( $address_fields_array['postcode']['validate'] );
			}
			return $address_fields_array;
		}
	}
	new Payer_For_Woocommerce();
}
