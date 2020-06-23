<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Payer Factory Gateway.
 *
 * @class    Payer_Factory_Gateway
 * @package  Payer/Classes/Gateways
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Factory_Gateway extends WC_Payment_Gateway {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_checkout_fields', array( $this, 'add_pno_field' ) );
	}

	/**
	 * Loads the settings file.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = include PAYER_PLUGIN_DIR . '/includes/payer-factory-settings.php';
	}

	/**
	 * Handles payment.
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		// Check if customer changed any of the data from get_address
		$this->check_posted_data( $order_id );

		update_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), apply_filters( 'payer_pno_field_data', $_POST['billing_pno'] ) );

		$checkout_url = wc_get_checkout_url();

		$redirect_url = add_query_arg(
			array(
				'payer-redirect' => '1',
				'order_id'       => $order_id,
			),
			$checkout_url
		);

		$this->clear_sessions();
		krokedil_set_order_gateway_version( $order_id, PAYER_VERSION_NUMBER );
		return array(
			'result'   => 'success',
			'redirect' => $redirect_url,
		);
	}

	/**
	 * Handles refunds.
	 *
	 * @param int    $order_id
	 * @param int    $amount
	 * @param string $reason
	 * @return bool
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );
		if ( false !== strpos( $order->get_payment_method(), 'payer_' ) ) {
			if ( ! get_post_meta( $order_id, '_payer_order_refunded' ) ) {
				Payer_Refund_Order::refund_order( $order_id, $amount, $reason );
				update_post_meta( $order_id, '_payer_order_refunded', 'true' );
				$order->add_order_note( __( 'The order has been refunded with Payer', 'payer-for-woocommerce' ) );
				return true;
			} else {
				throw new Exception( __( 'The order has already been refunded with Payer', 'payer-for-woocommerce' ) );
				return false;
			}
		}
		throw new Exception( __( 'Unknown error. The order has not been refunded with Payer.', 'payer-for-woocommerce' ) );
		return false;
	}

	/**
	 * Adds Personalnumber field to checkout.
	 *
	 * @param array $fields
	 * @return array $fields
	 */
	public function add_pno_field( $fields ) {
		$settings = get_option( 'woocommerce_payer_card_payment_settings' );
		if ( 'yes' === $settings['get_address'] ) {
			$fields['billing']['billing_pno'] = array(
				'label'       => apply_filters( 'payer_pno_label', __( 'Personal number', 'payer-for-woocommerce' ) ),
				'placeholder' => _x( 'xxxxxx-xxxx', 'placeholder', 'payer-for-woocommerce' ),
				'required'    => false,
				'class'       => array( 'form-row-wide' ),
				'clear'       => true,
			);
		}
		return $fields;
	}

	/**
	 * Gets the icon for the gateways.
	 *
	 * @return string
	 */
	public function set_icon() {
		switch ( $this->id ) {
			case 'payer_bank_payment':
				$default_img = 'payer-icon-payment_method-bank.png';
				break;
			case 'payer_card_payment':
				$default_img = 'payer-icon-payment_method-card_01.png';
				break;
			case 'payer_direct_invoice_gateway':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_einvoice_payment':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_installment_payment':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_invoice_payment':
				$default_img = 'payer-icon-payment_method-invoice.png';
				break;
			case 'payer_masterpass':
				$default_img = 'payer-icon-payment_method-masterpass.png';
				break;
			case 'payer_swish_payment':
				$default_img = 'payer-icon-payment_method-swish.png';
				break;
		}
		if ( '' !== $this->icon_url ) {
			return $this->icon_url;
		} else {
			return PAYER_PLUGIN_URL . '/assets/img/' . $default_img;
		}
	}

	/**
	 * Checks if address has been changed from what was recieved.
	 *
	 * @param int $order_id
	 * @return void
	 */
	public function check_posted_data( $order_id ) {
		if ( WC()->session->get( 'payer_customer_details' ) ) {
			$get_address_data = WC()->session->get( 'payer_customer_details' );
			$order            = wc_get_order( $order_id );
			$order_data       = array(
				'first_name' => $order->get_billing_first_name(),
				'last_name'  => $order->get_billing_last_name(),
				'address_1'  => $order->get_billing_address_1(),
				'address_2'  => $order->get_billing_address_2(),
				'company'    => $order->get_billing_company(),
				'city'       => $order->get_billing_city(),
			);
			if ( $get_address_data != $order_data ) {
				$order->add_order_note( 'The address information was changed by the customer from the get address information.', 'payer-for-woocommerce' );
			}
		}

	}

	/**
	 * Clears sessions.
	 *
	 * @return void
	 */
	private function clear_sessions() {
		WC()->session->__unset( 'payer_customer_details' );
	}
}
