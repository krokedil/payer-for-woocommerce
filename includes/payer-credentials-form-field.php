<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Shows credential form on settings page.
 */
function payer_show_credentials_form() {
    $payer_card_settings = get_option( 'woocommerce_payer_card_payment_settings' );
    // Setting values.
    $agent_id = $payer_card_settings['payer_agent_id'];
    $soap_id = $payer_card_settings['payer_soap_id'];
    $soap_password = $payer_card_settings['payer_password'];
    $post_key_1 = $payer_card_settings['payer_post_key_1'];
    $post_key_2 = $payer_card_settings['payer_post_key_2'];
    $rest_key = $payer_card_settings['payer_rest_key_1'];
    ?>
    <h2><?php _e( 'Payer credentials:', 'payer-for-woocommerce' ); ?></h2>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="payer_agent_id"><?php _e( 'Agent ID: ', 'payer-for-woocommerce' ); ?></label>
                </th>
                <td class="forminp">
                    <input class="input-text regular-input" type="text" value="<?php _e( $agent_id, 'payer-for-woocommerce' ); ?>" name="payer_agent_id" disabled></br>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="payer_soap_id"><?php _e( 'Soap ID: ', 'payer-for-woocommerce' ); ?></label>
                </th>
                <td class="forminp">
                    <input class="input-text regular-input" type="text" value="<?php _e( $soap_id, 'payer-for-woocommerce' ); ?>" name="payer_soap_id" disabled></br>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="payer_soap_password"><?php _e( 'Soap Password: ', 'payer-for-woocommerce' ); ?></label>
                </th>
                <td class="forminp">
                    <input class="input-text regular-input" type="text" value="<?php _e( $soap_password, 'payer-for-woocommerce' ); ?>" name="payer_soap_password" disabled></br>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="payer_post_key_1"><?php _e( 'Post key 1: ', 'payer-for-woocommerce' ); ?></label>
                </th>
                <td class="forminp">
                    <input class="input-text regular-input" type="text" value="<?php _e( $post_key_1, 'payer-for-woocommerce' ); ?>" name="payer_post_key_1" disabled></br>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="payer_post_key_2"><?php _e( 'Post key 2: ', 'payer-for-woocommerce' ); ?></label>
                </th>
                <td class="forminp">
                    <input class="input-text regular-input" type="text" value="<?php _e( $post_key_2, 'payer-for-woocommerce' ); ?>" name="payer_post_key_2" disabled></br>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="payer_rest_key"><?php _e( 'Rest key: ', 'payer-for-woocommerce' ); ?></label>
                </th>
                <td class="forminp">
                    <input class="input-text regular-input" type="text" value="<?php _e( $rest_key, 'payer-for-woocommerce' ); ?>" name="payer_rest_key" disabled></br>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}