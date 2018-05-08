<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Masterpass Gateway.
 * 
 * @class    Payer_Masterpass_Gateway
 * @package  Payer/Classes/Gateways
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Masterpass_Gateway extends Payer_Factory_Gateway {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->id                   = 'payer_masterpass';
		$this->method_title         = __( 'Payer Masterpass', 'payer-for-woocommerce' );
		$this->method_description   = __( 'Allows payments through ' . $this->method_title . '.', 'payer-for-woocommerce' );

		$this->title       		    = $this->get_option( 'title' );
		$this->description 		    = $this->get_option( 'description' );
		$this->test_mode            = $this->get_option( 'test_mode' );
		$this->debug_mode           = $this->get_option( 'debug_mode' );
		$this->icon_url				= $this->get_option( 'payer_masterpass_icon' );		
		$this->icon					= $this->set_icon();

		// Load the form fields.
		$this->init_form_fields();
		// Load the settings.
		$this->init_settings();

		$this->supports = array(
			'products',
		);

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_filter( 'woocommerce_page_wc-settings', array( $this, 'show_keys_in_settings' ) );		
	}

	/**
	 * Shows settings on the settings page.
	 *
	 * @return void
	 */
	public function show_keys_in_settings() {
		if ( isset( $_GET['section'] ) ) {
			if ( $this->id === $_GET['section'] ) {
				payer_show_credentials_form();
			}
		}
	}
}

add_filter( 'woocommerce_payment_gateways', 'add_krokedil_payer_masterpass_gateway' );
/**
 * Registers the gateway.
 *
 * @param array $methods
 * @return array
 */
function add_krokedil_payer_masterpass_gateway( $methods ) {
	if ( ! defined( 'UNSET_PAYER_MASTERPASS_PAYMENTS' ) ) {
		$methods[] = 'Payer_Masterpass_Gateway';
	}
	return $methods;
}