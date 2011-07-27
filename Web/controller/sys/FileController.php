<?php
/**
 * @file
 * Oversee file upload and download requests
 */
class FileController extends Controller implements ControllerInterface {

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
			'file-upload'   => 'uploadFile',
			'file-download' => 'downloadFile',
			'file-remove'   => 'removeFile',
			'file-rename'   => 'renameFile',
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
	 * Handle the file upload requests
	 */
	public function uploadFile() {
	}

	/**
	 * Handle the file download requests
	 */
	public function downloadFile() {
	}

	/**
	 * Handle request to remove a file
	 */
	public function removeFile() {
	}

	/**
	 * Handle request to rename a file
	 */
	public function renameFile() {
	}

}
