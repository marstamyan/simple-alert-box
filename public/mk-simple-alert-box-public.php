<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add custom styles to the page dynamically
function mk_simple_alert_box_get_dynamic_styles() {
	$info_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_info_color', '#bfdcef' ) );
	$warning_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_warning_color', '#f5eecb' ) );
	$success_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_success_color', '#d1f2c4' ) );
	$error_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_error_color', '#f0cccc' ) );

	$info_text_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_info_text_color', '#457389' ) );
	$warning_text_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_warning_text_color', '#8a6d3b' ) );
	$success_text_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_success_text_color', '#3c763d' ) );
	$error_text_color = sanitize_hex_color( get_option( 'mk_simple_alert_box_error_text_color', '#a94442' ) );

	$padding = intval( get_option( 'mk_simple_alert_box_padding', '10' ) );
	$margin = intval( get_option( 'mk_simple_alert_box_margin', '10' ) );
	$font_size = intval( get_option( 'mk_simple_alert_box_font_size', '16' ) );

	// Generate CSS styles for the alert boxes
	$custom_css = "
    .mk-simple-alert-box {
        padding: {$padding}px;
        margin: {$margin}px 0;
        font-size: {$font_size}px;
        border-left: 5px solid;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .mk-simple-alert-box-info {
        background-color: {$info_color};
        color: {$info_text_color};
        border-color: #31708f;
    }
    .mk-simple-alert-box-warning {
        background-color: {$warning_color};
        color: {$warning_text_color};
        border-color: #b88d42;
    }
    .mk-simple-alert-box-success {
        background-color: {$success_color};
        color: {$success_text_color};
        border-color: #53a354;
    }
    .mk-simple-alert-box-error {
        background-color: {$error_color};
        color: {$error_text_color};
        border-color: #db5552;
    }";

	// Output the styles in the page header
	echo '<style>' . $custom_css . '</style>';
}

// Add the custom styles to the <head> of the page
add_action( 'wp_head', 'mk_simple_alert_box_get_dynamic_styles' );

// Shortcodes
function mk_simple_alert_boxes_info_shortcode( $atts, $content = null ) {
	return '<div class="mk-simple-alert-box mk-simple-alert-box-info">' . esc_html( do_shortcode( $content ) ) . '</div>';
}

add_shortcode( 'mk_alert_info', 'mk_simple_alert_boxes_info_shortcode' );

function mk_simple_alert_boxes_warning_shortcode( $atts, $content = null ) {
	return '<div class="mk-simple-alert-box mk-simple-alert-box-warning">' . esc_html( do_shortcode( $content ) ) . '</div>';
}

add_shortcode( 'mk_alert_warning', 'mk_simple_alert_boxes_warning_shortcode' );

function mk_simple_alert_boxes_success_shortcode( $atts, $content = null ) {
	return '<div class="mk-simple-alert-box mk-simple-alert-box-success">' . esc_html( do_shortcode( $content ) ) . '</div>';
}

add_shortcode( 'mk_alert_success', 'mk_simple_alert_boxes_success_shortcode' );

function mk_simple_alert_boxes_error_shortcode( $atts, $content = null ) {
	return '<div class="mk-simple-alert-box mk-simple-alert-box-error">' . esc_html( do_shortcode( $content ) ) . '</div>';
}

add_shortcode( 'mk_alert_error', 'mk_simple_alert_boxes_error_shortcode' );
