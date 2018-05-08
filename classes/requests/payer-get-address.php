<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Gets the address for SE customers.
 * 
 * @class    Payer_Create_Purchase
 * @package  Payer/Classes/Requests
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Get_Address{

    /**
     * Gets address
     *
     * @param string $personal_number
     * @param string $zip_code
     * @return array
     */
    public static function get_address( $personal_number, $zip_code ) {
        $gateway = Payer_Create_Client::create_client();

        $data = array(
            'identity_number'   =>  $personal_number,
            'zip_code'          =>  $zip_code,
            'challenge_token'   =>  Payer_Create_Challenge::create_challenge(),
        );
        krokedil_log_events( null, 'Payer Get Address Request', $data );        
        $get_address = new Payer\Sdk\Resource\GetAddress( $gateway );
        $get_address_response = $get_address->create( $data );

        return $get_address_response;
    }
}