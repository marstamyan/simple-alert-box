<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mk_simple_alert_boxes_add_menu() {
	add_menu_page(
		__( 'Simple Alert Boxes Settings', 'simple-alert-box' ),
		__( 'Alert Boxes', 'simple-alert-box' ),
		'manage_options',
		'mk-simple-alert-box',
		'mk_simple_alert_box_options_page',
		'dashicons-info-outline',
		81
	);
}
add_action( 'admin_menu', 'mk_simple_alert_boxes_add_menu' );

function mk_simple_alert_box_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to manage these settings.', 'simple-alert-box' ) );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Simple Alert Boxes Settings', 'simple-alert-box' ); ?></h1>
		<?php settings_errors(); ?>
		<div class="mk-simple-alert-box mk-simple-alert-box-info">
			<strong><?php esc_html_e( 'How to use:', 'simple-alert-box' ); ?></strong>
			<?php esc_html_e( 'Insert these shortcodes in a Shortcode block or in the classic editor:', 'simple-alert-box' ); ?>
			<ul>
				<?php foreach ( array( 'info', 'warning', 'success', 'error' ) as $type ) : ?>
					<li><code><?php echo esc_html( '[mk_alert_' . $type . ']' . __( 'Your message here', 'simple-alert-box' ) . '[/mk_alert_' . $type . ']' ); ?></code></li>
				<?php endforeach; ?>
			</ul>
			<p><?php esc_html_e( 'Links, emphasis, lists and other safe post HTML are supported inside alerts.', 'simple-alert-box' ); ?></p>
			<p><code>[mk_alert type="warning" title="<?php esc_attr_e( 'Please note', 'simple-alert-box' ); ?>"]<?php esc_html_e( 'Your message here', 'simple-alert-box' ); ?>[/mk_alert]</code></p>
			<p><?php esc_html_e( 'Types: info, warning, success, error. Add title="…" for a heading or icon="false" to hide the icon. Existing shortcodes also accept title and icon="true".', 'simple-alert-box' ); ?></p>
		</div>
		<p id="mk-alert-color-help"><?php esc_html_e( 'Colors must use #RGB or #RRGGBB format, for example #abc or #bfdcef.', 'simple-alert-box' ); ?></p>
		<div class="mk-alert-settings-layout">
		<form method="post" action="options.php">
			<?php settings_fields( 'mk_simple_alert_box_options' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( mk_simple_alert_box_get_settings() as $option_name => $setting ) : ?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $option_name ); ?>"><?php echo esc_html( $setting['label'] ); ?></label></th>
						<td>
							<?php if ( 'string' === $setting['type'] ) : ?>
								<input type="text" class="mk-alert-color" data-default-color="<?php echo esc_attr( $setting['default'] ); ?>" id="<?php echo esc_attr( $option_name ); ?>" name="<?php echo esc_attr( $option_name ); ?>" value="<?php echo esc_attr( mk_simple_alert_box_get_option( $option_name ) ); ?>" maxlength="7" pattern="#[a-fA-F0-9]{3}([a-fA-F0-9]{3})?" aria-describedby="mk-alert-color-help" required />
							<?php else : ?>
								<input type="number" id="<?php echo esc_attr( $option_name ); ?>" name="<?php echo esc_attr( $option_name ); ?>" value="<?php echo esc_attr( mk_simple_alert_box_get_option( $option_name ) ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" step="1" aria-describedby="<?php echo esc_attr( $option_name ); ?>-help" required /> px
								<p class="description" id="<?php echo esc_attr( $option_name ); ?>-help">
									<?php
									/* translators: 1: minimum value, 2: maximum value. */
									echo esc_html( sprintf( __( 'Whole numbers from %1$d to %2$d pixels.', 'simple-alert-box' ), $setting['min'], $setting['max'] ) );
									?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
		<aside class="mk-alert-preview" aria-labelledby="mk-alert-preview-heading">
			<h2 id="mk-alert-preview-heading"><?php esc_html_e( 'Live preview', 'simple-alert-box' ); ?></h2>
			<p><?php esc_html_e( 'Changes appear here before saving. Save Changes applies them to your website.', 'simple-alert-box' ); ?></p>
			<?php foreach ( mk_simple_alert_box_get_types() as $type => $details ) : ?>
				<div data-mk-preview-type="<?php echo esc_attr( $type ); ?>">
					<?php echo wp_kses_post( mk_simple_alert_box_render( $type, __( 'This is how your message will look.', 'simple-alert-box' ), array( 'title' => $details['label'], 'icon' => true ) ) ); ?>
					<p class="description mk-alert-contrast" data-mk-contrast aria-live="polite"></p>
				</div>
			<?php endforeach; ?>
			<noscript><p><?php esc_html_e( 'Enable JavaScript for the color picker and live updates. You can still enter values and save the form.', 'simple-alert-box' ); ?></p></noscript>
		</aside>
		</div>
	</div>
	<?php
}

function mk_simple_alert_box_register_settings() {
	foreach ( mk_simple_alert_box_get_settings() as $option_name => $setting ) {
		register_setting(
			'mk_simple_alert_box_options',
			$option_name,
			array(
				'type'              => $setting['type'],
				'default'           => $setting['default'],
				'sanitize_callback' => function ( $input ) use ( $option_name ) {
					return mk_simple_alert_box_sanitize_setting( $input, $option_name );
				},
			)
		);
	}
}
add_action( 'admin_init', 'mk_simple_alert_box_register_settings' );

function mk_simple_alert_box_admin_enqueue_styles( $hook_suffix ) {
	if ( 'toplevel_page_mk-simple-alert-box' === $hook_suffix ) {
		mk_simple_alert_box_enqueue_styles();
		$plugin_url = plugin_dir_url( MK_SIMPLE_ALERT_BOX_PATH . 'mk-simple-alert-box.php' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'mk-simple-alert-box-admin', $plugin_url . 'admin/css/settings.css', array( 'wp-color-picker' ), MK_SIMPLE_ALERT_BOX_VERSION );
		wp_enqueue_script( 'mk-simple-alert-box-admin', $plugin_url . 'admin/js/settings.js', array( 'jquery', 'wp-color-picker' ), MK_SIMPLE_ALERT_BOX_VERSION, true );
		wp_localize_script(
			'mk-simple-alert-box-admin',
			'mkAlertBoxPreview',
			array(
				/* translators: %s: calculated contrast ratio. */
				'contrastGood' => __( 'Contrast: %s:1. Good for normal text.', 'simple-alert-box' ),
				/* translators: %s: calculated contrast ratio. */
				'contrastLow'  => __( 'Contrast: %s:1. Choose more contrasting text and background colors (minimum 4.5:1).', 'simple-alert-box' ),
				'invalidColor' => __( 'Enter valid colors to update this preview.', 'simple-alert-box' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'mk_simple_alert_box_admin_enqueue_styles' );
