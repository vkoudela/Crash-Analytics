BootstrapUI.Button = BootstrapUI.Button || {};
BootstrapUI.Button.Remote = BootstrapUI.Button.Remote || {};

BootstrapUI.Button.Remote.click = function(button) {
	var $button = $(button);
	
	if (typeof $button.data('prompt-text') != 'undefined') {
		if (!confirm($button.data('prompt-text'))) {
			return;
		}
	}
	
	$button.attr('disabled', 'disabled');
	
	if (typeof $button.data('progress-text') != 'undefined') {
		$button.html($button.data('progress-text'));
	}
	
	var params = {};
	if (typeof $button.data('params') != 'undefined') {
		params = json_decode(base64_decode($button.data('params')))
	}
	
	$.ajax({
		url: $button.data('url'),
		type: 'POST',
		data: params,
		$button: $button,
		success: function(json) {
			var $button = this.$button;
			$button.attr('disabled', false);
			
			if (json.success) {
				if (json.removeClosest) {
					$button.closest(json.removeClosest).fadeOut();
				}
				
				if (json.reloadParentTable && json.reloadParentTable == 'yes') {
					var $table = $button.closest('table');
					BootstrapUI.Table.Remote.reload($table);
				}
				
				if (json.disabled) {
					$button.attr('disabled', 'disabled');
				}
				
				if (json.removeOtherButtons) {
					$button.parent().find('button:not(#' + $button.attr('id') + '), .btn:not(#' + $button.attr('id') + ')').each(function() {
						$(this).fadeOut();
					});
				}
				
				if (json.disableOtherButtons) {
					$button.parent().find('button:not(#' + $button.attr('id') + '), .btn:not(#' + $button.attr('id') + ')').each(function() {
						$(this).attr('disabled', 'disabled');
					});
				}
				
				if (json.removeButton) {
					$button.fadeOut();
				}
				
				if (json.redirect) {
					window.location.href = json.redirect;
				}
				
				if (json.refresh) {
					window.location.reload();
				}
				
				if (json.back && document.referrer.length > 0) {
					window.location.href = document.referrer;
				}
				
				if (json.text) {
					$button.html(json.text);
				}
				
				if (json.promptText) {
					$button.data('prompt-text', json.promptText);
				}
				
				if (json.params) {
					var params = {};
					if (typeof $button.data('params') != 'undefined') {
						params = json_decode(base64_decode($button.data('params')))
					}
					for (var key in json.params) {
						params[key] = json.params[key];
					}
					
					$button.data('params', base64_encode(json_encode(params)));
				}
				
				if (json.color) {
					for (var i = 0; i < BootstrapUI.colors.length; i++) {
						var color = BootstrapUI.colors[i];
						$button.removeClass('btn-' + color);
					}
					
					$button.addClass(json.color)
				}
			} else {
				// todo: error
			}
		}
		// todo: handle error & offline
	});
};