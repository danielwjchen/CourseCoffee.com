<?php
/**
 * @file
 * Base class for all views that generates a text/HTML response
 */
abstract class PageView extends View {

	/**
	 * Add javascript to a page
	 *
	 * @param string $data
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
	public function addJS($data, $option) {
	}

	/**
	 * Add CSS to a page
	 *
	 * @param string $data
	 *  this could be either a path to a CSS file or plain CSS
	 * @param string $option
	 *  a flag that indicates the type of data being based possible values:
	 *   - inline: 
	 *      the CSS code that would be inserted in the page
	 *   - file: 
	 *      the path to a CSS file relative to /js
	 *   - external
	 *      the URI to a external CSS file source
	 */
	public function addCSS($data, $option) {
	}

}
