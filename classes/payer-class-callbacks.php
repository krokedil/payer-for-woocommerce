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
        $this->set_gateway();
        if( $_GET['address'] ) {
            $this->populate_order();
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

    private function populate_order() {
        
    }
}
new Payer_Callbacks;