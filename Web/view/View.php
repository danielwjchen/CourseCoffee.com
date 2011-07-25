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
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
    header('HTTP/1.1 200 OK');
		header("Pragma: no-cache");
		header($type);
	}
}
