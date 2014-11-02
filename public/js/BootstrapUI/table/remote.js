BootstrapUI.Table = BootstrapUI.Table || {};
BootstrapUI.Table.Remote = BootstrapUI.Table.Remote || {};

BootstrapUI.Table.Remote.reload = function($table) {
	$table.closest('.x-panel-remote').find('.panel-footer > .row > .x-remote-table-pagination').html('<img src="/img/ajax-loader.gif" />');

	var data = {};
	var params = json_decode(base64_decode($table.data('extra-params')));

	for (var key in params) {
		data[key] = params[key];
	}

	data.page = $table.data('page');
	data.limit = $table.data('limit');
	data.field = $table.data('field');
	data.dir = $table.data('dir').toLowerCase();

	$.ajax({
		url: $table.data('url'),
		method: 'POST',
		data: data,
		$table: $table,
		success: function(json) {
			this.$table.find('tbody').children().remove();
			var $footer = this.$table.closest('.x-panel-remote').find('.panel-footer > .row');

			if (json.data) {
				this.$table.find('tbody').html(json.data);
			}

			if (json.info) {
				$footer.find('.x-remote-table-info').html(json.info);
			}

			if (json.pagination) {
				$footer.find('.x-remote-table-pagination').html(json.pagination);
			} else {
				$footer.find('.x-remote-table-pagination').html('<button class="btn btn-primary btn-xs x-remote-table-refresh"><span class="glyphicon glyphicon-refresh"></span></button>');
				$footer.find('.x-remote-table-refresh').on('click', function(e) {
					BootstrapUI.Table.Remote.reload($(this).closest('.x-panel-remote').find('.x-table-remote'));
				});
			}

			$footer.find('.x-remote-table-pagination > a').click(function(event) {
				var $a = $(this);
				var page = $a.data('page');
				var $table = $a.closest('.x-panel-remote').find('.x-table-remote');
				$table.data('page', page);
				BootstrapUI.Table.Remote.reload($table);
				return false;
			});

			// adjust sortable column
			var els = this.$table.find('.x-remote-sortable-column > a');
			for (var i = 0; i < els.length; i++) {
				var $a = $(els[i]);
				$a.html($a.data('label'));
			}

			var $a = this.$table.find('.x-remote-sortable-' + this.$table.data('field'));
			$a.html($a.data('label') + ' ' + (this.$table.data('dir') == 'asc' ? '&#9650;' : '&#9660;'));
		}
	});
};

/**
 * Get all remote tables and make ajax request
 */
$(document).ready(function() {
	var els = $('.x-table-remote');
	for (var i = 0; i < els.length; i++) {
		var $table = $(els[i]);
		// we have the single $table now, bind everything needed
		BootstrapUI.Table.Remote.reload($table);
		$table.find('.x-remote-sortable-column > a').click(function(event) {
			var $a = $(this);
			var targetField = $a.data('field');
			var $table = $a.closest('table');

			if ($table.data('field') != targetField) {
				$table.data('field', targetField);
			} else {
				$table.data('dir', $table.data('dir') == 'asc' ? 'desc' : 'asc');
			}

			BootstrapUI.Table.Remote.reload($table);
			return false;
		});
	}
	
	var els = $('.x-remote-search-form');
	for (var i = 0; i < els.length; i++) {
		var $form = $(els[i]);
		var $input = $form.find('input');
		var $reset = $form.find('button[type=reset]');
		
		$input.on('focus', function() {
			$(this).css('width', 180);
		});
		
		$input.on('blur', function() {
			if ($(this).val() == '') {
				$(this).css('width', 80);
			}
		});
		
		$reset.on('click', function() {
			$(this).parent().find('input[type=search]').val('').blur();
			$(this).closest('form').submit();
		});
		
		$form.submit(function(e) {
			var $form = $(this);
			var $input = $form.find('input[type=search]');
			var $table = $form.closest('.x-panel-remote').find('table.x-table-remote');
			var params = {};
			var extraParams = json_decode(base64_decode($table.data('extra-params')));
			for (var key in extraParams) {
				params[key] = extraParams[key];
			}
			params.search = $input.val();
			$table.data('extra-params', base64_encode(json_encode(params)));
			$table.data('page', 1);
			BootstrapUI.Table.Remote.reload($table);
			return false;
		});
	}
	
});