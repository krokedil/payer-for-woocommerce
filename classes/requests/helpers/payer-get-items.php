<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Get_Items {
    public static function get_items( $order_id ) {
        $order = wc_get_order( $order_id );
        $items = array();
        foreach( $order->get_items() as $item ) {
            $formated_item = self::get_item( $item );
            array_push( $items, $formated_item );
        }
        foreach( $order->get_shipping_methods() as $shipping_method ) {
            $formated_shipping = self::get_shipping( $shipping_method );
            array_push( $items, $formated_shipping );
        }
        foreach( $order->get_fees() as $fee ) {
            $formated_fee = self::get_fee( $fee );
            array_push( $items, $formated_fee );
        }
        return $items;
    }

    private static function get_item( $item ) {
        $product = $item->get_product();
        
        if ( $item['variation_id'] ) {
            $product_id = $item['variation_id'];
        } else {
            $product_id = $item['product_id'];
        }
        
        return array(
            'type'                  =>  'Freeform',
            'article_number'        =>  self::get_sku( $product, $product_id ),
            'description'           =>  $product->get_name(),
            'unit_price'            =>  ( $item->get_total() + $item->get_total_tax() ) / $item['qty'],
            'unit_vat_percetage'    =>  self::calculate_tax( $product ),
            'quantity'              =>  $item['qty'],
        );
    }

    private static function get_shipping( $shipping_method ) {
        return array(
            'type'                  =>  'Freeform',
            'article_number'        =>  'Shipping',
            'description'           =>  $shipping_method->get_method_title(),
            'unit_price'            =>  $shipping_method->get_total() +  $shipping_method->get_total_tax(),
            'unit_vat_percetage'    =>  ( $shipping_method->get_total_tax() / $shipping_method->get_total() ) * 100,
            'quantity'              =>  '1',
        );
    }

    private static function get_fee( $fee ) {
        return array(
            'type'                  =>  'Freeform',
            'article_number'        =>  'Fee',
            'description'           =>  $fee->get_name(),
            'unit_price'            =>  $fee->get_total() + $fee->get_total_tax(),
            'unit_vat_percetage'    =>  ( $fee->get_total_tax() / $fee->get_total() ) * 100,
            'quantity'              =>  '1',
        );
    }

    private static function calculate_tax( $product ) {
        $price_incl_tax = wc_get_price_including_tax( $product );
        $price_excl_tax = wc_get_price_excluding_tax( $product );
        $price_difference = $price_incl_tax - $price_excl_tax;
        $tax_percent = intval( ( $price_difference / $price_excl_tax ) * 100 );

        return $tax_percent;
    }

    private static function get_sku( $product, $product_id ) {
        if ( get_post_meta( $product_id, '_sku', true ) !== '' ) {
            $part_number = $product->get_sku();
        } else {
            $part_number = $product->get_id();
        }
        return substr( $part_number, 0, 32 );
    }
}