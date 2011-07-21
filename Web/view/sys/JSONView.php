<?php
/**
 * @file
 * Generate JSON output
 */
class JSONView extends View{

	/**
	 * JSON header
	 */
	const JSON_HEADER = 'Content-type: application/json';
	
	/**
	 * Convert content to JSON string and send it
	 */
	public function render() {
		$this->setHeader(self::JSON_HEADER);
    print str_replace(
			array('<', '>', '&'), 
			array('\u003c', '\u003e', '\u0026'), 
			json_encode($this->content, JSON_FORCE_OBJECT)
		);
	}
}
