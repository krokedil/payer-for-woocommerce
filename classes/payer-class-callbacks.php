<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Callbacks {

    private $gateway;

    public function __construct() {
        // Add action for listener.
		add_action( 'woocommerce_api_payer_gateway', array( $this, 'payer_listener' ) );
    }

    public function payer_listener() {
        $callback_type = $_GET['payer_callback_type'];
        $order_id = $_GET['payer_merchant_reference_id'];
        $order = wc_get_order( $order_id );
        $used_gateway = $_GET['payer_payment_type'];
        if ( isset( $_GET['payer_added_fee'] ) ) {
            $payer_added_fee = $_GET['payer_added_fee'];
        }
        Payer_For_Woocommerce::log( 'Payer Callback: '. $order_id . ' $_GET: ' . var_export( $_GET, true ) );
        switch ( $callback_type ) {
            case 'auth':
                $this->authorize_reply( $order_id );
                break;
            case 'settle':
                $this->maybe_add_fee( $payer_added_fee, $order_id );
                $this->maybe_update_gateway( $order_id, $used_gateway );                
                $order->payment_complete( $order_id, $used_gateway );
                $this->settlement_reply( $order_id );
                break;
        }
    }

    private function settlement_reply( $order_id ) {
        update_post_meta( $order_id, '_payer_payment_id', $_GET['payer_payment_id'] );        
        $this->set_gateway();
        if( isset( $_GET['address'] ) ) {
            $this->populate_customer_data( $order_id, $_GET['address'] );
        }        
        $data = array(
            'payment'   =>  Payer_Get_Payment::get_payment( $order_id ),
            'purchase'  =>  Payer_Get_Purchase::get_purchase( $order_id ),
        );
        $purchase = new Payer\Sdk\Resource\Purchase( $this->gateway );
        $purchase->createSettlementResource( $data );
    }

    private function authorize_reply( $order_id ) {
        $this->set_gateway();        
        $data = array(
            'payment'   =>  Payer_Get_Payment::get_payment( $order_id ),
            'purchase'  =>  Payer_Get_Purchase::get_purchase( $order_id ),
        );
        $purchase = new Payer\Sdk\Resource\Purchase( $this->gateway );
        $purchase->createAuthorizeResource( $data );
    }

    private function maybe_add_fee( $payer_added_fee, $order_id ) {
        $payer_added_fee = doubleval( $payer_added_fee );
        $total_tax = apply_filters( 'payer_tax_rate', 0.25 ) * $payer_added_fee;
        if( $payer_added_fee !== 0 ) {
            $fee = array(
                'name'      => __( 'Payer Added Fees', 'payer-for-woocommerce' ),
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

    private function maybe_update_gateway( $order_id, $used_gateway ) {
        $order = wc_get_order( $order_id );
        $starting_gateway = $order->get_payment_method();

        switch( $used_gateway ) {
            case 'card' :
                $used_gateway = 'payer_card_payment';
                break;
            case 'bank' :
                $used_gateway = 'payer_bank_payment';
                break;
            case 'invoice' :
                $used_gateway = 'payer_invoice_payment';
                break;
            case 'installment' :
                $used_gateway = 'payer_installment_payment';
                break;
            case 'swish' :
                $used_gateway = 'payer_swish_payment';
                break;
            case 'einvoice' :
                $used_gateway = 'payer_einvoice_payment';
                break;
            default :
                $used_gateway = 'payer_card_payment';
                break;
        }

        if( $used_gateway !== $starting_gateway ) {
            $available_gateways = WC()->payment_gateways->payment_gateways();
            $payment_method = $available_gateways[ $used_gateway ];
            $order->set_payment_method( $payment_method );
            $order->save();
        }
    }

    private function set_gateway() {
        $this->gateway = Payer_Create_Client::create_client();
    }

    private function populate_customer_data( $order_id, $address ) {
        $address = base64_decode( $address );
        $address = json_decode( utf8_decode( $address ) );
        error_log( var_export( $address, true ) );
        $order = wc_get_order( $order_id );

        $order->set_billing_first_name( sanitize_text_field( $address->firstName ) );
		$order->set_billing_last_name( sanitize_text_field( $address->lastName ) );
		$order->set_billing_country( sanitize_text_field( $address->countryId ) );
		$order->set_billing_address_1( sanitize_text_field( $address->address1 ) );
		$order->set_billing_address_2( sanitize_text_field( $address->address2 ) );
		//$order->set_billing_city( sanitize_text_field( $address->city ) );
		$order->set_billing_postcode( sanitize_text_field( $address->postalCode  ) );
		$order->set_billing_phone( sanitize_text_field( $address->phone ) );
        $order->set_billing_email( sanitize_text_field( $address->email ) );
        
        $order->save();
    }
}
new Payer_Callbacks;