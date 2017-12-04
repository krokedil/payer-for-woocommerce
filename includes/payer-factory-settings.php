<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

return apply_filters( 'payer_factory_settings',
	array(
		'enabled' => array(
			'title'   => __( 'Enable/Disable', 'payer-for-woocommerce' ),
			'type'    => 'checkbox',
			'label'   => __( 'Enable ' . $this->method_title, 'payer-for-woocommerce' ),
			'default' => 'no',
		),
		'title' => array(
			'title'       => __( 'Title', 'payer-for-woocommerce' ),
			'type'        => 'text',
			'description' => __( 'This controls the title which the user sees during checkout.', 'payer-for-woocommerce' ),
			'default'     => __( $this->method_title, 'payer-for-woocommerce' ),
			'desc_tip'    => true,
		),
		'description' => array(
			'title'       => __( 'Description', 'payer-for-woocommerce' ),
			'type'        => 'textarea',
			'desc_tip'    => true,
			'description' => __( 'This controls the description which the user sees during checkout.', 'payer-for-woocommerce' ),
		),
		'payer_agent_id'  => array(
			'title'         => __( 'Agent ID', 'payer-for-woocommerce' ),
			'type'          => 'text',
			'description'   => __( 'Enter your Payer Agent ID', 'payer-for-woocommerce' ),
			'default'       => '',
			'desc_tip'      => true,
		),
		'payer_soap_id'  => array(
			'title'         => __( 'SOAP ID', 'payer-for-woocommerce' ),
			'type'          => 'text',
			'description'   => __( 'Enter your Payer SOAP ID', 'payer-for-woocommerce' ),
			'default'       => '',
			'desc_tip'      => true,
		),
		'payer_password'     => array(
			'title'         => __( 'Soap Password', 'payer-for-woocommerce' ),
			'type'          => 'text',
			'description'   => __( 'Enter your Payer Password', 'payer-for-woocommerce' ),
			'default'       => '',
			'desc_tip'      => true,
		),
		'payer_post_key_1'     => array(
			'title'         => __( 'Post key 1', 'payer-for-woocommerce' ),
			'type'          => 'text',
			'description'   => __( 'Enter your Payer Post key 1', 'payer-for-woocommerce' ),
			'default'       => '',
			'desc_tip'      => true,
		),
		'payer_post_key_2'     => array(
			'title'         => __( 'Post key 2', 'payer-for-woocommerce' ),
			'type'          => 'text',
			'description'   => __( 'Enter your Payer Post key 2', 'payer-for-woocommerce' ),
			'default'       => '',
			'desc_tip'      => true,
		),
		'test_mode_settings_title' => array(
			'title' => __( 'Test Mode Settings', 'payer-for-woocommerce' ),
			'type'  => 'title',
		),
		'test_mode'         => array(
			'title'         => __( 'Test mode', 'payer-for-woocommerce' ),
			'type'          => 'checkbox',
			'label'         => __( 'Enable Test mode for Payer.', 'payer-for-woocommerce' ),
			'default'       => 'no',
		),
		'debug_mode'       	=> array(
			'title'         => __( 'Debug', 'payer-for-woocommerce' ),
			'type'          => 'checkbox',
			'label'       	=> __( 'Enable logging.', 'payer-for-woocommerce' ),
			'description' 	=> sprintf( __( 'Log Payer events, in <code>%s</code>', 'payer-for-woocommerce' ), wc_get_log_file_path( 'payer' ) ),
			'default'       => 'no',
		),
	)
);