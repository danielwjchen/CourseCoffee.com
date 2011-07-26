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
			'doc/edit'    => 'editDocument',
			'doc/upload'  => 'uploadDocument',
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
	 * Upload a document
	 */
	public function uploadDocument() {
		$file_form = new FileFormModel();
		$token    = Input::Post('token');
		// we default visitor's user_id = 1
		$user_id  = Session::Get('user_id');
		$user_id  = empty($user_id) ? 1 : $user_id;
		$filename = 'document';
		// process form and save the file
		$result = $file_form->processForm($user_id, $token, $filename);
		header('Location: ?q=doc/edit&document=' . $result['encoded']  );
	}

	/**
	 * Handle the doc process requests
	 */
	public function processDocument() {
		$processor = new DocumentProcessorFormModel();
		$document = Input::Post('document');
		$result = $processor->processDocument($document);
		echo $result;
	}

	/**
	 * Provide an interactive task editor
	 */
	public function editDocument() {
		$processor = new DocumentProcessorFormModel();
		$document = Input::Get('document');
		$editor = new DocumentEditorPageView(array(
			'document' => $document,
			'processor_token'    => $processor->initializeFormToken(),
		));
		echo $editor->render();
	}

}
