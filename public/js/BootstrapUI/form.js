BootstrapUI.Form = BootstrapUI.Form || {};

BootstrapUI.Form.bindSubmitButton = function() {
    $('.x-form button[type=submit]', $(this).parents('form')).removeAttr('clicked');
    $(this).attr('clicked', 'true');
};

BootstrapUI.Form.bindInputFileChange = function(e) {
	var $form = $(this).closest('form');
	var limit = $form.data('file-count-limit');
	var maxSize = $form.data('max-size');
	
	if (typeof limit != 'undefined' && typeof maxSize != 'undefined') {
		limit = parseInt(limit);
		maxSize = parseInt(maxSize);
		
		if (this.files.length > limit) {
			alert('You can select maximum of ' + limit + ' file(s)');
			$(this).val('');
			return;
		}
		
		for (var i = 0, total = 0; i < this.files.length; i++) {
			total += this.files[i].size;
		}
		
		if (total > maxSize) {
			alert('Total size of selected files is bigger then server can accept it! (max: ' + Math.round(maxSize / 1024 / 1024) + 'MB)');
			$(this).val('');
			return;
		}
	}
	
	var allowedTypes = $(this).attr('accept');
	if (typeof allowedTypes != 'undefined' && allowedTypes.length > 0) {
		allowedTypes = allowedTypes.split(',');
		for (var i = 0; i < this.files.length; i++) {
			var file = this.files[i];
			if (!in_array(file.type, allowedTypes)) {
				alert(file.type + ' is not allowed file type. Please select another file(s).');
				$(this).val('');
				return;
			}
		}
	}
};

BootstrapUI.Form.handleFormResponse = function($form, json) {
	$form.find('input:not(.x-disabled), select:not(.x-disabled), textarea:not(.x-disabled), button:not(.x-disabled)').attr('disabled', false);
	$form.find('.help-block').html('');
	if (json.success && json.success === true) {
		if (json.helpText) {
			$form.find('.help-block').html(json.helpText);
		}

		if (json.icon) {
			$form.find('.x-status-icon').html(json.icon);
		}

		if (json.fieldValues) {
			for (var field in json.fieldValues) {
				$form.find('[name=' + field + ']').val(json.fieldValues[field]);
			}
		}
		
		if (json.fieldErrors) {
			for (var field in json.fieldErrors) {
				var x = json.fieldErrors[field];
				var parent = $form.find('[name=' + field + ']').parent();
				parent.addClass('has-' + x.state)
				
				if (x.message) {
					parent.append('<span class="x-invalid-field help-block" style="display:none">' + x.message + '</span>');
				}
			}
			$form.find('.x-invalid-field').fadeIn();
		}

		if (json.refresh && json.refresh === true) {
			window.location.reload();
			return;
		}
		
		if (json.refreshTable) {
			var $table = $('#' + json.refreshTable);
			if ($table.length == 1) {
				BootstrapUI.Table.Remote.reload($table);
			}
		}
		
		if (json.redirect) {
			window.location.href = json.redirect;
		}
		
		if (json.back && document.referrer.length > 0) {
			window.location.href = document.referrer;
		}
	} else {
		$form.find('.help-block').html('Something went wrong.');
	}
}

BootstrapUI.Form.bindFormSubmit = function() {
	var $form = $(this);

	var ajaxParams = {
		url: $form.attr('action'),
		type: 'POST',
		cache: false,
		data: $form.serialize(),
		$form: $form,

		success: function(json) {
			BootstrapUI.Form.handleFormResponse(this.$form, json);
		},
		failure: function() {
			this.$form.find('input, select, textarea, button').attr('disabled', false);
			this.$form.find('.help-block').html('Something went wrong.');
		},
		error: function() {
			this.$form.find('input, select, textarea, button').attr('disabled', false);
			this.$form.find('.help-block').html('Something went wrong.');
		}
	};

	var isFileUpload = $form.find('input[type=file]').length > 0;
	if (isFileUpload) {
		var limit = $form.data('file-count-limit');
		var maxSize = $form.data('max-size');
		
		if (typeof limit != 'undefined' && typeof maxSize != 'undefined') {
			limit = parseInt(limit);
			maxSize = parseInt(maxSize);
			var totalFiles = 0, totalSize = 0;
			
			// searching for all files in the form and checking sizes before upload
			var els = $form.find('input[type=file]');
			for (var i = 0; i < els.length; i++) {
				var file = els[i];
				totalFiles += file.files.length;
				
				if (totalFiles > limit) {
					alert('Hole form can post maximum of ' + limit + ' file(s). Please remove some files.');
					return false;
				}
				
				totalSize += file.files[i].size;
				
				if (totalSize > maxSize) {
					alert('Total size of all selected files is bigger then server can accept it! (max: ' + Math.round(maxSize / 1024 / 1024) + 'MB)\nPlease remove some files.');
					return;
				}
			}
		}

		ajaxParams.contentType = !isFileUpload;
	    ajaxParams.processData = !isFileUpload;
	    
		ajaxParams.xhr = function() {  // custom xhr
			var $progress = $form.find('.x-upload-progress');
			myXhr = $.ajaxSettings.xhr();
			if(myXhr.upload && isFileUpload){ // check if upload property exists
				//for handling the progress of the upload
				myXhr.upload.addEventListener('progress', function(e) {$progress.html(Math.round(e.position / e.total *100) + '%');}, false); 
				myXhr.upload.addEventListener('error', function(e) {$progress.html('error occured');}, false); 
				myXhr.upload.addEventListener('abort', function(e) {$progress.html('aborted');}, false); 
			}
			return myXhr;
		};
		
		ajaxParams.data = new FormData(this);
	}
	
	var $submits = $form.find('button[type=submit]');
	if ($submits.length > 1) {
		if (isFileUpload) {
			ajaxParams.data.append('submit_button', $form.find('button[type=submit][clicked=true]').attr('name'));
		} else {
			ajaxParams.data += '&submit_button=' + $form.find('button[type=submit][clicked=true]').attr('name');
		}
	}
	
	// UI stuff
	$form.find('input:not(.x-disabled), select:not(.x-disabled), textarea:not(.x-disabled), button:not(.x-disabled)').attr('disabled', 'disabled');
	$form.find('.x-status-icon').html(BootstrapUI.loaderImg + ' <span class="x-upload-progress"></span>');
	$form.find('.form-group')
		.removeClass('has-success')
		.removeClass('has-warning')
		.removeClass('has-error');
	
	$form.find('.x-invalid-field').remove();
	
	$.ajax(ajaxParams);
	
	return false;
};

$(document).ready(function() {
	var els = $('.x-form button[type=submit]');
	for (var i = 0; i < els.length; i++) {
		$(els[i]).click(BootstrapUI.Form.bindSubmitButton);
	}
	
	var els = $('.x-form');
	for (var i = 0; i < els.length; i++) {
		var $form = $(els[i]);
		//if ($form.find('input[type=file]').length == 0 || $form.data('prevent') == 'ajax') {
		$form.submit(BootstrapUI.Form.bindFormSubmit);
	}
	
	// bind input type files checks
	var els = $('.x-form input[type=file]');
	for (var i = 0; i < els.length; i++) {
		$(els[i]).change(BootstrapUI.Form.bindInputFileChange);
	}
});
