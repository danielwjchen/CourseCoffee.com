/**
 * Provide basic features to demostation desired functionality for editor v2
 */
$(document).ready(function() {
	var offset = $('.task-wrap').offset();
	var newTop = offset.top - 120;
	$('body').animate({scrollTop: newTop}, 1000);
	console.log('dddddddddddd');
});
