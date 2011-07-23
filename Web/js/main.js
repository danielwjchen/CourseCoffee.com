window.$P = $(document);
window.updatePageHeight = function() {
	var newHeight = body.outerHeight(true) + header.outerHeight(true) + 150;
	newHeight = (newHeight > 800) ? newHeight : 800;
	if ($('.container').height() < newHeight) {
		$('.container').height(newHeight);
	}
};

/**
 * Dynamically hide/show the default input value
 *
 * @param region
 *  a HTML region to apply
 */
window.blurInput = function(region) {
	this.inputs = $(':input', region);
	$(':input', body).each(function(index){
		$(this).attr('default', $(this).val());
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
	window.body = $("div.body");
	window.header = $(".header");
	$(".login .action").click(function() {
		body.removeClass('welcome');
		body.addClass('home');
		navigation.load('navigation.php');
		body.load('home.php', function() {
			updatePageHeight();
		});
	});

});
