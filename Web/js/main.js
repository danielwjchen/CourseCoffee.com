window.$P = $(document);

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


$P.ready(function() {
	window.body = $(".body");
	window.header = $(".header");
});
