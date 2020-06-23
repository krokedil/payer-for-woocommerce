<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Payer Ajax class.
 *
 * @class    Payer_Ajax
 * @package  Payer/Classes
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Ajax extends WC_AJAX {
	/**
	 * Initiatie the class.
	 *
	 * @return void
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Adds ajax events.
	 *
	 * @return void
	 */
	public static function add_ajax_events() {
			$ajax_events = array(
				'get_address'              => true,
				'instant_product_purchase' => true,
				'instant_cart_purchase'    => true,
			);
			foreach ( $ajax_events as $ajax_event => $nopriv ) {
				add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				if ( $nopriv ) {
					add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
					// WC AJAX can be used for frontend ajax requests.
					add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				}
			}
	}

	/**
	 * Get Address Ajax event.
	 *
	 * @return void
	 */
	public static function get_address() {
			$personal_number = $_POST['personal_number'];
		if ( $personal_number !== '' ) {
			if ( '' !== $_POST['zip_code'] ) {
				$zip_code = $_POST['zip_code'];
			} else {
				$zip_code = 12345;
			}

			$payer_address_information = Payer_Get_Address::get_address( $personal_number, $zip_code );
			if ( 'invalid request' === $payer_address_information['status'] ) {
				$return = array(
					'message' => __( 'Invalid request, please check the information or fill in the address manually.', 'payer-for-woocommerce' ),
				);
				wp_send_json_error( $return );
				wp_die();
			} else {
				self::set_address( $payer_address_information );
				krokedil_log_events( null, 'Payer Get Address Response', $payer_address_information );
				$return = array(
					'address_information' => $payer_address_information,
					'message'             => __( 'Address found and added to the checkout form', 'payer-for-woocommerce' ),
				);
				wp_send_json_success( $return );
				wp_die();
			}
		} else {
			$return = array(
				'message' => __( 'Please fill in the personal number field', 'payer-for-woocommerce' ),
			);
			wp_send_json_error( $return );
			wp_die();
		}
	}

	/**
	 * Sets address as session.
	 *
	 * @param array $payer_address_information
	 *
	 * @return void
	 */
	private static function set_address( $payer_address_information ) {
		$payer_customer_details = array(
			'first_name' => $payer_address_information['first_name'],
			'last_name'  => $payer_address_information['last_name'],
			'address_1'  => $payer_address_information['address_1'],
			'address_2'  => $payer_address_information['address_2'],
			'company'    => $payer_address_information['company'],
			'city'       => $payer_address_information['city'],
		);

		WC()->session->set( 'payer_customer_details', $payer_customer_details );
	}

	/**
	 * Starts a instant purchase with MasterPass from the product page.
	 *
	 * @return void
	 */
	public static function instant_product_purchase() {
		$product_id   = $_POST['product_id'];
		$variation_id = $_POST['variation_id'];
		$quantity     = $_POST['quantity'];
		// Empty the current cart to prevent incorrect orders.
		WC()->cart->empty_cart();
		WC()->session->set( 'chosen_payment_method', 'payer_masterpass' );
		Payer_Masterpass_Populate_Order::add_item_to_cart( $product_id, $quantity, $variation_id );
		Payer_Masterpass_Functions::maybe_add_campaign_fee();

		$order    = wc_create_order();
		$order_id = $order->get_id();

		Payer_Masterpass_Populate_Order::add_order_details( $order );

		Payer_Masterpass_Populate_Order::set_gateway( $order );

		$redirect_url = wc_get_checkout_url();

		$redirect_url = add_query_arg(
			array(
				'payer-redirect' => '1',
				'order_id'       => $order_id,
			),
			$redirect_url
		);
		wp_send_json_success( $redirect_url );
		wp_die();
	}

	/**
	 * Starts a instant purchase with MasterPass from the cart page.
	 *
	 * @return void
	 */
	public static function instant_cart_purchase() {
		if ( WC()->cart->get_cart_contents_count() > 0 ) {
			WC()->session->set( 'chosen_payment_method', 'payer_masterpass' );
			Payer_Masterpass_Functions::maybe_add_campaign_fee();
			$order    = wc_create_order();
			$order_id = $order->get_id();

			Payer_Masterpass_Populate_Order::add_order_details( $order );

			Payer_Masterpass_Populate_Order::set_gateway( $order );

			$redirect_url = wc_get_checkout_url();

			$redirect_url = add_query_arg(
				array(
					'payer-redirect' => '1',
					'order_id'       => $order_id,
				),
				$redirect_url
			);
			wp_send_json_success( $redirect_url );
			wp_die();
		}
		wp_send_json_error();
		wp_die();
	}
}
Payer_Ajax::init();
