<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Creates a Payer Chanllenge.
 * 
 * @class    Payer_Create_Challenge
 * @package  Payer/Classes/Requests/Helpers
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Create_Challenge{
    /**
     * Creates Challenge
     *
     * @return string
     */
    public static function create_challenge() {
        $gateway = Payer_Create_Client::create_client();
        $challenge = new Payer\Sdk\Resource\Challenge( $gateway );

        $challenge_response = $challenge->create();

        return $challenge_response['challenge_token'];
    }
}