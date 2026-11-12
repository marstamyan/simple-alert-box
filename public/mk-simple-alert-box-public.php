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
	}
	.mk-simple-alert-box-header {
		display: flex;
		align-items: baseline;
		gap: 0.5em;
		margin-bottom: 0.5em;
		overflow-wrap: anywhere;
	}
	.mk-simple-alert-box-header:last-child {
		margin-bottom: 0;
	}
	.mk-simple-alert-box-title {
		font-weight: 700;
	}
	.mk-simple-alert-box-icon {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		flex: 0 0 auto;
		width: 1.25em;
		height: 1.25em;
		border: 1.5px solid currentColor;
		border-radius: 50%;
		font-family: Arial, sans-serif;
		font-weight: 700;
		line-height: 1;
	}
	.mk-simple-alert-box-type {
		position: absolute;
		width: 1px;
		height: 1px;
		padding: 0;
		margin: -1px;
		overflow: hidden;
		clip: rect(0, 0, 0, 0);
		white-space: nowrap;
		border: 0;
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

/** Type labels also provide a text equivalent for the decorative icons. */
function mk_simple_alert_box_get_types() {
	return array(
		'info'    => array( 'label' => __( 'Information', 'simple-alert-box' ), 'icon' => 'i' ),
		'warning' => array( 'label' => __( 'Warning', 'simple-alert-box' ), 'icon' => '!' ),
		'success' => array( 'label' => __( 'Success', 'simple-alert-box' ), 'icon' => '✓' ),
		'error'   => array( 'label' => __( 'Error', 'simple-alert-box' ), 'icon' => '×' ),
	);
}

function mk_simple_alert_box_render( $type, $content, $atts = array(), $default_icon = false ) {
	$types = mk_simple_alert_box_get_types();
	$type  = is_string( $type ) && isset( $types[ $type ] ) ? $type : 'info';
	$atts  = shortcode_atts( array( 'title' => '', 'icon' => $default_icon ), $atts );
	$title = is_string( $atts['title'] ) ? sanitize_text_field( $atts['title'] ) : '';
	$icon  = filter_var( $atts['icon'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	$icon  = null === $icon ? $default_icon : $icon;
	$head  = '';
	if ( $icon || '' !== $title ) {
		$head = '<div class="mk-simple-alert-box-header">';
		if ( $icon ) {
			$head .= '<span class="mk-simple-alert-box-icon" aria-hidden="true">' . esc_html( $types[ $type ]['icon'] ) . '</span>';
			$head .= '<span class="mk-simple-alert-box-type">' . esc_html( $types[ $type ]['label'] ) . ': </span>';
		}
		if ( '' !== $title ) {
			$head .= '<strong class="mk-simple-alert-box-title">' . esc_html( $title ) . '</strong>';
		}
		$head .= '</div>';
	}

	$content = is_string( $content ) ? $content : '';
	// Repair paragraph fragments from the classic editor, and format raw block content.
	$content = shortcode_unautop( wpautop( $content ) );
	return '<div class="mk-simple-alert-box mk-simple-alert-box-' . esc_attr( $type ) . '">' . $head . wp_kses_post( do_shortcode( $content ) ) . '</div>';
}

function mk_simple_alert_box_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( array( 'type' => 'info', 'title' => '', 'icon' => true ), $atts, 'mk_alert' );
	return mk_simple_alert_box_render( $atts['type'], $content, $atts, true );
}
add_shortcode( 'mk_alert', 'mk_simple_alert_box_shortcode' );

function mk_simple_alert_boxes_info_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'info', $content, $atts );
}
add_shortcode( 'mk_alert_info', 'mk_simple_alert_boxes_info_shortcode' );

function mk_simple_alert_boxes_warning_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'warning', $content, $atts );
}
add_shortcode( 'mk_alert_warning', 'mk_simple_alert_boxes_warning_shortcode' );

function mk_simple_alert_boxes_success_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'success', $content, $atts );
}
add_shortcode( 'mk_alert_success', 'mk_simple_alert_boxes_success_shortcode' );

function mk_simple_alert_boxes_error_shortcode( $atts, $content = null ) {
	return mk_simple_alert_box_render( 'error', $content, $atts );
}
add_shortcode( 'mk_alert_error', 'mk_simple_alert_boxes_error_shortcode' );
