<?php
/**
 * @file
 * Base class of all views
 */
abstract class View {

	/**
	 * An array of content to be generated
	 */
	protected $content;

	/**
	 * Default constructor
	 *
	 * @param array $content
	 *  an associative array to be converted to JSON string
	 */
	function __construct($content) {
		$this->content = $content;
	}

	/**
	 * Set HTTP Header
	 *
	 * @param string $type
	 *  a string that indicates the type of header to be set
	 */
	public function setHeader($type) {
		header($type);
		header('Cache-Control: no-cache, must-revalidate');
	}
}
