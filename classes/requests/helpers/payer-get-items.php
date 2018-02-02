<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Get_Items {
    public static function get_items( $order_id ) {
        $order = wc_get_order( $order_id );
        $line_number = 0;
        $items = array();
        foreach( $order->get_items() as $item ) {
            $line_number = $line_number + 1;
            $formated_item = self::get_item( $item, $line_number );
            array_push( $items, $formated_item );
        }
        foreach( $order->get_shipping_methods() as $shipping_method ) {
            $line_number = $line_number + 1;
            $formated_shipping = self::get_shipping( $shipping_method, $line_number );
            array_push( $items, $formated_shipping );
        }
        foreach( $order->get_fees() as $fee ) {
            $line_number = $line_number + 1;
            $formated_fee = self::get_fee( $fee, $line_number );
            array_push( $items, $formated_fee );
        }
        return $items;
    }

    private static function get_item( $item, $line_number ) {

        if ( $item['variation_id'] ) {
            $product = wc_get_product( $item['variation_id'] );
            $product_id = $item['variation_id'];
        } else {
            $product = wc_get_product( $item['product_id'] );
            $product_id = $item['product_id'];
        }
        return array(
            'type'                  =>  'Freeform',
            'line_number'           =>  $line_number,
            'article_number'        =>  self::get_sku( $product, $product_id ),
            'description'           =>  $product->get_name(),
            'unit_price'            =>  wc_get_price_including_tax( $product ),
            'unit_vat_percentage'    =>  self::calculate_tax( $product ),
            'quantity'              =>  $item['qty'],
        );
    }

    private static function get_shipping( $shipping_method, $line_number ) {
        return array(
            'type'                  =>  'Freeform',
            'article_number'        =>  'Shipping',
            'line_number'           =>  $line_number,
            'description'           =>  $shipping_method->get_method_title(),
            'unit_price'            =>  $shipping_method->get_total() +  $shipping_method->get_total_tax(),
            'unit_vat_percentage'    =>  ( $shipping_method->get_total_tax() / $shipping_method->get_total() ) * 100,
            'quantity'              =>  '1',
        );
    }

    private static function get_fee( $fee, $line_number ) {
        return array(
            'type'                  =>  'Freeform',
            'article_number'        =>  'Fee',
            'line_number'           =>  $line_number,
            'description'           =>  $fee->get_name(),
            'unit_price'            =>  $fee->get_total() + $fee->get_total_tax(),
            'unit_vat_percentage'    =>  ( $fee->get_total_tax() / $fee->get_total() ) * 100,
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