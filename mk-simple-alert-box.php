<?php
/*
 * Plugin Name: Simple Alert Boxes
 * Description: Customizable info, warning, success and error boxes using shortcodes, with optional titles and icons, color pickers and live previews.
 * Version: 1.2.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Mamikon
 * Author URI: https://linkedin.com/in/mamikon-arustamyan-3969301ab/
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-alert-box
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MK_SIMPLE_ALERT_BOX_PATH', plugin_dir_path( __FILE__ ) );
define( 'MK_SIMPLE_ALERT_BOX_VERSION', '1.2.0' );

require_once MK_SIMPLE_ALERT_BOX_PATH . 'includes/mk-simple-alert-box-settings.php';
require_once MK_SIMPLE_ALERT_BOX_PATH . 'admin/mk-simple-alert-box-admin.php';
require_once MK_SIMPLE_ALERT_BOX_PATH . 'public/mk-simple-alert-box-public.php';
