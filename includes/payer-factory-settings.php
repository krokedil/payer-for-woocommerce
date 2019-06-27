<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$settings = array(
	'enabled'     => array(
		'title'   => __( 'Enable/Disable', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable ' . $this->method_title, 'payer-for-woocommerce' ),
		'default' => 'no',
	),
	'title'       => array(
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
);

if ( $this->id === 'payer_rent_payment' ) {
	$settings['payer_rent_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['payer_rent_notice'] = array(
		'title' => __( 'To use this payment method you need to contact Payer first. If you do not contact payer before hand and get approval, your payments will not go through.', 'payer-for-woocommerce' ),
		'type'  => 'title',
	);
}

if ( $this->id === 'payer_masterpass' ) {
	$settings['instant_masterpass_checkout']      = array(
		'title'   => __( 'Instant MasterPass checkout', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable instant MasterPass checkout for products and cart page.', 'payer-for-woocommerce' ),
		'default' => 'no',
	);
	$settings['masterpass_instant_purchase_text'] = array(
		'title'       => __( 'Instant purchase text', 'payer-for-woocommerce' ),
		'type'        => 'textarea',
		'desc_tip'    => true,
		'description' => __( 'Add an optional text after the Instant purchase button.', 'payer-for-woocommerce' ),
		'default'     => '',
	);
	$settings['masterpass_campaign']              = array(
		'title'   => __( 'MasterPass Campaign', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable this setting to partake in a MasterPass campaign that is ongoing.', 'payer-for-woocommerce' ),
		'default' => 'no',
	);
	$settings['masterpass_campaign_text']         = array(
		'title'   => __( 'MasterPass Campaign text', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable this setting to show campaign information text to customer.', 'payer-for-woocommerce' ),
		'default' => 'no',
	);
	$settings['masterpass_campaign_amount']       = array(
		'title'   => __( 'Discount amount for campaign', 'payer-for-woocommerce' ),
		'type'    => 'number',
		'label'   => __( 'Enter the amount to be discounted for the current campaign.', 'payer-for-woocommerce' ),
		'default' => '0',
	);
}

if ( $this->id === 'payer_bank_payment' ) {
	$settings['payer_bank_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}

if ( $this->id === 'payer_direct_invoice_gateway' ) {
	$settings['payer_direct_invoice_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}

if ( $this->id === 'payer_einvoice_payment' ) {
	$settings['payer_einvoice_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}

if ( $this->id === 'payer_installment_payment' ) {
	$settings['payer_installment_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}
if ( $this->id === 'payer_invoice_payment' ) {
	$settings['payer_invoice_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}

if ( $this->id === 'payer_masterpass' ) {
	$settings['payer_masterpass_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}

if ( $this->id === 'payer_swish_payment' ) {
	$settings['payer_swish_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}

if ( $this->id === 'payer_card_payment' ) {
	$settings['payer_card_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);
}

if ( $this->id === 'payer_card_payment' ) {
	$settings['payer_card_payment_icon'] = array(
		'title'       => __( 'Icon URL', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Put the URL to a new icon image here', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['payer_key_section'] = array(
		'title'       => __( 'Payer credentials', 'payer-for-woocommerce' ),
		'type'        => 'title',
		'description' => __( 'The key values can be found under the Settings/Account section in <a href="https://secure.payer.se/adminweb/inloggning/inloggning.php"> Payer Administration</a>. Contact Payer if you haven\'t got access to your login credentials', 'payer-for-woocommerce' ),
	);

	$settings['payer_agent_id'] = array(
		'title'       => __( 'Agent ID', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Enter your Payer Agent ID', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['payer_soap_id'] = array(
		'title'       => __( 'Soap ID', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Enter your Payer SOAP ID', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['payer_password'] = array(
		'title'       => __( 'Soap Password', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Enter your Payer Password', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['payer_post_key_1'] = array(
		'title'       => __( 'Post key 1', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Enter your Payer Post key 1', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['payer_post_key_2'] = array(
		'title'       => __( 'Post key 2', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Enter your Payer Post key 2', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['payer_rest_key_1'] = array(
		'title'       => __( 'Rest key', 'payer-for-woocommerce' ),
		'type'        => 'text',
		'description' => __( 'Enter your Payer Rest key', 'payer-for-woocommerce' ),
		'default'     => '',
		'desc_tip'    => true,
	);

	$settings['order_management'] = array(
		'title'   => __( 'Enable order management', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable order management for Payer.', 'payer-for-woocommerce' ),
		'default' => 'no',
	);

	$settings['get_address'] = array(
		'title'   => __( 'Enable get address', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable get address for Payer (SE only).', 'payer-for-woocommerce' ),
		'default' => 'yes',
	);

	$settings['is_proxy'] = array(
		'title'   => __( 'Proxy', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable if you are using a proxy to prevent callback issues.', 'payer-for-woocommerce' ),
		'default' => 'no',
	);

	$settings['skip_ip_validation'] = array(
		'title'   => __( 'Skip IP Validation', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable if you want to skip IP Validation. Notice that turning off the validation may be a high security risk. Please contact Payer Administration for further recommendations.', 'payer-for-woocommerce' ),
		'default' => 'no',
	);

	$settings['test_mode_settings_title'] = array(
		'title' => __( 'Test Mode Settings', 'payer-for-woocommerce' ),
		'type'  => 'title',
	);

	$settings['test_mode'] = array(
		'title'   => __( 'Test mode', 'payer-for-woocommerce' ),
		'type'    => 'checkbox',
		'label'   => __( 'Enable Test mode for Payer.', 'payer-for-woocommerce' ),
		'default' => 'yes',
	);

	$settings['debug_mode'] = array(
		'title'       => __( 'Debug', 'payer-for-woocommerce' ),
		'type'        => 'checkbox',
		'label'       => __( 'Enable logging.', 'payer-for-woocommerce' ),
		'description' => sprintf( __( 'Log Payer events, in <code>%s</code>', 'payer-for-woocommerce' ), wc_get_log_file_path( 'payer' ) ),
		'default'     => 'no',
	);
} else {
	$settings['payer_factory_notice'] = array(
		'title' => __( 'Put credentials in the Payer Card Payments settings.', 'payer-for-woocommerce' ),
		'type'  => 'title',
	);
}

return apply_filters( 'payer_factory_settings', $settings );
