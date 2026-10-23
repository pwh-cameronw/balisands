<?php
/**
 * The "Get a quote" Gravity Form: created by setup, rendered by the quote widget.
 *
 * @package BaliSands
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID of the quote form, or 0 when Gravity Forms is missing or the form was deleted.
 *
 * @return int
 */
function bs_quote_form_id() {
	if ( ! class_exists( 'GFAPI' ) ) {
		return 0;
	}
	$id = (int) get_option( 'bs_quote_form_id' );
	if ( $id && GFAPI::get_form( $id ) ) {
		return $id;
	}
	return 0;
}

/**
 * Create the quote form if it does not exist yet.
 *
 * @return int|WP_Error Form ID.
 */
function bs_create_quote_form() {
	if ( ! class_exists( 'GFAPI' ) ) {
		return new WP_Error( 'bs_no_gf', 'Gravity Forms is not active.' );
	}
	$existing = bs_quote_form_id();
	if ( $existing ) {
		return $existing;
	}

	$half = array( 'layoutGridColumnSpan' => 6 );
	$form = array(
		'title'          => 'Get a quote',
		'description'    => '',
		'labelPlacement' => 'top_label',
		'cssClass'       => 'bs-quote-form',
		'button'         => array(
			'type' => 'text',
			'text' => 'Send enquiry',
		),
		'fields'         => array(
			array_merge( array( 'id' => 1, 'type' => 'text', 'label' => 'Your name', 'placeholder' => 'Your name', 'isRequired' => true ), $half ),
			array_merge( array( 'id' => 2, 'type' => 'phone', 'label' => 'Phone', 'placeholder' => 'Phone', 'phoneFormat' => 'international', 'isRequired' => true ), $half ),
			array_merge( array( 'id' => 3, 'type' => 'email', 'label' => 'Email', 'placeholder' => 'Email', 'isRequired' => true ), $half ),
			array_merge( array( 'id' => 4, 'type' => 'text', 'label' => 'Delivery address (optional)', 'placeholder' => 'Delivery address (optional)' ), $half ),
			array(
				'id'          => 5,
				'type'        => 'select',
				'label'       => 'What are you after?',
				'placeholder' => 'What are you after? Supplies, imports or both',
				'choices'     => array(
					array( 'text' => 'Landscape supplies', 'value' => 'Landscape supplies' ),
					array( 'text' => 'Bali imports', 'value' => 'Bali imports' ),
					array( 'text' => 'Both', 'value' => 'Both' ),
				),
			),
			array(
				'id'          => 6,
				'type'        => 'textarea',
				'label'       => 'Tell us about the job',
				'placeholder' => 'Tell us about the job',
				'isRequired'  => true,
				'size'        => 'small',
			),
		),
		'notifications'  => array(
			'bs_yard' => array(
				'id'       => 'bs_yard',
				'name'     => 'Yard notification',
				'isActive' => true,
				'event'    => 'form_submission',
				'toType'   => 'email',
				'to'       => '{admin_email}',
				'replyTo'  => '{Email:3}',
				'fromName' => 'Bali Sands website',
				'from'     => '{admin_email}',
				'subject'  => 'Quote request from {Your name:1}',
				'message'  => '{all_fields}',
			),
		),
		'confirmations'  => array(
			'bs_thanks' => array(
				'id'        => 'bs_thanks',
				'name'      => 'Default Confirmation',
				'isDefault' => true,
				'type'      => 'message',
				'message'   => "Thanks, we've got your enquiry and we'll be in touch soon. Need it sooner? Call the yard on " . bs_yard( 'bs_phone' ) . '.',
			),
		),
	);

	$id = GFAPI::add_form( $form );
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	update_option( 'bs_quote_form_id', (int) $id );
	return (int) $id;
}

add_filter( 'gform_notification', 'bs_quote_notification_to', 10, 3 );

/**
 * Send quote notifications to the enquiry email from Yard details.
 *
 * @param array $notification Notification.
 * @param array $form         Form.
 * @return array
 */
function bs_quote_notification_to( $notification, $form ) {
	if ( (int) $form['id'] === (int) get_option( 'bs_quote_form_id' ) && 'bs_yard' === ( $notification['id'] ?? '' ) ) {
		$email = bs_yard( 'bs_email' );
		if ( is_email( $email ) ) {
			$notification['toType'] = 'email';
			$notification['to']     = $email;
		}
	}
	return $notification;
}
