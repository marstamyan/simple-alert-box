<?php
/*
 * Plugin Name: Simple Alert Boxes
 * Description: A simple plugin to create alert boxes using shortcodes. Usage: [mk_alert_info]Info message[/mk_alert_info], [mk_alert_warning]Warning message[/mk_alert_warning], [mk_alert_success]Success message[/mk_alert_success], [mk_alert_error]Error message[/mk_alert_error].
 * Version: 1.0
 * Author: Mamikon
 * Author URI: https://linkedin.com/in/mamikon-arustamyan-3969301ab?/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mk-simple-alert-box
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MK_SIMPLE_ALERT_BOX_PATH', plugin_dir_path( __FILE__ ) );

require_once MK_SIMPLE_ALERT_BOX_PATH . 'admin/mk-simple-alert-box-admin.php';
require_once MK_SIMPLE_ALERT_BOX_PATH . 'public/mk-simple-alert-box-public.php';