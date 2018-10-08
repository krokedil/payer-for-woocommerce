<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Returns error messages depending on
 *
 * @class    Payer_Admin_Notices
 * @package  Payer/Classes
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class Payer_Admin_Notices {
	/**
	 * The reference the *Singleton* instance of this class.
	 *
	 * @var $instance
	 */
	protected static $instance;
	/**
	 * The settings.
	 *
	 * @var $settings
	 */
	protected $settings;
	/**
	 * Checks if Payer gateway is enabled.
	 *
	 * @var $enabled
	 */
	protected $enabled;
	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return self::$instance The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Class constructor.
	 */
	public function __construct() {
		$settings       = get_option( 'woocommerce_payer_card_payment_settings' );
		$this->enabled  = $settings['enabled'];
		$this->settings = $settings;
		add_action( 'admin_init', array( $this, 'check_payer_settings' ) );
	}

	/**
	 * Check if all settings are configured.
	 */
	public function check_payer_settings() {
		if ( 'yes' !== $this->enabled ) {
			return;
		}

		$check = true;

		$settings_to_check = array( 'payer_agent_id', 'payer_soap_id', 'payer_password', 'payer_post_key_1', 'payer_post_key_2' );
		$settings          = $this->settings;
		$failed_checkes    = array();
		foreach ( $settings as $key => $value ) {
			if ( in_array( $key, $settings_to_check, true ) ) {
				if ( '' === $value ) {
					$check           = false;
					$failed_checks[] = $key;
				}
			}
		}

		if ( false === $check ) {
			$failed_checks_string = '';
			foreach ( $failed_checks as $failed_check ) {
				$failed_checks_string = $failed_checks_string . $failed_check . ', ';
			}
			$failed_checks_string = substr_replace( $failed_checks_string, '', -2 );
			echo '<div class="notice notice-error">';
			// translators: Used to show the missing credentials for the Payer API.
			echo '<p>' . esc_html( sprintf( __( 'Not all credentials are filled in. Missed credentials: %s', 'payer-for-woocommerce' ), $failed_checks_string ) ) . '</p>';
			echo '</div>';
		}
	}
}
Payer_Admin_Notices::get_instance();
