<?php
/**
 * Admin new appointment email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-appointment.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @version     4.8.11
 * @since       3.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$text_align        = is_rtl() ? 'right' : 'left';
$appointment       = wc_appointments_maybe_appointment_object( $appointment );
$appointment       = $appointment ? $appointment : get_wc_appointment( 0 );
$appointment_order = $appointment->get_order();

if ( wc_appointment_order_requires_confirmation( $appointment_order ) && $appointment->has_status( array( 'pending-confirmation' ) ) ) {
	/* translators: 1: billing first and last name */
	$opening_paragraph = __( 'An appointment has been made by %s and is awaiting your approval. The details of this appointment are shown below.', 'woocommerce-appointments' );
} else {
	/* translators: 1: billing first and last name */
	$opening_paragraph = __( 'An new appointment has been made by %s. The details of this appointment are shown below.', 'woocommerce-appointments' );
}

do_action( 'woocommerce_email_header', $email_heading, $email );

if ( $appointment_order ) {
	if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
		$first_name = $appointment_order->billing_first_name;
		$last_name  = $appointment_order->billing_last_name;
	} else {
		$first_name = $appointment_order->get_billing_first_name();
		$last_name  = $appointment_order->get_billing_last_name();
	}
}
?>

<?php if ( $appointment_order && ! empty( $first_name ) && ! empty( $last_name ) ) : ?>
	<p><?php printf( $opening_paragraph, wp_kses_post( $first_name . ' ' . $last_name ) ); // WPCS: XSS ok. ?></p>
<?php endif; ?>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; margin:0 0 16px;" border="1">
	<tbody>
		<tr>
			<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Scheduled Product', 'woocommerce-appointments' ); ?></th>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( $appointment->get_product_name() ); ?></td>
		</tr>
		<tr>
			<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Appointment ID', 'woocommerce-appointments' ); ?></th>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo esc_attr( $appointment->get_id() ); ?></td>
		</tr>
		<tr>
			<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Appointment Date', 'woocommerce-appointments' ); ?></th>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo esc_attr( $appointment->get_start_date() ); ?></td>
		</tr>
		<tr>
			<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Appointment Duration', 'woocommerce-appointments' ); ?></th>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo esc_attr( $appointment->get_duration() ); ?></td>
		</tr>
		<?php $staff = $appointment->get_staff_members( true ); ?>
		<?php if ( $appointment->has_staff() && $staff ) : ?>
			<tr>
				<th class="td" scope="row" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Appointment Providers', 'woocommerce-appointments' ); ?></th>
				<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo esc_attr( $staff ); ?></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php if ( wc_appointment_order_requires_confirmation( $appointment_order ) && $appointment->has_status( array( 'pending-confirmation' ) ) ) : ?>
<p><?php esc_html_e( 'This appointment is awaiting your approval. Please check it and inform the customer if the date is available or not.', 'woocommerce-appointments' ); ?></p>
<?php endif; ?>

<p>
<?php
/* translators: 1: a href to appointment */
echo make_clickable( sprintf( esc_html__( 'You can view and edit this appointment in the dashboard here: %s', 'woocommerce-appointments' ), admin_url( 'post.php?post=' . $appointment->get_id() . '&action=edit' ) ) ); // WPCS: XSS ok.
?>
</p>

<?php $wc_order = $appointment->get_order(); ?>
<?php if ( $wc_order ) : ?>

	<?php do_action( 'woocommerce_email_before_order_table', $wc_order, $sent_to_admin, $plain_text, $email ); ?>

	<br />
	<h2>
	<?php

	$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
	if ( $pre_wc_30 ) {
		$order_date = $wc_order->order_date;
	} else {
		$order_date = $wc_order->get_date_created() ? $wc_order->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
	}

	echo esc_html__( 'Order', 'woocommerce-appointments' ) . ': #' . esc_html( $wc_order->get_order_number() );
	?>
	(
	<?php
	printf( '<time datetime="%s">%s</time>', esc_attr( date_i18n( 'c', strtotime( $order_date ) ) ), esc_attr( date_i18n( wc_date_format(), strtotime( $order_date ) ) ) );
	?>
	)</h2>

	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; margin:0 0 16px;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce-appointments' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'woocommerce-appointments' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'woocommerce-appointments' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			switch ( $wc_order->get_status() ) {
				case 'completed':
					echo $pre_wc_30 ? $wc_order->email_order_items_table( array( 'show_sku' => false ) ) : wc_get_email_order_items( $wc_order, array( 'show_sku' => false ) ); // WPCS: XSS ok.
					break;
				case 'processing':
				default:
					echo $pre_wc_30 ? $wc_order->email_order_items_table( array( 'show_sku' => true ) ) : wc_get_email_order_items( $wc_order, array( 'show_sku' => true ) ); // WPCS: XSS ok.
					break;
			}
			?>
		</tbody>
		<tfoot>
			<?php
			$order_totals = $wc_order->get_order_item_totals();
			if ( $order_totals ) {
				$i = 0;
				foreach ( $order_totals as $order_total ) {
					$i++;
					?>
					<tr>
						<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $order_total['label'] ); ?></th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $order_total['value'] ); ?></td>
					</tr>
					<?php
				}
			}
			?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_email_after_order_table', $wc_order, $sent_to_admin, $plain_text, $email ); ?>

	<?php do_action( 'woocommerce_email_order_meta', $wc_order, $sent_to_admin, $plain_text, $email ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
