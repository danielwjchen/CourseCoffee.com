<?php
/**
 * @file
 * Handle requests to upload and process a document/syllabus
 */
class DocumentController extends Controller implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function path() {
		return array(
			'doc-init'    => 'issueDocToken',
			'doc-upload'  => 'uploadDocument',
			'doc-process' => 'processDocument',
		);
	}

	/**
	 * Override Controller::beforeAction()
	 */
	public function beforeAction() {
	}

	/**
	 * Issue a file token
	 */
	public function issueDocToken() {
		$file = new FileFormModel($this->sub_domain);
		$this->output = new JSONView(array(
			'token' => $file->initializeFormToken(),
		));
	}
	/**
	 * Upload a document
	 */
	public function uploadDocument() {
		$file_form = new FileFormModel($this->sub_domain);
		$token    = Input::Post('token');
		$user_id  = $this->getUserId();
		// we default visitor's user_id = 1
		$user_id  = empty($user_id) ? 1 : $user_id;
		$filename = 'document';
		// process form and save the file
		$result = $file_form->processForm($user_id, $token, $filename, FileType::SYLLABUS);
		$this->redirect('/doc-edit&file_id=' . $result['file_id'] . '&doc-type=' . $result['mime'] . '&document=' . $result['encoded']);
	}

	/**
	 * Handle the doc process requests
	 */
	public function processDocument() {
		$processor = new DocumentProcessorFormModel($this->sub_domain);
		$token    = Input::Post('token');
		$mime     = Input::Post('mime');
		$document = Input::Post('document');
		$result = $processor->processDocument($document, $mime, $token);
		$this->output = new JSONView($result);
	}

}
