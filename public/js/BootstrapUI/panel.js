$(document).ready(function() {
	var els = $('.x-panel-collapsible');
	for (var i = 0; i < els.length; i++) {
		var $a = $(els[i]);
		var $panel = $a.closest('.panel');
		switch ($panel.data('collapsed')) {
			case true:
			case 'true':
				$a.html('<span class="glyphicon glyphicon-' + $a.data('down') + '"></span>');
				break;
			
			case false:
			case 'false':
				$a.html('<span class="glyphicon glyphicon-' + $a.data('up') + '"></span>');
				break;
		}
		
		$a.click(function() {
			var $a = $(this);
			var $panel = $a.closest('.panel');
			switch ($panel.data('collapsed')) {
				case true:
				case 'true':
					$a.html('<span class="glyphicon glyphicon-' + $a.data('up') + '"></span>');
					$panel.find('.panel-body').slideUp();
					$panel.find('.panel-footer, table').hide();
					$panel.find('.panel-heading > .x-panel-header-elements form').hide();
					$panel.data('collapsed', 'false');
					break;
					
				case false:
				case 'false':
					$a.html('<span class="glyphicon glyphicon-' + $a.data('down') + '"></span>');
					$panel.find('.panel-body').slideDown();
					$panel.find('.panel-footer, table').show();
					$panel.find('.panel-heading > .x-panel-header-elements form').show();
					$panel.data('collapsed', 'true');
					break;
			}
			
			return false;
		});
	}
});