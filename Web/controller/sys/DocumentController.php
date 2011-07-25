<?php
/**
 * @file
 * Handle requests to upload and process a document/syllabus
 */
class DocumentController extends Controller implements ControllerInterface {

	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'doc/init'    => 'initDocument',
			'doc/process' => 'processDocument',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {

	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function afterAction() {
	}

	/**
	 * Initialize the system to handle document requests
	 */
	public function initDocument() {
	}

	/**
	 * Handle the doc process requests
	 */
	public function processDocument() {
	}

}
