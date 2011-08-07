<?php
/**
 * @file
 * Base class for all views that generates a text/HTML response
 */
interface PageViewInterface {

	/**
	 * Define the data of the page
	 */
	public function getContent();

}

abstract class PageView extends View implements ViewInterface {

	const CONTENT_TYPE = 'text/html; charset=utf-8';

	/**
	 * HTML header
	 */
	const HTML_HEADER = 'Content-type: text/html';

	/**
	 * Override the default constructor
	 *
	 * @param array $data
	 *  an associative array that holds blocks to be rendered into a HTML page
	 *  - header
	 *  - body
	 *  - footer
	 */
	function __construct($data) {
		$this->data = $data;

		 // JavaScript to be added to the page
		$this->data['js'] = array();
		 // CSS to be added to the page
		$this->data['css'] = array();
		
		$this->setPageTitle('CourseCoffee.com');
		$this->addMeta(array(
			'http-equiv' => 'data-type',
			'data' => 'text/html;charset=UTF-8',
		));
		$this->addMeta(array(
			'http-equiv' => 'Pragma',
			'data' => 'no-cache'
		));
		$this->addJQuery();
		$this->addJS('main.js');
		$this->addCSS('layout.css');
		$this->addCSS('main.css');
		$this->addCSS('navigation.css');
	}

	/**
	 * Set page title
	 *
	 * @param string $title
	 *  a string to be used as page title
	 */
	public function setPageTitle($title) {
		$this->data['page']['title'] = 'CourseCoffee.com - ' . $title;
	}
	
	/**
	 * Add javascript to a page
	 *
	 * @param string $js
	 *  this could be either a path to a javascript file or plain inline 
	 *  javascript
	 * @param string $option
	 *  a flag that indicates the type of data being based possible values:
	 *   - file: 
	 *      the path to a javascript file relative to /js
	 *   - external
	 *      the URI to a external javascript file source
	 */
	public function addJS($js, $option = 'file') {
		switch ($option) {
			case 'file':
				$this->data['js'][] = "/js/{$js}";
				break;
			case 'external':
				$this->data['js'][] = $js;
				break;
		}
	}

	/**
	 * Add CSS to a page
	 *
	 * @param string $css
	 *  this could be either the path to a CSS file
	 * @param string $option
	 *  a flag that indicates the type of data being based possible values:
	 *   - internal: 
	 *      the path to a CSS file relative to /css
	 *   - external
	 *      the URI to a external CSS file source
	 */
	public function addCSS($css, $option = 'internal') {
		switch ($option) {
			case 'internal':
				$this->data['css'][] = "/css/{$css}";
				break;
			case  'external':
				$this->data['css'][] = $css;
				break;
		}
	}

	/**
	 * Add JQuery
	 */
	protected function addJQuery() {
		$this->addJS('lib/jquery-1.6.2.js');
	}

	/**
	 * Add JQuery UI
	 */
	protected function addJQueryUI() {
		$this->data['js'][]  = "/js/lib/jquery-ui/jquery-ui-1.8.14.custom.min.js";
		$this->data['css'][] = "/js/lib/jquery-ui/themes/smoothness/jquery-ui-1.8.14.custom.css";
	}

	/**
	 * Add JQuery UI plugin
	 */
	public function addJQueryUIPlugin($name) {
		switch ($name) {
			case 'datetime':
				$this->data['js'][]  = "/js/lib/jquery-ui/plugins/datetime/jquery.ui.datetime.src.js";
				$this->data['css'][] = "/js/lib/jquery-ui/plugins/datetime/jquery.ui.datetime.css";
				break;

		}
	}

	/**
	 * Set <script> tag
	 *
	 * This is a helper function for PageView::renderJS()
	 *
	 * @param string $src
	 *  source of the javascript file
	 */
	private function setScriptTag(&$src) {
		$src =<<<JS
<script type="text/javascript" src="{$src}" ></script>
JS;
	}

	/**
	 * Get <link> tag
	 *
	 * This is a helper function for PageView::renderCSS()
	 *
	 * @param string $href
	 *  source of the CSS file
	 */
	private function setLinkTag(&$href) {
		$href =<<<CSS
<link rel="stylesheet" type="text/css" href="{$href}" />
CSS;
	}


	/**
	 * Add meta tags to a page
	 */
	public function addMeta($meta) {
		$string = '';
		foreach ($meta as $key => $value) {
			$string .= "{$key} ='{$value}' ";
		}
		$this->data['meta'] .= <<<META
<meta {$string} />\n
META;
	}

	/**
	 * Render the CSS files
	 */
	protected function renderCSS() {
		array_walk($this->data['css'], 'PageView::setLinkTag');
		return implode("\n", $this->data['css']);
		
	}

	/**
	 * Render the JS files
	 */
	protected function renderJS() {
		array_walk($this->data['js'], 'PageView::setScriptTag');
		return implode("\n", $this->data['js']);
	}

	/**
	 * Render Meta tags
	 */
	protected function renderMeta() {
	}

	/**
	 * Implement ViewInteface::render()
	 */
	public function render() {
		$js      = $this->renderJS();
		$css     = $this->renderCSS();
		$content = $this->getContent();
		$title   = $this->data['title'];

		return <<<HTML
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv='data-type' data='text/html;charset=UTF-8' /> 
		<meta http-equiv='Pragma' data='no-cache' /> 
		<title>{$title}</title>
		{$js}
		{$css}
	</head>
	<body>
		{$content}
	</body>
</html>
HTML;
	}

}
