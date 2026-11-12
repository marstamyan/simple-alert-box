<?php
/**
 * Shared settings definitions and validation, including legacy database values.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mk_simple_alert_box_get_settings() {
	return array(
		'mk_simple_alert_box_info_color'         => array( 'type' => 'string', 'default' => '#bfdcef', 'label' => __( 'Info Box Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_info_text_color'    => array( 'type' => 'string', 'default' => '#31586d', 'label' => __( 'Info Text Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_warning_color'      => array( 'type' => 'string', 'default' => '#f5eecb', 'label' => __( 'Warning Box Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_warning_text_color' => array( 'type' => 'string', 'default' => '#755a2b', 'label' => __( 'Warning Text Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_success_color'      => array( 'type' => 'string', 'default' => '#d1f2c4', 'label' => __( 'Success Box Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_success_text_color' => array( 'type' => 'string', 'default' => '#356936', 'label' => __( 'Success Text Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_error_color'        => array( 'type' => 'string', 'default' => '#f0cccc', 'label' => __( 'Error Box Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_error_text_color'   => array( 'type' => 'string', 'default' => '#943b39', 'label' => __( 'Error Text Color', 'simple-alert-box' ) ),
		'mk_simple_alert_box_padding'            => array( 'type' => 'integer', 'default' => 10, 'min' => 0, 'max' => 1000, 'label' => __( 'Padding', 'simple-alert-box' ) ),
		'mk_simple_alert_box_margin'             => array( 'type' => 'integer', 'default' => 10, 'min' => 0, 'max' => 1000, 'label' => __( 'Margin', 'simple-alert-box' ) ),
		'mk_simple_alert_box_font_size'          => array( 'type' => 'integer', 'default' => 16, 'min' => 1, 'max' => 200, 'label' => __( 'Font Size', 'simple-alert-box' ) ),
	);
}

/**
 * Return a valid value, or null for invalid input. Never cast arrays or objects.
 */
function mk_simple_alert_box_normalize_setting( $value, $setting ) {
	if ( 'string' === $setting['type'] ) {
		if ( ! is_string( $value ) ) {
			return null;
		}
		$color = sanitize_hex_color( trim( $value ) );
		return $color ? $color : null;
	}

	if ( ! is_int( $value ) && ! is_string( $value ) ) {
		return null;
	}

	$value = trim( (string) $value );
	if ( ! preg_match( '/^[0-9]+$/D', $value ) || $value < $setting['min'] || $value > $setting['max'] ) {
		return null;
	}

	return (int) $value;
}

/**
 * Apply defaults even when an older installation has saved unusable values.
 */
function mk_simple_alert_box_get_option( $option_name ) {
	$settings = mk_simple_alert_box_get_settings();
	if ( ! isset( $settings[ $option_name ] ) ) {
		return null;
	}

	$setting = $settings[ $option_name ];
	$value   = mk_simple_alert_box_normalize_setting( get_option( $option_name, $setting['default'] ), $setting );
	return null === $value ? $setting['default'] : $value;
}

/**
 * Reject invalid changes without losing the last usable setting.
 */
function mk_simple_alert_box_sanitize_setting( $input, $option_name ) {
	$settings = mk_simple_alert_box_get_settings();
	if ( ! isset( $settings[ $option_name ] ) ) {
		return null;
	}

	$setting = $settings[ $option_name ];
	$value   = mk_simple_alert_box_normalize_setting( $input, $setting );
	if ( null !== $value ) {
		return $value;
	}

	if ( 'string' === $setting['type'] ) {
		/* translators: %s: setting label. */
		$message = sprintf( __( 'Enter a color in #RGB or #RRGGBB format for %s. The previous valid value has been kept.', 'simple-alert-box' ), $setting['label'] );
	} else {
		/* translators: 1: minimum value, 2: maximum value, 3: setting label. */
		$message = sprintf( __( 'Enter a whole number from %1$d to %2$d for %3$s. The previous valid value has been kept.', 'simple-alert-box' ), $setting['min'], $setting['max'], $setting['label'] );
	}

	add_settings_error( 'mk_simple_alert_box_options', $option_name . '_invalid', $message );
	return mk_simple_alert_box_get_option( $option_name );
}
