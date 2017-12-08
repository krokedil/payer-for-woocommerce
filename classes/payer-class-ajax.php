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
			'get_adress' => true,
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
    
    public static function get_adress() {
        error_log('hello');
        $personal_number = $_POST['personal_number'];

        $return = array(
            'personal_number' => $personal_number,
        );

        wp_send_json_success( $return );
        wp_die();
    }
}
Payer_Ajax::init();