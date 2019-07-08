<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handles callback from Payer
 *
 * @class    Payer_Callbacks
 * @package  Payer/Classes
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Callbacks {

	/**
	 * Gateway
	 *
	 * @var string
	 */
	private $gateway;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		// Add action for listener.
		add_action( 'woocommerce_api_payer_gateway', array( $this, 'payer_listener' ) );
	}

	/**
	 * Callback listener.
	 *
	 * @return void
	 */
	public function payer_listener() {
		$callback_type = $_GET['payer_callback_type'];
		$order_id      = $_GET['payer_merchant_reference_id'];
		$order         = wc_get_order( $order_id );
		$used_gateway  = $_GET['payer_payment_type'];
		if ( isset( $_GET['payer_added_fee'] ) ) {
			$payer_added_fee = $_GET['payer_added_fee'];
		}
		if ( isset( $_GET['payer_unique_id'] ) ) {
			$recurring_token = $_GET['payer_unique_id'];
			update_post_meta( $order_id, 'payer_recurring_token', $recurring_token );
		}
		Payer_For_Woocommerce::log( 'Payer Callback: ' . $order_id . ' $_GET: ' . var_export( $_GET, true ) );
		/**
		 * Add comment.
		 */
		switch ( $callback_type ) {
			case 'auth':
				krokedil_log_events( $order_id, 'Payer Auth Callback', $_GET );
				$this->authorize_reply( $order_id );
				break;
			case 'store':
				krokedil_log_events( $order_id, 'Payer Store Callback', $_GET );
				$order->payment_complete( $order_id, $used_gateway );
				$this->authorize_reply( $order_id );
				break;
			case 'settle':
				krokedil_log_events( $order_id, 'Payer Settlement Callback', $_GET );
				$this->maybe_add_fee( $payer_added_fee, $order_id );
				$this->maybe_update_gateway( $order_id, $used_gateway );
				$order->payment_complete( $order_id, $used_gateway );
				$this->settlement_reply( $order_id );
				break;
		}
	}

	/**
	 * Creates a reply for a settlement callback.
	 *
	 * @param int $order_id
	 * @return void
	 */
	private function settlement_reply( $order_id ) {
		update_post_meta( $order_id, '_payer_payment_id', $_GET['payer_payment_id'] );
		$this->set_gateway();
		if ( isset( $_GET['address'] ) ) {
			$this->populate_customer_data( $order_id, $_GET['address'] );
		}
		$settings           = get_option( 'woocommerce_payer_card_payment_settings' );
		$is_proxy           = false;
		$skip_ip_validation = false;
		if ( isset( $settings['is_proxy'] ) && 'yes' === $settings['is_proxy'] ) {
			$is_proxy = true;
		}
		if ( isset( $settings['skip_ip_validation'] ) && 'yes' === $settings['skip_ip_validation'] ) {
			$skip_ip_validation = true;
		}

		$data     = apply_filters(
			'payer_callback_data', array(
				'payment'            => Payer_Get_Payment::get_payment( $order_id ),
				'purchase'           => Payer_Get_Purchase::get_purchase( $order_id ),
				'is_proxy'           => $is_proxy,
				'skip_ip_validation' => $skip_ip_validation,
			)
		);
		$purchase = new Payer\Sdk\Resource\Purchase( $this->gateway );
		$response = $purchase->createSettlementResource( $data );
		krokedil_log_response( $order_id, $response );
	}

	/**
	 * Creates a reply for a authorize/store callback.
	 *
	 * @param int $order_id
	 * @return void
	 */
	private function authorize_reply( $order_id ) {
		$this->set_gateway();
		$settings           = get_option( 'woocommerce_payer_card_payment_settings' );
		$is_proxy           = false;
		$skip_ip_validation = false;
		if ( isset( $settings['is_proxy'] ) && 'yes' === $settings['is_proxy'] ) {
			$is_proxy = true;
		}
		if ( isset( $settings['skip_ip_validation'] ) && 'yes' === $settings['skip_ip_validation'] ) {
			$skip_ip_validation = true;
		}

		$data     = apply_filters(
			'payer_callback_data', array(
				'payment'            => Payer_Get_Payment::get_payment( $order_id ),
				'purchase'           => Payer_Get_Purchase::get_purchase( $order_id ),
				'is_proxy'           => $is_proxy,
				'skip_ip_validation' => $skip_ip_validation,
			)
		);
		$purchase = new Payer\Sdk\Resource\Purchase( $this->gateway );
		$response = $purchase->createAuthorizeResource( $data );
		krokedil_log_response( $order_id, $response );
	}

	private function maybe_add_fee( $payer_added_fee, $order_id ) {
		$payer_added_fee = doubleval( $payer_added_fee );
		$total_tax       = apply_filters( 'payer_tax_rate', 0.25 ) * $payer_added_fee;
		if ( $payer_added_fee !== 0 ) {
			$fee   = array(
				'name'       => __( 'Payer Added Fees', 'payer-for-woocommerce' ),
				'tax_class'  => '',
				'tax_status' => '',
				'total'      => $payer_added_fee,
				'total_tax'  => $total_tax,
				'taxes'      => '',
			);
			$order = wc_get_order( $order_id );
			$order->add_item( $fee );
			$order->save();
		}
	}

	/**
	 * Maybe update the gateway used for the order.
	 *
	 * @param int    $order_id
	 * @param string $used_gateway
	 * @return void
	 */
	private function maybe_update_gateway( $order_id, $used_gateway ) {
		$order            = wc_get_order( $order_id );
		$starting_gateway = $order->get_payment_method();

		switch ( $used_gateway ) {
			case 'card':
				$used_gateway = 'payer_card_payment';
				break;
			case 'bank':
				$used_gateway = 'payer_bank_payment';
				break;
			case 'invoice':
				$used_gateway = 'payer_invoice_payment';
				break;
			case 'installment':
				$used_gateway = 'payer_installment_payment';
				break;
			case 'swish':
				$used_gateway = 'payer_swish_payment';
				break;
			case 'einvoice':
				$used_gateway = 'payer_einvoice_payment';
				break;
			default:
				$used_gateway = 'payer_card_payment';
				break;
		}

		if ( $used_gateway !== $starting_gateway ) {
			$available_gateways = WC()->payment_gateways->payment_gateways();
			$payment_method     = $available_gateways[ $used_gateway ];
			$order->set_payment_method( $payment_method );
			$order->save();
		}
	}

	/**
	 * Sets the gateway for the order.
	 *
	 * @return void
	 */
	private function set_gateway() {
		$this->gateway = Payer_Create_Client::create_client();
	}

	/**
	 * Populates the order with customer data from callback.
	 *
	 * @param int    $order_id
	 * @param string $address
	 * @return void
	 */
	private function populate_customer_data( $order_id, $address ) {
		$address = base64_decode( $address );
		$address = json_decode( utf8_decode( $address ) );
		$order   = wc_get_order( $order_id );

		$order->set_billing_first_name( sanitize_text_field( $address->firstName ) );
		$order->set_billing_last_name( sanitize_text_field( $address->lastName ) );
		$order->set_billing_country( sanitize_text_field( $address->countryId ) );
		$order->set_billing_address_1( sanitize_text_field( $address->address1 ) );
		$order->set_billing_address_2( sanitize_text_field( $address->address2 ) );
		$order->set_billing_city( sanitize_text_field( $address->city ) );
		$order->set_billing_postcode( sanitize_text_field( $address->postalCode ) );
		$order->set_billing_phone( sanitize_text_field( $address->phone ) );
		$order->set_billing_email( sanitize_text_field( $address->email ) );

		$order->save();
	}
}
new Payer_Callbacks();
