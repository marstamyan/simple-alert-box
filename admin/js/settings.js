(function ($) {
	'use strict';

	$(function () {
		var previews = document.querySelectorAll('[data-mk-preview-type]');
		if (!previews.length) {
			return;
		}

		function color(value) {
			value = value.trim();
			return /^#(?:[a-f0-9]{3}|[a-f0-9]{6})$/i.test(value) ? value : null;
		}

		function luminance(hex) {
			hex = hex.slice(1);
			if (hex.length === 3) {
				hex = hex.split('').map(function (c) { return c + c; }).join('');
			}
			var channels = hex.match(/../g).map(function (c) {
				var s = parseInt(c, 16) / 255;
				return s <= 0.04045 ? s / 12.92 : Math.pow((s + 0.055) / 1.055, 2.4);
			});
			return channels[0] * 0.2126 + channels[1] * 0.7152 + channels[2] * 0.0722;
		}

		function update() {
			previews.forEach(function (wrapper) {
				var type = wrapper.getAttribute('data-mk-preview-type');
				var box = wrapper.querySelector('.mk-simple-alert-box');
				var background = color(document.getElementById('mk_simple_alert_box_' + type + '_color').value);
				var text = color(document.getElementById('mk_simple_alert_box_' + type + '_text_color').value);
				var status = wrapper.querySelector('[data-mk-contrast]');
				var message = mkAlertBoxPreview.invalidColor;
				var low = true;
				if (background && text) {
					box.style.backgroundColor = background;
					box.style.color = text;
					var a = luminance(background);
					var b = luminance(text);
					var ratio = (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);
					low = ratio < 4.5;
					message = (low ? mkAlertBoxPreview.contrastLow : mkAlertBoxPreview.contrastGood).replace('%s', (Math.floor(ratio * 100) / 100).toFixed(2));
				}
				if (status.textContent !== message) {
					status.textContent = message;
				}
				status.classList.toggle('is-low', low);
				['padding', 'margin', 'font_size'].forEach(function (name) {
					var input = document.getElementById('mk_simple_alert_box_' + name);
					if (/^[0-9]+$/.test(input.value) && input.checkValidity()) {
						var property = name === 'font_size' ? 'fontSize' : name;
						box.style[property] = input.value + 'px' + (name === 'margin' ? ' 0' : '');
					}
				});
			});
		}

		$('.mk-alert-color').wpColorPicker({
			change: function (event, ui) {
				// Iris calls change before updating the input value.
				$(this).val(ui.color.toString());
				update();
			},
			clear: function () { window.setTimeout(update, 0); }
		});
		$('.mk-alert-settings-layout input').on('input change', update);
		update();
	});
}(jQuery));
