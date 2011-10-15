/**
 * @file
 * Define default constants and utility functions
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
window.$P = $(document);

$P.ready(function() {
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
