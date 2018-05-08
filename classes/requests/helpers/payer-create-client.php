<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Creates a Payer Client.
 * 
 * @class    Payer_Create_Client
 * @package  Payer/Classes/Requests/Helpers
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Create_Client {

	/**
	 * Agent id
	 *
	 * @var int
	 */
	private static $agent_id;
	/**
	 * Soap Username
	 *
	 * @var string
	 */
	private static $soap_username;
	/**
	 * Soap Password
	 *
	 * @var string
	 */
	private static $soap_password;
	/**
	 * Post Key 1
	 *
	 * @var string
	 */
	private static $post_key1;
	/**
	 * Post key 2
	 *
	 * @var string
	 */
	private static $post_key2;

	/**
	 * Creates client.
	 *
	 * @return array
	 */
	public static function create_client() {
		self::set_variables();
		$credentials = array(
			'agent_id'  =>  self::$agent_id,
			'soap'		=>	array(
				'username'  =>  self::$soap_username,
				'password'  =>  self::$soap_password,
			),
			'post'		=>	array(
				'key_1'     =>  self::$post_key1,
				'key_2'     =>  self::$post_key2,
			),
		);

		return Payer\Sdk\Client::create( $credentials );
	}

	/**
	 * Sets needed variables.
	 *
	 * @return void
	 */
	private static function set_variables() {
		$payer_settings = get_option( 'woocommerce_payer_card_payment_settings'  );
		self::$agent_id = $payer_settings['payer_agent_id'];
		self::$soap_username = $payer_settings['payer_soap_id'];
		self::$soap_password = $payer_settings['payer_password'];
		self::$post_key1 = $payer_settings['payer_post_key_1'];
		self::$post_key2 = $payer_settings['payer_post_key_2'];
	}
}