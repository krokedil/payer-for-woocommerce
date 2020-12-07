<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Gets Order Items.
 *
 * @class    Payer_Get_Items
 * @package  Payer/Classes/Requests/Helpers
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Get_Items {
	/**
	 * Gets items.
	 *
	 * @param int $order_id
	 * @return array
	 */
	public static function get_items( $order_id ) {
		$order       = wc_get_order( $order_id );
		$line_number = 0;
		$items       = array();
		foreach ( $order->get_items() as $item ) {
			$line_number   = $line_number + 1;
			$formated_item = self::get_item( $item, $line_number, $order );
			array_push( $items, $formated_item );
		}
		foreach ( $order->get_shipping_methods() as $shipping_method ) {
			$line_number       = $line_number + 1;
			$formated_shipping = self::get_shipping( $shipping_method, $line_number );
			array_push( $items, $formated_shipping );
		}
		foreach ( $order->get_fees() as $fee ) {
			$line_number  = $line_number + 1;
			$formated_fee = self::get_fee( $fee, $line_number );
			array_push( $items, $formated_fee );
		}
		return $items;
	}

	/**
	 * Gets single item.
	 *
	 * @param array $item
	 * @return array
	 */
	private static function get_item( $item, $line_number, $order ) {
		$product = $item->get_product();

		if ( $item['variation_id'] ) {
			$product_id = $item['variation_id'];
		} else {
			$product_id = $item['product_id'];
		}
		return array(
			'type'                => 'Freeform',
			'line_number'         => $line_number,
			'article_number'      => self::get_sku( $product, $product_id ),
			'description'         => $product->get_name(),
			'unit_price'          => ( $item->get_total() + $item->get_total_tax() ) / $item['qty'],
			'unit_vat_percentage' => self::calculate_tax( $order, $item ),
			'quantity'            => $item['qty'],
		);
	}

	/**
	 * Gets shipping
	 *
	 * @param string $shipping_method
	 * @param int    $line_number
	 * @return array
	 */
	private static function get_shipping( $shipping_method, $line_number ) {
		$free_shipping = false;
		if ( 0 === intval( $shipping_method->get_total() ) ) {
			$free_shipping = true;
		}

		return array(
			'type'                => 'Freeform',
			'article_number'      => 'Shipping',
			'line_number'         => $line_number,
			'description'         => $shipping_method->get_method_title(),
			'unit_price'          => ( $free_shipping ) ? 0 : $shipping_method->get_total() + $shipping_method->get_total_tax(),
			'unit_vat_percentage' => ( $free_shipping ) ? 0 : ( $shipping_method->get_total_tax() / $shipping_method->get_total() ) * 100,
			'quantity'            => '1',
		);
	}

	/**
	 * Gets order Fee.
	 *
	 * @param array $fee
	 * @param int   $line_number
	 * @return array
	 */
	private static function get_fee( $fee, $line_number ) {
		return array(
			'type'                => 'Freeform',
			'article_number'      => 'Fee',
			'line_number'         => $line_number,
			'description'         => $fee->get_name(),
			'unit_price'          => $fee->get_total() + $fee->get_total_tax(),
			'unit_vat_percentage' => ( $fee->get_total_tax() / $fee->get_total() ) * 100,
			'quantity'            => '1',
		);
	}

	/**
	 * Calculates tax
	 *
	 * @param array $product
	 * @return int
	 */
	private static function calculate_tax( $order, $order_item ) {
		$tax_items = $order->get_items( 'tax' );
		foreach ( $tax_items as $tax_item ) {
			$rate_id = $tax_item->get_rate_id();
			foreach ( $order_item->get_taxes()['total'] as $key => $value ) {
				if ( '' !== $value ) {
					if ( $rate_id === $key ) {
						return round( WC_Tax::_get_tax_rate( $rate_id )['tax_rate'] );
					}
				}
			}
		}
		// If we get here, there is no tax set for the order item. Return zero.
		return 0;
	}

	/**
	 * Gets SKU
	 *
	 * @param array $product
	 * @param int   $product_id
	 * @return string
	 */
	private static function get_sku( $product, $product_id ) {
		if ( get_post_meta( $product_id, '_sku', true ) !== '' ) {
			$part_number = $product->get_sku();
		} else {
			$part_number = $product->get_id();
		}
		return substr( $part_number, 0, 32 );
	}
}
