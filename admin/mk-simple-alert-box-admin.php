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
		<?php settings_errors( 'mk_simple_alert_box_options' ); ?>
		<div class="mk-simple-alert-box mk-simple-alert-box-info">
			<strong><?php esc_html_e( 'How to use:', 'simple-alert-box' ); ?></strong>
			<?php esc_html_e( 'Insert these shortcodes in a Shortcode block or in the classic editor:', 'simple-alert-box' ); ?>
			<ul>
				<?php foreach ( array( 'info', 'warning', 'success', 'error' ) as $type ) : ?>
					<li><code><?php echo esc_html( '[mk_alert_' . $type . ']' . __( 'Your message here', 'simple-alert-box' ) . '[/mk_alert_' . $type . ']' ); ?></code></li>
				<?php endforeach; ?>
			</ul>
			<p><?php esc_html_e( 'Links, emphasis, lists and other safe post HTML are supported inside alerts.', 'simple-alert-box' ); ?></p>
		</div>
		<p id="mk-alert-color-help"><?php esc_html_e( 'Colors must use #RGB or #RRGGBB format, for example #abc or #bfdcef.', 'simple-alert-box' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'mk_simple_alert_box_options' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( mk_simple_alert_box_get_settings() as $option_name => $setting ) : ?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $option_name ); ?>"><?php echo esc_html( $setting['label'] ); ?></label></th>
						<td>
							<?php if ( 'string' === $setting['type'] ) : ?>
								<input type="text" id="<?php echo esc_attr( $option_name ); ?>" name="<?php echo esc_attr( $option_name ); ?>" value="<?php echo esc_attr( mk_simple_alert_box_get_option( $option_name ) ); ?>" maxlength="7" pattern="#[a-fA-F0-9]{3}([a-fA-F0-9]{3})?" aria-describedby="mk-alert-color-help" required />
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
	}
}
add_action( 'admin_enqueue_scripts', 'mk_simple_alert_box_admin_enqueue_styles' );
