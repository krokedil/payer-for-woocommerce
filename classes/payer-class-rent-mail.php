<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class WC_New_Rent_Order {
	public function __construct() {
		add_action( 'payer_send_rent_mail', array( $this, 'trigger' ) );
	}

	public function get_default_subject() {
		return __( '[{site_title}]: New rent order #{order_number}', 'payer-for-woocommerce' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int            $order_id The order ID.
	 * @param WC_Order|false $order Order object.
	 */
	public function trigger( $order_id, $order = false ) {
		$to      = 'b2b-leasing@payer.se';
		$subject = '[' . get_home_url() . '] New rent order ' . $order_id;
		$content = $this->get_content( $order_id );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		if ( false !== $content ) {
			wp_mail( $to, $subject, $content, $headers );
		}
	}

	public function get_content( $order_id ) {
		$order         = wc_get_order( $order_id );
		$gateway_used  = get_post_meta( $order_id, '_payment_method', true );
		$pno           = get_post_meta( $order_id, apply_filters( 'payer_billing_pno_meta_name', '_billing_pno' ), true );
		$html          = false;
		$subscriptions = wcs_get_subscriptions_for_order( $order );
		if ( empty( $subscriptions ) ) {
			$subscriptions = wcs_get_subscriptions_for_renewal_order( $order );
		}
		if ( ! empty( $subscriptions ) ) {
			ob_start();
		?>	
			<h2 style="color: #96588a;font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif;font-size: 18px;font-weight: bold;line-height: 130%;margin: 0 0 18px;text-align: left"><?php echo '[Order ' . $order_id . '] ' . date( DATE_RFC2822 ); ?></h2>
			<table class="td" cellspacing="0" cellpadding="6" style="width: 100%;font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;color: #636363;border: 1px solid #e5e5e5" border="1">
			<thead>
				<tr>
					<th class="td" scope="col" style="text-align: left;color: #636363;border: 1px solid #e5e5e5;padding: 12px"> <?php _e( 'Product', 'woocommerce' ); ?> </th>
					<th class="td" scope="col" style="text-align: left;color: #636363;border: 1px solid #e5e5e5;padding: 12px"> <?php _e( 'Quantity', 'woocommerce' ); ?> </th>
					<th class="td" scope="col" style="text-align: left;color: #636363;border: 1px solid #e5e5e5;padding: 12px"> <?php _e( 'Price', 'woocommerce' ); ?> </th>
				</tr>
			</thead>
			<tbody>
		<?php
		foreach ( $subscriptions as $subscription_id => $subscription_order ) {
			foreach ( $subscription_order->get_items() as $item ) {
				?>
				<tr>
					<td class="td" style="text-align: left;vertical-align: middle;border: 1px solid #eee;color: #636363;padding: 12px"> <?php echo $item->get_name() . ' (' . $this->get_product_sub_legth( $item ) . ' months)'; ?> </td>
					<td class="td" style="text-align: left;vertical-align: middle;border: 1px solid #eee;color: #636363;padding: 12px"> <?php echo $item->get_quantity(); ?> </td>
					<td class="td" style="text-align: left;vertical-align: middle;border: 1px solid #eee;color: #636363;padding: 12px"> <?php echo wc_price( $item->get_total() ); ?> </td>
				</tr>
				<?php
			}
		}
		?>
				<?php if ( $order->get_shipping_method() ) { ?>
				<tr>
					<td class="td" style="text-align: left;vertical-align: middle;border: 1px solid #eee;color: #636363;padding: 12px"> <?php echo $order->get_shipping_method(); ?> </td>
					<td class="td" style="text-align: left;vertical-align: middle;border: 1px solid #eee;color: #636363;padding: 12px"> <?php echo 1; ?> </td>
					<td class="td" style="text-align: left;vertical-align: middle;border: 1px solid #eee;color: #636363;padding: 12px"> <?php echo wc_price( $order->get_shipping_total() ); ?> </td>
				</tr>
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<th class="td" scope="row" colspan="2" style="text-align: left;color: #636363;border: 1px solid #e5e5e5;padding: 12px"> <?php _e( 'Total', 'woocommerce' ); ?> </th>
						<td class="td" style="text-align: left;color: #636363;border: 1px solid #e5e5e5;padding: 12px"> <?php echo wc_price( $order->get_total() ); ?></td>
					</tr>
				</tfoot>
			</table>
			<h2 style="color: #96588a;font-family: &quot;Helvetica Neue&quot;, Helvetica, Roboto, Arial, sans-serif;font-size: 18px;font-weight: bold;line-height: 130%;margin: 0 0 18px;text-align: left"><?php _e( 'Billing address', 'woocommerce' ); ?></h2>
			<address class="address" style="padding: 12px 12px 0;color: #636363;border: 1px solid #e5e5e5">
				<?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?>
				<br>
				<?php echo $order->get_billing_address_1(); ?>
				<br>
				<?php if ( '' !== $order->get_billing_address_2() ) { ?>
				<?php echo $order->get_billing_address_2(); ?>
				<br>
				<?php } ?>
				<?php echo $order->get_billing_postcode() . ' ' . $order->get_billing_city(); ?>
				<br>
				<?php echo $order->get_billing_phone(); ?>
				<br>
				<?php echo $order->get_billing_email(); ?>
				<br>
				<br>
				<?php echo __( 'Personal/Organisation number: ', 'payer-for-woocommerce' ) . $pno; ?>
			</address>
		<?php
		$html = ob_get_clean();
		}
		return $html;
	}

	public function get_product_sub_legth( $item ) {
		$product_id = $item->get_product_id();

		$sub_length = get_post_meta( $product_id, '_subscription_length', true );
		return $sub_length;
	}
}
new WC_New_Rent_Order();
