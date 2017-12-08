<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Create_Client {

	private static $agent_id;
	private static $soap_username;
	private static $soap_password;
	private static $post_key1;
	private static $post_key2;

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

	private static function set_variables() {
		$payer_settings = get_option( 'woocommerce_payer_card_payment_settings'  );
		self::$agent_id = $payer_settings['payer_agent_id'];
		self::$soap_username = $payer_settings['payer_soap_id'];
		self::$soap_password = $payer_settings['payer_password'];
		self::$post_key1 = $payer_settings['payer_post_key_1'];
		self::$post_key2 = $payer_settings['payer_post_key_2'];
	}
}