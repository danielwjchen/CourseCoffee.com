/**
 * Handle Document upload and process request
 */
window.doc = {
	/**
	 * Initialize the upload process
	 */
	'init' : function() {
		doc.form = $('doc-upload-form');
		$('body', $P).append('<div class="dialog-mesh">' + 
			'<div class="upload dialog">' + 
				'<div class="dialog-inner">' + 
				'<h2>Please select syllabus documents to upload (.pdf, .doc, .docx, .html, .txt, e.t.c)</h2>' +
				'</div>' + 
			'</div>' + 
		'<div>');
		$('.dialog-inner', $P).live('click', function(e) {
			e.stopPropagation();
		});
		$('.dialog-mesh', $P).live('click', function(e) {
			if ($(e.currentTarget).hasClass('dialog-mesh')) {
				doc.cancel();
			}
		});
	},
	/**
	 * Cancel the process
	 */
	'cancel' : function() {
		$('.dialog-mesh', $P).remove();
	},
	/**
	 * Generate the HTML form for user registration
	 */
	'form': function(email, password, confirm, token, error) {
		return '<div class="user-registration dialog">' +
			'<form id="user-registration-form" name="registration" action="user/register" method="post">' +
				'<input type="hidden" name="token" value="' + token + '" />' +
				'<div class="row error hidden"></div>' +
				'<div class="row">' +
					'<div class="title">' +
						'<label for="user-account">email: </label>' +
					'</div>' +
					'<div class="field">' +
						'<input type="text" name="email" value="' + email + '" />' +
					'</div>' +
				'</div>' +
				'<div class="row">' +
					'<div class="title">' +
						'<label for="password">password: </label>' +
					'</div>' +
					'<div class="field">' +
						'<input type="password" name="password" value="' + password + '" />' +
					'</div>' +
				'</div>' +
				'<div class="row">' +
					'<div class="title">' +
						'<label for="confirm">confirm password: </label>' +
					'</div>' +
					'<div class="field">' +
						'<input type="confirm" name="confirm" value="' + confirm + '" />' +
					'</div>' +
				'</div>' +
				'<a href="#" class="button register">Join</a>' +
			'</form>' +
		'</div>';
	}
};
