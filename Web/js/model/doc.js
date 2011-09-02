/**
 * Handle Document upload and process request
 */
window.doc = {
	/**
	 * Initialize the upload process
	 */
	'init' : function() {
		dialog.open('upload', '');
		var message = '<h2>From doc to data. Easy as ABC.</h2>' + 
		'<p><strong>Upload your syllabus, and we will extract the assignments and arrange them into a nice calendar.</strong></p>' +
		'<img src="/images/syllabus-to-calendar.png" />' +
		'<p><strong>Ingenious? We think so.</strong></p>';
		doc.createForm('.dialog-inner', message);

		$('.dialog-close', $P).live('click', function(e) {
			e.preventDefault();
			dialog.close()
		});

	},
	/**
	 * Create document form
	 *
	 * @param string regionName
	 *  the id or class name of the resion which this form will be appended to.
	 * @param string message
	 *  a message to guide the user through the process
	 */
	'createForm' : function (regionName, message) {
		var form = $('#doc-upload-form-skeleton').clone();
		form.attr('id', 'doc-upload-form');
		$.ajax({
			url: '/doc-init',
			type: 'POST',
			success: function(response) {
				if (response.token) {
					$('input[name=token]', form).attr('value', response.token);
				} 
			}
		});
		form.appendTo(regionName);
		form.before('<h3>' + message + '</h3>');
		form.removeClass('hidden');
		form.delegate('a.submit', 'click', function(e) {
			e.preventDefault();
			if ($('input[name=document]', form).val() == '') {
				$('.error', form).html('<p>Must select a file</p>');
				$('.error', form).removeClass('hidden');
			} else {
				$('.error', form).addClass('hidden');
				form.submit();
			}
		});
	},
};
