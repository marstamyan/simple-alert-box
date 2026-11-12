<?php
/**
 * Run against a disposable WordPress installation:
 * MK_ALERT_TESTS=1 wp eval-file tests/integration.php --use-include --path=/path/to/wordpress
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI || '1' !== getenv( 'MK_ALERT_TESTS' ) ) {
	throw new RuntimeException( 'Use WP-CLI with MK_ALERT_TESTS=1 on a disposable WordPress installation.' );
}

if ( ! function_exists( 'mk_simple_alert_box_get_settings' ) ) {
	throw new RuntimeException( 'Activate Simple Alert Boxes before running the tests.' );
}

require_once ABSPATH . 'wp-admin/includes/admin.php';
if ( ! function_exists( 'mk_simple_alert_box_register_settings' ) ) {
	require_once dirname( __DIR__ ) . '/admin/mk-simple-alert-box-admin.php';
}

function mk_alert_test_assert( $condition, $message ) {
	global $mk_alert_test_assertions;
	++$mk_alert_test_assertions;
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function mk_alert_test_equal( $expected, $actual, $message ) {
	mk_alert_test_assert( $expected === $actual, $message . ' Expected ' . var_export( $expected, true ) . ', got ' . var_export( $actual, true ) );
}

/** Calculate relative luminance independently of the plugin's CSS generation. */
function mk_alert_test_luminance( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$channels = array();
	foreach ( str_split( $hex, 2 ) as $channel ) {
		$srgb       = hexdec( $channel ) / 255;
		$channels[] = $srgb <= 0.04045 ? $srgb / 12.92 : pow( ( $srgb + 0.055 ) / 1.055, 2.4 );
	}
	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

global $mk_alert_test_assertions;
$mk_alert_test_assertions = 0;
$settings                 = mk_simple_alert_box_get_settings();
$original_options         = array();
$missing                  = new stdClass();
$original_user            = get_current_user_id();
$created_user             = 0;

foreach ( $settings as $name => $setting ) {
	$original_options[ $name ] = get_option( $name, $missing );
}

// Fail on runtime diagnostics, including warnings emitted by core APIs called by the plugin.
set_error_handler(
	function ( $severity, $message, $file, $line ) {
		if ( error_reporting() & $severity ) {
			throw new ErrorException( $message, 0, $severity, $file, $line );
		}
		return false;
	}
);

try {
	mk_simple_alert_box_register_settings();
	foreach ( $settings as $name => $setting ) {
		delete_option( $name );
		mk_alert_test_equal( $setting['default'], mk_simple_alert_box_get_option( $name ), $name . ' has a usable default.' );
	}
	foreach ( array( 'info', 'warning', 'success', 'error' ) as $type ) {
		$background = mk_alert_test_luminance( $settings[ 'mk_simple_alert_box_' . $type . '_color' ]['default'] );
		$foreground = mk_alert_test_luminance( $settings[ 'mk_simple_alert_box_' . $type . '_text_color' ]['default'] );
		$contrast   = ( max( $background, $foreground ) + 0.05 ) / ( min( $background, $foreground ) + 0.05 );
		mk_alert_test_assert( $contrast >= 4.5, $type . ' default text/background contrast meets 4.5:1 for normal text; measured ' . round( $contrast, 2 ) . ':1.' );
	}

	foreach ( array( 'info', 'warning', 'success', 'error' ) as $type ) {
		$tag = 'mk_alert_' . $type;
		mk_alert_test_assert( shortcode_exists( $tag ), $tag . ' is registered.' );
		$html = do_shortcode( '[' . $tag . ']<strong>Message &amp; text</strong>[/' . $tag . ']' );
		mk_alert_test_assert( false !== strpos( $html, 'mk-simple-alert-box-' . $type ), $tag . ' renders the correct box.' );
		mk_alert_test_assert( false !== strpos( $html, '<strong>Message &amp; text</strong>' ), $tag . ' preserves safe formatting and entities.' );
		mk_alert_test_assert( false === strpos( $html, '&amp;amp;' ), $tag . ' does not double-escape entities.' );
		mk_alert_test_assert( false !== strpos( do_shortcode( '[' . $tag . ']' ), 'mk-simple-alert-box-' . $type ), $tag . ' accepts empty content without warnings.' );
		mk_alert_test_assert( false !== strpos( do_shortcode( '[' . $tag . ' /]' ), 'mk-simple-alert-box-' . $type ), $tag . ' accepts self-closing syntax without warnings.' );
		mk_alert_test_assert( false !== strpos( do_shortcode( '[' . $tag . '][/' . $tag . ']' ), 'mk-simple-alert-box-' . $type ), $tag . ' accepts an empty enclosing pair without warnings.' );
		mk_alert_test_assert( false === strpos( $html, 'mk-simple-alert-box-header' ), 'Legacy shortcodes keep their original appearance by default.' );
		$html = do_shortcode( '[mk_alert type="' . $type . '" title="Заголовок &amp; text"]<strong>Body</strong>[/mk_alert]' );
		mk_alert_test_assert( false !== strpos( $html, 'mk-simple-alert-box-' . $type . '"' ), 'Unified shortcode supports ' . $type . '.' );
		mk_alert_test_assert( false !== strpos( $html, 'mk-simple-alert-box-title">Заголовок &amp; text</strong>' ), 'Titles support Unicode and entities.' );
		mk_alert_test_assert( false !== strpos( $html, 'mk-simple-alert-box-icon" aria-hidden="true"' ), 'Decorative icons are hidden from assistive technology.' );
		mk_alert_test_assert( false !== strpos( $html, 'mk-simple-alert-box-type">' ), 'An icon has a text equivalent for assistive technology.' );
		$html = do_shortcode( '[' . $tag . ' title="Legacy title" icon="true"]Body[/' . $tag . ']' );
		mk_alert_test_assert( false !== strpos( $html, 'Legacy title</strong>' ) && false !== strpos( $html, 'mk-simple-alert-box-icon' ), 'Legacy shortcodes support opt-in headings and icons.' );
	}
	foreach ( array( 'false', '0', 'no', 'off' ) as $disabled ) {
		mk_alert_test_assert( false === strpos( do_shortcode( '[mk_alert icon="' . $disabled . '"]Body[/mk_alert]' ), 'mk-simple-alert-box-header' ), 'The icon can be disabled with ' . $disabled . '.' );
	}
	mk_alert_test_assert( false !== strpos( do_shortcode( '[mk_alert]' ), 'mk-simple-alert-box-info' ), 'The unified shortcode supports empty content and defaults to info.' );
	mk_alert_test_assert( false !== strpos( do_shortcode( '[mk_alert type="not-a-type"]Body[/mk_alert]' ), 'mk-simple-alert-box-info' ), 'Unknown types safely fall back to info.' );
	$html = mk_simple_alert_box_shortcode( array( 'type' => array(), 'title' => array(), 'icon' => array() ), null );
	mk_alert_test_assert( false !== strpos( $html, 'mk-simple-alert-box-info' ), 'Malformed attribute types do not cause runtime errors.' );
	$html = mk_simple_alert_box_shortcode( array( 'title' => '<img src=x onerror=alert(1)>Safe <b>title</b>', 'class' => 'injected', 'style' => 'display:none' ), 'Body' );
	foreach ( array( '<img', 'onerror', '<b>', 'injected', 'display:none' ) as $unsafe ) {
		mk_alert_test_assert( false === strpos( $html, $unsafe ), 'Unsafe or unsupported shortcode attributes are not rendered: ' . $unsafe );
	}
	$html = do_shortcode( '[mk_alert_info][mk_alert type="success" title="Nested"]Body[/mk_alert][/mk_alert_info]' );
	mk_alert_test_assert( false !== strpos( $html, 'mk-simple-alert-box-icon" aria-hidden="true"' ), 'A nested unified alert retains its accessible icon.' );

	add_shortcode( 'mk_alert_test_link', function () { return '<a href="https://example.com/">Nested link</a>'; } );
	$html = do_shortcode( '[mk_alert_info][mk_alert_test_link][/mk_alert_info]' );
	mk_alert_test_assert( false !== strpos( $html, '<a href="https://example.com/">Nested link</a>' ), 'Nested shortcode HTML remains usable.' );
	remove_shortcode( 'mk_alert_test_link' );
	$html = do_shortcode( '[mk_alert_info][mk_alert_success]Nested[/mk_alert_success][/mk_alert_info]' );
	mk_alert_test_equal( 2, substr_count( $html, '<div ' ), 'Different alert types may be nested.' );
	$html = do_shortcode( '[mk_alert_info]<script>alert(1)</script><a href="javascript:alert(1)" onclick="alert(1)">Link</a><img src="https://example.com/a.png" onerror="alert(1)">[/mk_alert_info]' );
	foreach ( array( '<script', 'javascript:', 'onclick=', 'onerror=' ) as $unsafe ) {
		mk_alert_test_assert( false === stripos( $html, $unsafe ), 'Unsafe content is removed: ' . $unsafe );
	}
	$html = apply_filters( 'the_content', "[mk_alert_info]<strong>Первый абзац</strong>\n\nSecond paragraph[/mk_alert_info]" );
	mk_alert_test_assert( false !== strpos( $html, '<strong>Первый абзац</strong>' ), 'Normal post content preserves Unicode and markup.' );
	mk_alert_test_assert( ! preg_match( '/<p>\s*<div class="mk-simple-alert-box/', $html ), 'WordPress paragraph formatting does not wrap the alert div in a paragraph.' );
	preg_match_all( '/<p(?:\s[^>]*)?>/', $html, $opening_paragraphs );
	mk_alert_test_assert( count( $opening_paragraphs[0] ) >= 2, 'Multiline alert text retains separate paragraphs.' );
	mk_alert_test_equal( count( $opening_paragraphs[0] ), substr_count( $html, '</p>' ), 'Multiline alert paragraphs have balanced tags.' );

	foreach ( $settings as $name => $setting ) {
		update_option( $name, array( 'invalid-first-save' ) );
		mk_alert_test_equal( (string) $setting['default'], (string) get_option( $name ), $name . ' uses the default when the first submitted value is invalid.' );
		if ( 'integer' === $setting['type'] ) {
			foreach ( array( $setting['min'], $setting['max'] ) as $boundary ) {
				update_option( $name, (string) $boundary );
				mk_alert_test_equal( (string) $boundary, (string) get_option( $name ), $name . ' accepts values at the supported range boundary.' );
			}
		}
		$valid = 'integer' === $setting['type'] ? ( 'mk_simple_alert_box_font_size' === $name ? 24 : 0 ) : '#123abc';
		update_option( $name, $valid );
		mk_alert_test_equal( (string) $valid, (string) get_option( $name ), $name . ' saves a valid value through WordPress.' );
		mk_alert_test_equal( (string) $valid, (string) mk_simple_alert_box_get_option( $name ), $name . ' reads the saved value.' );
		$invalid_values = 'integer' === $setting['type']
			? array( '', '-1', '12px', '1.5', '1e3', true, array( '12' ), 1.5, (string) ( $setting['max'] + 1 ) )
			: array( '', 'red', '#12', '#gggggg', 'red; color:black', true, array( '#fff' ) );
		foreach ( $invalid_values as $invalid ) {
			$GLOBALS['wp_settings_errors'] = array();
			update_option( $name, $invalid );
			mk_alert_test_equal( (string) $valid, (string) get_option( $name ), $name . ' preserves the last valid setting after malformed input.' );
			mk_alert_test_assert( count( get_settings_errors() ) > 0, $name . ' explains rejected input.' );
		}

		// Simulate options left by older versions or modified directly in the database.
		foreach ( array( array( 'corrupt' ), false, 'invalid', -999 ) as $legacy ) {
			$filter = function () use ( $legacy ) { return $legacy; };
			// option_* allows false to reach the reader; pre_option_* uses false as a sentinel.
			add_filter( 'option_' . $name, $filter );
			mk_alert_test_equal( $setting['default'], mk_simple_alert_box_get_option( $name ), $name . ' falls back safely for malformed legacy data.' );
			$css = mk_simple_alert_box_get_dynamic_styles();
			mk_alert_test_assert( is_string( $css ) && false === strpos( $css, 'Array' ), 'Malformed option data does not break CSS generation.' );
			remove_filter( 'option_' . $name, $filter );
		}
	}

	update_option( 'mk_simple_alert_box_info_color', ' #abc ' );
	mk_alert_test_equal( '#abc', get_option( 'mk_simple_alert_box_info_color' ), 'Three-digit hex colors and surrounding whitespace are accepted.' );
	update_option( 'mk_simple_alert_box_font_size', 0 );
	mk_alert_test_equal( '24', (string) get_option( 'mk_simple_alert_box_font_size' ), 'Font size cannot hide the text by becoming zero.' );
	ob_start();
	$css            = mk_simple_alert_box_get_dynamic_styles();
	$unexpected_css = ob_get_clean();
	mk_alert_test_equal( '', $unexpected_css, 'The CSS generator does not print output directly.' );
	mk_alert_test_assert( (bool) preg_match( '/padding:\s*0px/', $css ), 'Zero padding is retained in CSS.' );
	mk_alert_test_assert( (bool) preg_match( '/margin:\s*0px/', $css ), 'Zero margin is retained in CSS.' );
	mk_alert_test_assert( false !== strpos( $css, '#abc' ), 'Saved colors reach the generated CSS.' );
	mk_alert_test_assert( false === stripos( $css, '<style' ), 'The CSS generator returns CSS rather than printing a style tag.' );

	// The enqueue hooks must work independently of shortcode location (widgets/templates included).
	do_action( 'wp_enqueue_scripts' );
	mk_alert_test_assert( wp_style_is( 'mk-simple-alert-box', 'enqueued' ), 'Frontend styles use the WordPress asset API.' );
	mk_alert_test_assert( ! wp_script_is( 'mk-simple-alert-box-admin', 'enqueued' ), 'Preview JavaScript is not loaded on the frontend.' );
	$inline = wp_styles()->get_data( 'mk-simple-alert-box', 'after' );
	mk_alert_test_assert( is_array( $inline ) && false !== strpos( implode( '\n', $inline ), '#abc' ), 'Dynamic styles are attached to the registered stylesheet.' );
	wp_dequeue_style( 'mk-simple-alert-box' );
	set_current_screen( 'dashboard' );
	do_action( 'admin_enqueue_scripts', 'dashboard' );
	mk_alert_test_assert( ! wp_style_is( 'mk-simple-alert-box', 'enqueued' ), 'Other admin screens do not receive plugin styles.' );
	mk_alert_test_assert( ! wp_script_is( 'mk-simple-alert-box-admin', 'enqueued' ), 'Other admin screens do not receive preview JavaScript.' );
	set_current_screen( 'toplevel_page_mk-simple-alert-box' );
	do_action( 'admin_enqueue_scripts', 'toplevel_page_mk-simple-alert-box' );
	mk_alert_test_assert( wp_style_is( 'mk-simple-alert-box', 'enqueued' ), 'The settings screen receives its styles.' );
	mk_alert_test_assert( wp_script_is( 'mk-simple-alert-box-admin', 'enqueued' ), 'The settings screen receives preview JavaScript.' );
	mk_alert_test_assert( wp_style_is( 'wp-color-picker', 'enqueued' ), 'The settings screen uses the built-in WordPress color picker.' );

	$administrators = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	mk_alert_test_assert( count( $administrators ) > 0, 'The disposable WordPress installation has an administrator.' );
	wp_set_current_user( $administrators[0] );
	ob_start();
	mk_simple_alert_box_options_page();
	$page = ob_get_clean();
	mk_alert_test_assert( false !== strpos( $page, 'action="options.php"' ), 'Settings submit through WordPress options.php.' );
	mk_alert_test_assert( false !== strpos( $page, 'name="_wpnonce"' ), 'The settings form includes WordPress CSRF protection.' );
	mk_alert_test_equal( 4, substr_count( $page, 'data-mk-preview-type=' ), 'All four alert types have a live preview.' );
	mk_alert_test_equal( 8, substr_count( $page, 'class="mk-alert-color"' ), 'All eight color fields use the color picker.' );
	foreach ( $settings as $name => $setting ) {
		mk_alert_test_assert( false !== strpos( $page, 'name="' . $name . '"' ), 'The settings page includes ' . $name . '.' );
		mk_alert_test_assert( (bool) preg_match( '/<label\b[^>]*for="' . preg_quote( $name, '/' ) . '"/', $page ), 'The settings field has a linked label: ' . $name );
	}

	$created_user = wp_insert_user( array( 'user_login' => 'mk-alert-test-' . wp_generate_password( 8, false ), 'user_pass' => wp_generate_password(), 'role' => 'subscriber' ) );
	mk_alert_test_assert( ! is_wp_error( $created_user ), 'Created a subscriber for the permission check.' );
	wp_set_current_user( $created_user );
	$die_handler = function () { return function () { throw new RuntimeException( 'mk-alert-permission-denied' ); }; };
	add_filter( 'wp_die_handler', $die_handler );
	$denied = false;
	ob_start();
	try {
		mk_simple_alert_box_options_page();
	} catch ( RuntimeException $error ) {
		if ( 'mk-alert-permission-denied' !== $error->getMessage() ) {
			throw $error;
		}
		$denied = true;
	} finally {
		$unauthorized_output = ob_get_clean();
		remove_filter( 'wp_die_handler', $die_handler );
	}
	mk_alert_test_assert( $denied, 'The page callback independently rejects users without manage_options.' );
	mk_alert_test_assert( false === strpos( $unauthorized_output, '<form' ), 'Unauthorized users do not receive the settings form.' );

} finally {
	restore_error_handler();
	wp_set_current_user( $original_user );
	if ( $created_user && ! is_wp_error( $created_user ) ) {
		wp_delete_user( $created_user );
	}
	foreach ( $original_options as $name => $value ) {
		if ( $missing === $value ) {
			delete_option( $name );
		} else {
			update_option( $name, $value );
		}
	}
}

WP_CLI::success( $mk_alert_test_assertions . ' integration assertions passed on WordPress ' . get_bloginfo( 'version' ) . ' / PHP ' . PHP_VERSION . '.' );
