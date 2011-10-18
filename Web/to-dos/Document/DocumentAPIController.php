<?php
/**
 * @file
 * Handle requests to upload and process a document/syllabus
 */
class DocumentAPIController extends APIController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function definePath() {
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

		$token      = Input::Post('token');
		$section_id = Input::Post('section-id');
		$user_id    = $this->getUserId();
		// we default visitor's user_id = 1
		$user_id  = empty($user_id) ? 1 : $user_id;
		$filename = 'document';
		// process form and save the file
		$result = $file_form->processForm($user_id, $token, $filename, FileTypeSetting::SYLLABUS);
		$data = array(
			'file_id'    => $result['file_id'],
			'mime'       => str_replace('application/', '', $result['mime']),
			'document'   => $result['encoded'],
			'section_id' => $section_id,
		);
		$this->redirect('/doc-edit&' . http_build_query($data, '', '&'));
		// $this->redirect('/doc-edit&file_id=' . $result['file_id'] . '&doc-type=' . $result['mime'] . '&document=' . $result['encoded']);
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
