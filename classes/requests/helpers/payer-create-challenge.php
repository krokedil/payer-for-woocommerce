<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Payer_Create_Challenge{
    public static function create_challenge() {
        $gateway = Payer_Create_Client::create_client();
        $challenge = new Payer\Sdk\Resource\Challenge( $gateway );

        $challenge_response = $challenge->create();

        return $challenge_response['challenge_token'];
    }
}