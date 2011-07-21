<?php
/**
 * @file
 * Base class for all views that generates a text/HTML response
 */
abstract class PageView extends View {

	const CONTENT_TYPE = 'text/html; charset=utf-8';

	/**
	 * HTML header
	 */
	const HTML_HEADER = 'Content-type: text/html';

	/**
	 * Override the default constructor
	 *
	 * @param array $content
	 *  an associative array that holds blocks to be rendered into a HTML page
	 *  - header
	 *  - body
	 *  - footer
	 */
	function __construct($content) {
		$this->content['header']['block'] = $content['header']['block'];
		$this->content['body']['block'] = $content['body']['block'];
		$this->content['footer']['block'] = $content['footer']['block'];

		 // JavaScript to be added to the page
		$this->content['js'] = '';
		 // CSS to be added to the page
		$this->content['css'] = '';
		// meta tags to be added to the page
		$this->content['meta'] = '';
		
		$this->content['page']['title'] = 'CourseCoffee.com';
		$this->addMeta(array(
			'http-equiv' => 'content-type',
			'content' => 'text/html;charset=UTF-8',
		));
		$this->addJS('lib/jquery-1.4.4.min.js');
		$this->addJS('main.js');
		$this->addCSS('elements.css');
		$this->addCSS('layout.css');
		$this->addCSS('main.css');
		$this->addCSS('navigation.css');
	}
	
	/**
	 * Add javascript to a page
	 *
	 * @param string $js
	 *  this could be either a path to a javascript file or plain inline 
	 *  javascript
	 * @param string $option
	 *  a flag that indicates the type of data being based possible values:
	 *   - inline: 
	 *      the javascript code that would be inserted in the page
	 *   - file: 
	 *      the path to a javascript file relative to /js
	 *   - external
	 *      the URI to a external javascript file source
	 */
	public function addJS($js, $option = 'file') {
		switch ($option) {
			case 'file':
				$this->content['js'] .= <<<JS
<script type="text/javascript" src="js/{$js}"></script>
JS;
				break;
			case 'external':
				$this->content['js'] .= <<<JS
<script type="text/javascript" src="{$js}"></script>
JS;
				break;
			case 'inline':
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
				$this->content['css'] .= <<<CSS
<link rel="stylesheet" type="text/css" href="css/{$css}" />
CSS;
				break;
			case  'external':
				$this->content['css'] .= <<<CSS
<link rel="stylesheet" type="text/css" href="{$css}" />
CSS;
				break;
		}
	}

	/**
	 * Add meta tags to a page
	 */
	public function addMeta($meta) {
		$string = '';
		foreach ($meta as $key => $value) {
			$string .= "{$key} ='{$value}' ";
		}
		$this->content['meta'] .= <<<META
<meta {$string} />
META;
	}

	/**
	 * Render the pieces together and send it to the user
	 */
	public function render() {
		$this->setHeader(self::HTML_HEADER);
		extract($this->content);
		include TEMPLATE_PATH . '/welcome.page.tpl';
	}

}
