=== Simple Alert Boxes ===
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Four static alert boxes with safe HTML content and customizable colors, spacing, and text size.

== Description ==

Display informational, warning, success, and error messages using shortcodes. Customize the background and text colors, padding, vertical margin, and font size in the Alert Boxes administration page.

Supported shortcodes:

* [mk_alert type="warning" title="Please note"]Your message here.[/mk_alert]
* [mk_alert_info]Information[/mk_alert_info]
* [mk_alert_warning]Warning[/mk_alert_warning]
* [mk_alert_success]Success[/mk_alert_success]
* [mk_alert_error]Error[/mk_alert_error]

The unified shortcode accepts type="info|warning|success|error", an optional plain-text title, and icon="false" to hide its default icon. The original shortcodes keep their appearance and also accept title and icon="true".

Links, emphasis, lists, paragraphs, and other safe post HTML are supported. Nested shortcode output is filtered through WordPress's allowed post HTML. Scripts and iframe embeds are not supported inside an alert. Nesting the same shortcode tag inside itself is a WordPress parser limitation, including two mk_alert tags with different types.

The settings screen includes WordPress color pickers and live previews of all four types. Colors, spacing, and text size update immediately in the preview; Save Changes applies them to the website. A contrast indicator warns about text/background pairs below 4.5:1. Theme overrides, links, and embedded HTML may use different colors. Without JavaScript, you can still type values and save the form.

These boxes are static content, without popups, dismissal, scheduling, or live announcements. Styles load throughout the frontend to cover widgets and templates, and only on this plugin's own administration page.

All settings are local to your WordPress installation. The plugin does not contact external services. Settings are preserved on deactivation and removal.

== Installation ==

1. Upload the plugin ZIP through Plugins > Add New > Upload Plugin, or copy the plugin directory into wp-content/plugins.
2. Activate Simple Alert Boxes.
3. Open Alert Boxes in the administration menu.
4. Insert a supported shortcode in a Shortcode block or the classic editor.

== Frequently Asked Questions ==

= What color formats are supported? =

Use #RGB or #RRGGBB, such as #abc or #bfdcef.

= What values can I use for spacing and font size? =

Padding and vertical margin accept whole numbers from 0 to 1000 pixels. Font size accepts whole numbers from 1 to 200 pixels. Invalid changes keep the previous usable value and show an error.

= Will my existing shortcodes and settings continue working? =

The original four shortcode names, CSS classes, and eleven option names are preserved. Replace the files in the existing plugin directory when updating. Invalid legacy values use safe defaults.

== Changelog ==

= 1.2.0 =
* Add the unified mk_alert shortcode with optional title and type-specific icons.
* Add optional titles and icons to existing shortcodes without changing their default appearance.
* Add WordPress color pickers, live previews, and contrast feedback.
* Keep preview JavaScript limited to the settings screen.
* Show confirmation when settings are saved.

= 1.1.0 =
* Preserve safe HTML and nested shortcode output.
* Fix empty messages and multiple-paragraph content.
* Validate settings, preserve valid values, and handle malformed legacy options.
* Use WordPress's stylesheet API and style the settings page.
* Add accessible labels, field hints, translatable strings, and integration checks.
* Improve text contrast in the default palette without replacing saved custom colors.

= 1.0 =
* Initial release.
