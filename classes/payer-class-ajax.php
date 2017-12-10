<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Ajax extends WC_AJAX { 
    public static function init() {
		self::add_ajax_events();
    }
    
    public static function add_ajax_events() {
		$ajax_events = array(
			'get_address' => true,
		);
		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				// WC AJAX can be used for frontend ajax requests.
				add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
    }
    
    public static function get_address() {
        $personal_number = $_POST['personal_number'];
        $zip_code = $_POST['zip_code'];

        $payer_address_information = Payer_Get_Address::get_address( $personal_number, $zip_code );
        wp_send_json_success( $payer_address_information );
        wp_die();
    }
}
Payer_Ajax::init();