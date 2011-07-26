/**
 * @file
 * Define default constants and utility functions
 */
window.$P = $(document);

$P.ready(function() {
	window.body = $(".body");
	window.header = $(".header");
});

/**
 * Dynamically hide/show the default input value
 *
 * @param region
 *  a HTML region to apply
 */
window.blurInput = function(region) {
	this.inputs = $(':input', region);
	$(':input', region).each(function(index){
		// we don't blur hidden inputs
		if ($(this).attr('type') != 'hidden') {
			$(this).attr('default', $(this).val());
		}
	})
	inputs.focus(function(e) {
		if ($(this).val() == $(this).attr('default')) {
			$(this).val('');
		}
	});
	inputs.blur(function(e) {
		if ($(this).attr('default') && $(this).val() == '') {
			$(this).val($(this).attr('default'));
		}
	});
}

/**
 * Define popup dialog
 *
 * The dialog style is defined in css/dialog.css
 */
window.dialog = {
	/**
	 * Open a dialog
	 *
	 * @param type
	 *  a CSS class value to define the dialog, e.g. upload, warning
	 * @param content
	 *  the content to be displayed within the dialog
	 */
	'open' : function(type, content) {
		$('.body', $P).after('<div class="dialog-mesh"></div>' + 
		'<div class="dialog-wrapper">' + 
			'<div class="' + type + ' dialog">' + 
				'<div class="dialog-inner">' + 
					content + 
				'</div>' + 
			'</div>' + 
		'<div>');
		$('.dialog-inner', $P).live('click', function(e) {
			e.stopPropagation();
		});
		$('.dialog-wrapper', $P).live('click', function(e) {
			dialog.close();
		});
	},
	/**
	 * Close a dialog
	 */
	'close' : function() {
		$('.dialog-mesh', $P).remove();
		$('.dialog-wrapper', $P).remove();
	}
}

