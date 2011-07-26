/**
 * Handle Document upload and process request
 */
window.doc = {
	/**
	 * Initialize the upload process
	 */
	'init' : function() {
		var form = $('#doc-upload-form-skeleton').clone();
		form.attr('id', 'doc-upload-form');
		content = '<h2>Please select syllabus documents to upload (.pdf, .doc, .docx, .html, .txt, e.t.c)</h2>';
		dialog.open('upload', content);
		form.appendTo('.dialog-inner');
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
	/**
	 * Cancel the process
	 */
	'cancel' : function() {
		display.close();
	}
};
