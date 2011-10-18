/**
 * @file
 * Redirect user automatically on campus selection
 */
$P.ready(function() {
	$('select[name=institution-domain-options]').change(function(e) {
		window.location = window.location.protocol + '//' + $(this).val();
	});
});
