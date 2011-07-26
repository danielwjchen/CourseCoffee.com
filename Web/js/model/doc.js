/**
 * Handle Document upload and process request
 */
window.doc = {
	/**
	 * Initialize the upload process
	 */
	'init' : function() {
		doc.form = $('doc-upload-form');
		content = '<h2>Please select syllabus documents to upload (.pdf, .doc, .docx, .html, .txt, e.t.c)</h2>';
		dialog.open('upload', content);
	},
	/**
	 * Cancel the process
	 */
	'cancel' : function() {
		display.close();
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
