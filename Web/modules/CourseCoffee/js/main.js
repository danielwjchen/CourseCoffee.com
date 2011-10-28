/**
 * @file
 * Define default constants and utility functions
 */
window.$P = $(document);

$P.ready(function() {
	window.body = $(".body");
	window.header = $(".header");
	if(
		 ($.browser.msie && parseInt($.browser.version, 10) < 9) ||
		 ($.browser.mozilla && parseInt(jQuery.browser.version, 10) < 2) ||
		 ($.browser.safari && parseInt(jQuery.browser.version, 10) < 5)
	){
		$('.system-message').removeClass('hidden');
		$('.system-message-inner').html('<p>In order to experience the full awesomeness of CourseCoffee.com, we recommend upgrading your browser to Google Chrome, Firefox 4.0+, Safari 5.0+ or IE 9.0+</p>');
	}
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
 * Define a generic cache handler
 */
Cache = function() {
	var storedValue = {};

	this.set = function(key, value) {
		storedValue[key] = value;
	};
	this.get = function(key) {
		return storedValue[key] ? storedValue[key] : null;
	};
	this.unset = function(key) {
		storedValue[key] = null;
	};
	this.flush = function() {
		storedValue = {};
	};
};

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
		//$('body').addClass('no-scroll');
		$('.body', $P).after('<div class="dialog-mesh"></div>' + 
		'<div class="dialog-wrapper">' + 
			'<div class="' + type + ' dialog">' + 
				'<div class="dialog-inner">' + 
					'<a href="#" class="dialog-close">' + 
						'<span class="hidden">close</span>' +
					'</a>' +
					content + 
				'</div>' + 
			'</div>' + 
		'<div>');
		$('.dialog-inner', $P).live('click', function(e) {
			e.stopPropagation();
		});
		
		/*
		$('.dialog-wrapper', $P).live('click', function(e) {
			dialog.close();
		});
		*/
	},
	/**
	 * Close a dialog
	 */
	'close' : function() {
		//$('body').removeClass('no-scroll');
		$('.dialog-mesh', $P).remove();
		$('.dialog-wrapper', $P).remove();
	}
}

/**
 * Manage access to cookie
 */
