<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Create_Refund_Data {
    public static function create_refund_data( $order_id, $amount, $reason, $payment_id ) {

        $refund_id = self::get_refunded_order( $order_id );
        if( '' === $reason ) {
            $reason = 'N/A';
        }
        if( null !== $refund_id ) {
            $data = array(
                'transaction_id'    =>  $payment_id,
                'reason'            =>  $reason,
                'amount'            =>  $amount,
                'vat_percentage'    =>  self::calculate_tax( $refund_id ),
            );
        }
        return $data;
    }

    private static function get_refunded_order( $order_id ) {
        $query_args = array(
            'fields'         => 'id=>parent',
            'post_type'      => 'shop_order_refund',
            'post_status'    => 'any',
            'posts_per_page' => -1,
        );

        $refunds = get_posts( $query_args );

        return array_search( $order_id, $refunds );
    }

    private static function calculate_tax( $refund_id ) {
        $refund_order = wc_get_order( $refund_id );
        $refund_tax_total = $refund_order->get_total_tax() * -1;
        $refund_total = ( $refund_order->get_total() * -1 ) - $refund_tax_total;
        return intval( ( $refund_tax_total / $refund_total ) * 100 );
    }
}