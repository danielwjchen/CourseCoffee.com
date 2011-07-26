<?php
/**
 * @file
 * Process documents
 */
class DocumentProcessorFormModel extends FormModel{

	/**
	 * @defgroup error_messages
	 * @{
	 * Error message for the user when an error is encountered
	 */

	const ERROR_FAIL      = 'The document could not be processed.';
	const ERROR_MAX_TIME  = 'The document took too long to process.';

	/**
	 * @} End of error_messages
	 */

	/**
	 * Extend Model::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Process the document
	 *
	 * @param string $doc
	 *  
	 */
	public function processDocument($doc) {
		$doc = escapeshellcmd($doc);
		$doc = escapeshellarg($doc);
		$output = null;
		exec('pdftotext ' . FILE_PATH . '/' . $doc . ' - ', $output);
		$result = implode("<br />", $output);
		return $result;
	}

}
