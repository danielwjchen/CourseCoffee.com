/**
 * @file
 * Define popup modal
 *
 * The modal style is defined in css/modal.css
 */
window.Modal = function() {
	/**
	 * Open a modal
	 *
	 * @param type
	 *  a CSS class value to define the modal, e.g. upload, warning
	 * @param content
	 *  the content to be displayed within the modal
	 */
	this.open = function(type, content) {
		//$('body').addClass('no-scroll');
		$('.body', $P).after('<div class="modal-mesh"></div>' + 
		'<div class="modal-wrapper">' + 
			'<div class="' + type + ' modal">' + 
				'<div class="modal-inner">' + 
					'<a href="#" class="modal-close">' + 
						'<span class="hidden">close</span>' +
					'</a>' +
					content + 
				'</div>' + 
			'</div>' + 
		'<div>');
		$('.modal-inner', $P).live('click', function(e) {
			e.stopPropagation();
		});
		
		/*
		$('.modal-wrapper', $P).live('click', function(e) {
			modal.close();
		});
		*/
	};

	/**
	 * Close a modal
	 */
	this.close = function() {
		//$('body').removeClass('no-scroll');
		$('.modal-mesh', $P).remove();
		$('.modal-wrapper', $P).remove();
	}
}
