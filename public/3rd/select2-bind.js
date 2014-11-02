$(document).ready(function() {
	var els = $('.select2');
	for (var i = 0; i < els.length; i++) {
		var $el = $(els[i]);
		var config = json_decode(base64_decode($el.data('config')));
		$('#' + $el.attr('id')).select2(config);
	}
});