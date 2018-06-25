<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Compliance with European Union's General Data Protection Regulation.
 *
 * @class    Payer_GDPR
 * @version  1.0.0
 * @package  Payer/Classes
 * @category Class
 * @author   Krokedil
 */
class Payer_GDPR {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'privacy_declarations' ) );
	}
	/**
	 * Privacy declarations.
	 *
	 * @return void
	 */
	public function privacy_declarations() {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			$content =
				__(
					'When you place an order in the webstore with Payer as the choosen payment method, ' .
					'information about the products in the order (namne, price, quantity, SKU) is sent to Payer ' .
					'together with your billing and shipping address. Payer then responds with a unique transaction ID. ' .
					'This ID is stored in the order in WooCommerce for future reference.',
					'payer-for-woocommerce'
				);
			wp_add_privacy_policy_content(
				'Payer for WooCommerce',
				wp_kses_post( wpautop( $content ) )
			);
		}
	}
}
new Payer_GDPR();