<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Get_Address{

    public static function get_address( $personal_number, $zip_code ) {
        $gateway = Payer_Create_Client::create_client();

        $data = array(
            'identity_number'   =>  $personal_number,
            'zip_code'          =>  $zip_code,
            'challenge_token'   =>  Payer_Create_Challenge::create_challenge(),
        );
        $get_address = new Payer\Sdk\Resource\GetAddress( $gateway );
        $get_address_response = $get_address->create( $data );

        return $get_address_response;
    }
}