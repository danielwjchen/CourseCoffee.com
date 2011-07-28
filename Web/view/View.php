<?php
/**
 * @file
 * Base class of all views
 */

interface ViewInterface {
	/**
	 * Render the pieces together.
	 *
	 * Note that this function is called by controllers automatically, usually. 
	 * It also acts more or less the template for all HTML pages.
	 *
	 * @return string
	 *  a rendered HTML output
	 */
	public function render();
	
}
abstract class View {

	/**
	 * @defgroup http_status
	 * @{
	 * Define possible HTTP status codes
	 */
	const STATUS_OK      = 'HTTP/1.1 200 OK';
	const UNAUTHORIZED   = 'HTTP/1.1 401 Unauthorized';
	const FORBIDDEN      = 'HTTP/1.1 403 Forbidden';
	const NOT_FOUND      = 'HTTP/1.0 404 Not Found';
	const INTERNAL_ERROR = 'HTTP/1.1 500 Internal Server Error';
	/**
	 * @} End of "http_status"
	 */

	/**
	 * An array of data to be generated
	 */
	protected $data;

	/**
	 * Implement View::getHeader()
	 *
	 * This is defaulted to 200 OK, but some pages might want to change it, e.g. 
	 * 404 not found.
	 */
	protected function getHeader() {
    header(self::STATUS_OK);
	}

	/**
	 * Default constructor
	 *
	 * @param array $data
	 *  an associative array to be converted to JSON string
	 */
	function __construct($data) {
		$this->data = $data;
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
		header("Pragma: no-cache");
		$this->getHeader();
	}
}
