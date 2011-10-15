<?php
/**
 * @file
 * Base class for views that produce HTML output
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
abstract class HTMLView extends View {
	
	/**
	 * Add javascript to ouput
	 *
	 * @param string $js
	 *  path to a javascript file
	 * @param string $option
	 *  a flag that indicates the type of data being based possible values:
	 *   - internal: 
	 *      the path to a CSS file relative to /css
	 *   - external
	 *      the URI to a external CSS file source
	 */
	protected function addJS($js, $option = 'internal') {
		if ($option == 'internal') {
			$this->data['js'][$option][] = "/js/{$js}.js";
		} elseif ($option == 'external') {
			$this->data['js'][$option][] = $js;
		}
	}

	/**
	 * Add CSS to output
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
		if ($option == 'internal') {
			$this->data['css'][$option][] = "/css/{$css}.css";
		} elseif ($option == 'external') {
			$this->data['css'][$option][] = $css;
		}
	}
}
