<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Gets a Payer Customer.
 *
 * @class    Payer_Get_Customer
 * @package  Payer/Classes/Requests/Helpers
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Get_Customer {
	/**
	 * Gets customer
	 *
	 * @param int $order_id
	 * @return array
	 */
	public static function get_customer( $order_id, $pno ) {
		$order = wc_get_order( $order_id );

		if ( null === $pno ) {
			$pno = ( null === get_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), true ) ) ? '' : get_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), true );
		}

		return array(
			'id'              => ( null === get_post_meta( $order_id, '_customer_user', true ) ) ? '' : get_post_meta( $order_id, '_customer_user', true ),
			'identity_number' => $pno,
			'organisation'    => ( false == $order->get_billing_company() ) ? '' : $order->get_billing_company(),
			'your_reference'  => '',
			'first_name'      => ( false == $order->get_billing_first_name() ) ? '' : $order->get_billing_first_name(),
			'last_name'       => ( false == $order->get_billing_last_name() ) ? '' : $order->get_billing_last_name(),
			'address'         => self::get_address( $order ),
			'zip_code'        => ( false == $order->get_billing_postcode() ) ? '' : $order->get_billing_postcode(),
			'city'            => ( false == $order->get_billing_city() ) ? '' : $order->get_billing_city(),
			'country_code'    => ( false == $order->get_billing_country() ) ? '' : $order->get_billing_country(),
			'email'           => ( false == $order->get_billing_email() ) ? '' : $order->get_billing_email(),
			'phone'           => self::get_phone( $order ),
		);
	}

	/**
	 * Gets address
	 *
	 * @param array $order
	 * @return array
	 */
	private static function get_address( $order ) {
		return array(
			'address_1' => ( false == $order->get_billing_address_1() ) ? '' : $order->get_billing_address_1(),
			'address_2' => ( false == $order->get_billing_address_2() ) ? '' : $order->get_billing_address_2(),
			'co'        => '',
		);
	}

	/**
	 * Gets phone
	 *
	 * @param array $order
	 * @return array
	 */
	private static function get_phone( $order ) {
		return array(
			'home'   => '',
			'mobile' => ( false == $order->get_billing_phone() ) ? '' : $order->get_billing_phone(),
			'work'   => '',
		);
	}
}
