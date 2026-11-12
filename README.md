# Simple Alert Boxes

Create four kinds of static notice boxes in WordPress posts, pages, widgets, or templates.

## Requirements

- WordPress 6.0 or later.
- PHP 7.4 or later. Use a currently supported PHP version on a live website.

## Installation

1. Download a plugin ZIP or the repository ZIP.
2. In WordPress, open **Plugins > Add New > Upload Plugin**.
3. Install the ZIP and activate **Simple Alert Boxes**.
4. Open **Alert Boxes** in the administration menu to customize the appearance.

When updating an existing installation, replace its plugin files in the same plugin directory. The original four shortcode names, CSS classes, and all eleven saved option names are preserved.

## Usage

Add a Shortcode block in the block editor, or paste a shortcode into the classic editor:

```text
[mk_alert type="warning" title="Please note"]Your message here.[/mk_alert]
```

`type` accepts `info` (default), `warning`, `success`, or `error`. Unknown types fall back to `info`. The optional `title` is plain text. A type-specific icon appears by default; add `icon="false"` to hide it. Icons have a text equivalent for screen readers. Alert boxes are static content, without interruptive live-region announcements.

The original shortcodes remain available and keep their appearance. They also support optional `title="…"` and `icon="true"` attributes:

```text
[mk_alert_info]An information message.[/mk_alert_info]
[mk_alert_warning]A warning message.[/mk_alert_warning]
[mk_alert_success]A success message.[/mk_alert_success]
[mk_alert_error]An error message.[/mk_alert_error]
```

Safe post HTML, including links, emphasis, and lists, is supported:

```html
[mk_alert_info]<strong>Please read:</strong> <a href="https://example.com/help">Help page</a>.[/mk_alert_info]
```

Different shortcode tags and other shortcodes may be nested. Their output is filtered through WordPress's allowed post HTML; scripts, event handlers, and unsafe URL protocols are removed. Embeds or shortcodes that require disallowed HTML such as iframes are not supported inside an alert. Nesting the same shortcode tag inside itself is a limitation of WordPress's parser, including two `mk_alert` tags with different `type` values. Use different legacy tags when nesting alerts.

These boxes display static content. They do not create popups, dismiss buttons, scheduled notices, or live notifications.

## Appearance settings

- Background and text colors for each of the four alert types: `#RGB` or `#RRGGBB`.
- Padding and vertical margin: whole numbers from 0 to 1000 pixels.
- Font size: whole numbers from 1 to 200 pixels.

Each color field uses WordPress's built-in color picker, with palette swatches and a Default button. All eight fields still accept typed HEX values.

The settings screen shows a live preview of all four types. Colors, spacing, and font size update before you save; **Save Changes** applies them to your website. A contrast indicator warns when a text/background pair falls below 4.5:1 for normal text. The indicator does not assess theme overrides or the colors of links and embedded HTML. Invalid values keep the last valid preview until corrected. Without JavaScript, the form still works and previews show the saved settings.

Invalid changes keep the previous usable value and show an error. Existing invalid database values fall back to the built-in defaults without requiring a migration. Zero padding and margin are supported. Administrators control the settings through the standard WordPress Settings API.

The built-in text colors are darker in 1.1.0 for readable contrast against the default backgrounds. Saved custom colors are preserved; choose contrasting colors when customizing them.

A small inline stylesheet loads on the frontend so alerts also work in widgets and PHP templates. In the administration area it loads only on this plugin's settings page. Interface strings are prepared for translation; translation files are not bundled.

Deactivation preserves the settings. This plugin does not delete its saved settings when removed.

## Development and checks

See [tests/README.md](tests/README.md) for the real WordPress integration suite and local Docker environment. GitHub Actions checks the declared minimum environment and current WordPress/PHP combinations when the workflow runs.

## Changelog

### 1.2.0

- Add the unified `mk_alert` shortcode with type, title, and icon options.
- Support optional headings and icons on the original shortcodes, preserving their default appearance.
- Add the WordPress color picker, live previews, and contrast feedback on the settings page.
- Keep preview scripts limited to the plugin's settings screen; no new frontend JavaScript.
- Display the standard confirmation after saving settings.

### 1.1.0

- Preserve safe formatting and nested shortcode output.
- Handle empty shortcodes without passing null into WordPress string functions.
- Validate color and number types and ranges, preserve usable settings, and recover from invalid legacy values.
- Enqueue styles through WordPress and include them on the settings screen.
- Associate form labels with inputs, add input hints and bounds, and prepare interface text for translation.
- Improve text contrast in the default palette.
- Add integration checks and continuous integration configuration.

### 1.0

- Initial release.

## License

GPL-2.0-or-later.
