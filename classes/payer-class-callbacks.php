<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Callbacks {

    private $gateway;

    public function __construct() {
        $this->set_gateway();

        // Add action for listener.
		add_action( 'woocommerce_api_payer_gateway', array( $this, 'payer_listener' ) );

    }

    public function payer_listener() {
        $callback_type = $_GET['payer_callback_type'];
        $order_id = $_GET['payer_merchant_reference_id'];
        if ( isset( $_GET['payer_added_fee'] ) ) {
            $payer_added_fee = $_GET['payer_added_fee'];
        }
        switch ( $callback_type ) {
            case 'auth':
                $this->authorize_reply( $order_id );
                break;
            case 'settle':
                $this->maybe_add_fee( $payer_added_fee, $order_id );
                $this->payment_complete( $order_id );
                $this->settlement_reply( $order_id );
                break;
        }
    }

    private function settlement_reply( $order_id ) {
        $data = array(
            'payment'   =>  Payer_Get_Payment::get_payment( $order_id ),
            'purchase'  =>  Payer_Get_Purchase::get_purchase( $order_id ),
        );
        $purchase = new Payer\Sdk\Resource\Purchase( $this->gateway );
        $purchase->createSettlementResource( $data );
    }

    private function authorize_reply( $order_id ) {
        $data = array(
            'payment'   =>  Payer_Get_Payment::get_payment( $order_id ),
            'purchase'  =>  Payer_Get_Purchase::get_purchase( $order_id ),
        );
        $purchase = new Payer\Sdk\Resource\Purchase( $this->gateway );
        $purchase->createAuthorizeResource( $data );
    }

    private function maybe_add_fee( $payer_added_fee, $order_id ) {
        $payer_added_fee = doubleval( $payer_added_fee );
        if( $payer_added_fee !== 0 ) {
            $fee = array(
                'name'      => __( 'Payer Added Fees', 'payer-for-woocommerce' ),
                'tax_class'  => '',
                'tax_status' => '',
                'total'      => $payer_added_fee,
                'total_tax'  => '',
                'taxes'      => '',
            );
            $order = wc_get_order( $order_id );
            $order->add_item( $fee );
        }
    }

    private function payment_complete( $order_id ) {
        $order = wc_get_order( $order_id );
        $order->payment_complete();
    }

    private function set_gateway() {
        $this->gateway = Payer_Create_Client::create_client( 'payer_card_payment' );
    }
    
}
new Payer_Callbacks;