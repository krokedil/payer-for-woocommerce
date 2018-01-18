<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function payer_autoload() {
    $files = payer_get_files_from_dir();
    $files = payer_get_value_froms_array( $files );
    error_log( var_export( $files, true ) );
    foreach( $files as $key => $value ) {
        include_once( $value );
    }
}

function payer_get_files_from_dir( $directory = PAYER_PLUGIN_DIR ) {

    $result = array();

    $scan = scandir( $directory );
    foreach( $scan as $key => $value ) { 
        if( ! in_array( $value, array( '.', '..', 'vendor' ) ) ) {
            if ( is_dir( $directory . DIRECTORY_SEPARATOR . $value ) ) {
                $result[] = payer_get_files_from_dir( $directory . DIRECTORY_SEPARATOR . $value );
            } 
            else {
                if( strpos( $value, '.php' ) && ( ! strpos( $value, 'payer-autoloader.php' ) && ! strpos( $value, 'payer-for-woocommerce.php' ) && ! strpos( $value, 'payer-factory-settings.php' ) ) ) {
                    $result[] = $directory . DIRECTORY_SEPARATOR . $value; 
                }
            } 
        } 
    }
    return $result; 
}

function payer_get_value_froms_array( $array ) {
    $values = array();
    foreach ( $array as $key => $value ) {
        if( is_array( $value ) ) {
            $values = array_merge( $values, payer_get_value_froms_array( $value ) );
        } else {
            $values[] = $value;
        }
    }
    return $values;
}