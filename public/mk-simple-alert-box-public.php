<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return CSS using validated values, including on sites upgraded from 1.0.
 */
function mk_simple_alert_box_get_dynamic_styles() {
	$padding   = mk_simple_alert_box_get_option( 'mk_simple_alert_box_padding' );
	$margin    = mk_simple_alert_box_get_option( 'mk_simple_alert_box_margin' );
	$font_size = mk_simple_alert_box_get_option( 'mk_simple_alert_box_font_size' );
	$css       = ".mk-simple-alert-box {
		box-sizing: border-box;
		padding: {$padding}px;
		margin: {$margin}px 0;
		font-size: {$font_size}px;
		border-inline-start: 5px solid;
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	}
	.mk-simple-alert-box > :first-child {
		margin-top: 0;
	}
	.mk-simple-alert-box > :last-child {
		margin-bottom: 0;
	}";

	$borders = array( 'info' => '#31708f', 'warning' => '#b88d42', 'success' => '#53a354', 'error' => '#db5552' );
	foreach ( $borders as $type => $border ) {
		$background = mk_simple_alert_box_get_option( 'mk_simple_alert_box_' . $type . '_color' );
		$text       = mk_simple_alert_box_get_option( 'mk_simple_alert_box_' . $type . '_text_color' );
		$css       .= "\n.mk-simple-alert-box-{$type} {
			background-color: {$background};
			color: {$text};
			border-color: {$border};
		}";
	}

	return $css;
}

function mk_simple_alert_box_enqueue_styles() {
	if ( wp_style_is( 'mk-simple-alert-box', 'enqueued' ) ) {
		return;
	}

	wp_register_style( 'mk-simple-alert-box', false, array(), MK_SIMPLE_ALERT_BOX_VERSION );
	wp_enqueue_style( 'mk-simple-alert-box' );
	wp_add_inline_style( 'mk-simple-alert-box', mk_simple_alert_box_get_dynamic_styles() );
}
// Load on all frontend pages so alerts in widgets and templates are covered too.
add_action( 'wp_enqueue_scripts', 'mk_simple_alert_box_enqueue_styles' );

function mk_simple_alert_box_render( $type, $content ) {
	$content = is_string( $content ) ? $content : '';
	// Repair paragraph fragments from the classic editor, and format raw block content.
	$content = shortcode_unautop( wpautop( $content ) );
	return '<div class="mk-simple-alert-box mk-simple-alert-box-' . esc_attr( $type ) . '">' . wp_kses_post( do_shortcode( $content ) ) . '</div>';
}

function mk_simple_alert_boxes_info_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'info', $content );
}
add_shortcode( 'mk_alert_info', 'mk_simple_alert_boxes_info_shortcode' );

function mk_simple_alert_boxes_warning_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'warning', $content );
}
add_shortcode( 'mk_alert_warning', 'mk_simple_alert_boxes_warning_shortcode' );

function mk_simple_alert_boxes_success_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'success', $content );
}
add_shortcode( 'mk_alert_success', 'mk_simple_alert_boxes_success_shortcode' );

function mk_simple_alert_boxes_error_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'error', $content );
}
add_shortcode( 'mk_alert_error', 'mk_simple_alert_boxes_error_shortcode' );
