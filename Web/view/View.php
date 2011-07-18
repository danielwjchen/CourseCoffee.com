<?php
/**
 * @file
 * Base class of all views
 */
abstract class View {

	public function toHTMLBlock($html) {
	}

	public function toHTMLPage($html) {
		return <<<HTML
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
	<head>
	</head>
	<body>
		{$html}
	</body>
</html>
HTML;
	}

	public function toJSON($json) {
	}

}
