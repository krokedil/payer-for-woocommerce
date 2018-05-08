<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Creates Payer refund data.
 * 
 * @class    Payer_Create_Refund_Data
 * @package  Payer/Classes/Requests/Helpers
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Create_Refund_Data {
    /**
     * Creates refund data
     *
     * @param int $order_id
     * @param int $amount
     * @param string $reason
     * @param string $payment_id
     * @return array
     */
    public static function create_refund_data( $order_id, $amount, $reason, $payment_id ) {

        $refund_id = self::get_refunded_order( $order_id );
        if( '' === $reason ) {
            $reason = 'N/A';
        }
        if( null !== $refund_id ) {
            $data = array(
                'transaction_id'    =>  get_post_meta( $order_id, '_payer_payment_id', true ),
                'reason'            =>  $reason,
                'amount'            =>  $amount,
                'vat_percentage'    =>  self::calculate_tax( $refund_id ),
            );
        }

        update_post_meta( $refund_id, '_krokedil_refunded', 'true' );
        return $data;
    }

    /**
     * Gets refunded order
     *
     * @param int $order_id
     * @return string
     */
    private static function get_refunded_order( $order_id ) {
        $query_args = array(
            'fields'         => 'id=>parent',
            'post_type'      => 'shop_order_refund',
            'post_status'    => 'any',
            'posts_per_page' => -1,
        );

        $refunds = get_posts( $query_args );

        $refund_id = array_search( $order_id, $refunds );

        if( is_array( $refund_id ) ) {
            foreach( $refund_id as $key => $value ) {
                if( ! get_post_meta( $value, '_krokedil_refunded' ) ) {
                    $refund_id = $value;
                    break;
                }
            }
        }

        return $refund_id;
    }

    /**
     * Calculates tax.
     *
     * @param string $refund_id
     * @return void
     */
    private static function calculate_tax( $refund_id ) {
        $refund_order = wc_get_order( $refund_id );
        $refund_tax_total = $refund_order->get_total_tax() * -1;
        $refund_total = ( $refund_order->get_total() * -1 ) - $refund_tax_total;
        return intval( ( $refund_tax_total / $refund_total ) * 100 );
    }
}