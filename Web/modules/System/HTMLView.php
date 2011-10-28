<?php
/**
 * @file
 * Base class for all views that generates a text/HTML response
 */
abstract class HTMLView extends View {
	
	/**
	 * Add javascript to a page
	 *
	 * @param string $js
	 *  this could be either a path to a javascript file or plain inline 
	 *  javascript
	 * @param string $option
	 *  a flag that indicates the type of data being based possible values:
	 *   - internal: 
	 *      the path to a javascript file relative to /js
	 *   - external
	 *      the URI to a external javascript file source
	 */
	protected function addJS($js, $option = 'internal') {
		switch ($option) {
			case 'internal':
				$this->data['js'][$option][] = "/js/{$js}.js";
				break;
			case 'external':
				$this->data['js'][$option][] = $js;
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
	protected function addCSS($css, $option = 'internal') {
		switch ($option) {
			case 'internal':
				$this->data['css'][$option][] = "/css/{$css}.css";
				break;
			case  'external':
				$this->data['css'][$option][] = $css;
				break;
		}
	}
}
