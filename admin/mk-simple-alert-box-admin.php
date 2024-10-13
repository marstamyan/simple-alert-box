<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mk_simple_alert_boxes_add_menu() {
	add_menu_page(
		'Simple Alert Boxes Settings',
		'Alert Boxes',
		'manage_options',
		'mk-simple-alert-box',
		'mk_simple_alert_box_options_page',
		'dashicons-info-outline',
		81
	);
}

add_action( 'admin_menu', 'mk_simple_alert_boxes_add_menu' );

// Function to display the settings page
function mk_simple_alert_box_options_page() {
	?>
	<div class="wrap">
		<h1>Simple Alert Boxes Settings</h1>

		<!-- Info block with usage instructions -->
		<div class="mk-simple-alert-box mk-simple-alert-box-info" style="margin-bottom: 20px;">
			<strong>How to use:</strong> You can insert the following shortcodes to display different alert boxes:
			<ul>
				<li><code>[mk_alert_info]Your info message here[/mk_alert_info]</code> - for an info alert</li>
				<li><code>[mk_alert_warning]Your warning message here[/mk_alert_warning]</code> - for a warning alert</li>
				<li><code>[mk_alert_success]Your success message here[/mk_alert_success]</code> - for a success alert</li>
				<li><code>[mk_alert_error]Your error message here[/mk_alert_error]</code> - for an error alert</li>
			</ul>
		</div>

		<form method="post" action="options.php">
			<?php
			// Output security fields for the registered setting
			settings_fields( 'mk_simple_alert_box_options' );
			do_settings_sections( 'mk_simple_alert_box_options' );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Info Box Color</th>
					<td><input type="text" name="mk_simple_alert_box_info_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_info_color', '#bfdcef' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Info Text Color</th>
					<td><input type="text" name="mk_simple_alert_box_info_text_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_info_text_color', '#457389' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Warning Box Color</th>
					<td><input type="text" name="mk_simple_alert_box_warning_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_warning_color', '#f5eecb' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Warning Text Color</th>
					<td><input type="text" name="mk_simple_alert_box_warning_text_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_warning_text_color', '#8a6d3b' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Success Box Color</th>
					<td><input type="text" name="mk_simple_alert_box_success_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_success_color', '#d1f2c4' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Success Text Color</th>
					<td><input type="text" name="mk_simple_alert_box_success_text_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_success_text_color', '#3c763d' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Error Box Color</th>
					<td><input type="text" name="mk_simple_alert_box_error_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_error_color', '#f0cccc' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Error Text Color</th>
					<td><input type="text" name="mk_simple_alert_box_error_text_color"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_error_text_color', '#a94442' ) ); ?>" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Padding</th>
					<td><input type="number" name="mk_simple_alert_box_padding"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_padding', '10' ) ); ?>" /> px</td>
				</tr>
				<tr valign="top">
					<th scope="row">Margin</th>
					<td><input type="number" name="mk_simple_alert_box_margin"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_margin', '10' ) ); ?>" /> px</td>
				</tr>
				<tr valign="top">
					<th scope="row">Font Size</th>
					<td><input type="number" name="mk_simple_alert_box_font_size"
							value="<?php echo esc_attr( get_option( 'mk_simple_alert_box_font_size', '16' ) ); ?>" /> px
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

// Register settings for the options page// Validation for hex colors and numeric values
function mk_simple_alert_box_validate_color( $input ) {
	return sanitize_hex_color( $input );
}

function mk_simple_alert_box_validate_number( $input ) {
	return intval( $input );
}

// Register settings with validation callbacks
function mk_simple_alert_box_register_settings() {
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_info_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_warning_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_success_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_error_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_info_text_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_warning_text_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_success_text_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_error_text_color', 'mk_simple_alert_box_validate_color' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_padding', 'mk_simple_alert_box_validate_number' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_margin', 'mk_simple_alert_box_validate_number' );
	register_setting( 'mk_simple_alert_box_options', 'mk_simple_alert_box_font_size', 'mk_simple_alert_box_validate_number' );
}
add_action( 'admin_init', 'mk_simple_alert_box_register_settings' );