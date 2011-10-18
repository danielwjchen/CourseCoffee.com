<?php
/**
 * @file
 * Oversee file upload and download requests
 */
class FilePageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'uploadFile' => array(
				'file-upload',
			),
			'downloadFile' => array(
				'file-download',
			),
			'removeFile' => array(
				'file-remove',
			),
			'renameFile' => array(
				'file-rename',
			),
		);
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
		header("Content-type: application/octet-stream");
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
