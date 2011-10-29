<?php
/**
 * @file
 * Generate JSON output
 */
class JSONView extends View implements ViewInterface {

	/**
	 * JSON header
	 */
	const JSON_HEADER = 'Content-type: application/json';

	/**
	 * Override View::getHeader()
	 */
	protected function getHeader() {
    header(self::STATUS_OK);
    header(self::JSON_HEADER);
	}

	/**
	 * Implement ViewInteface::render()
	 *
	 * Convert content to JSON string and send it
	 */
	public function render() {
		$this->setHeader();
    return str_replace(
			array('<', '>', '&'), 
			array('\u003c', '\u003e', '\u0026'), 
			json_encode($this->data, JSON_FORCE_OBJECT)
		);
	}
}
