<?php
/**
 * @file
 * Handle CourseCoffee /welcome request
 */
class DocumentController extends CourseCoffeeController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'doc-edit'    => 'getDocumentEditorPage',
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

	/**
	 * Provide an interactive task editor
	 */
	public function getDocumentEditorPage() {
		$this->redirectUnsupportedDomain();

		$processor = new DocumentProcessorFormModel($this->sub_domain);
		$process_state = $this->isUserLoggedIn() ? 'redirect' : 'sign-up';
		$document     = Input::Get('document');
		$file_id      = Input::Get('file_id');
		$mime         = Input::Get('mime');
		$section_id   = Input::Get('section_id');
		$section_code = null;
		if (!empty($section_id)) {
			$college_class = new CurriculumClassModel($this->sub_domain);
			$result = $college_class->getClassById($section_id);
			if (isset($result['content'])) {
				$section_code = $result['content']['section_code'];
			}
		}
		$this->output = new DocumentEditorPageView(array(
			'process_state'   => $process_state,
			'document'        => $document,
			'file_id'         => $file_id,
			'section_id'      => $section_id,
			'section_code'    => $section_code,
			'mime'            => $mime,
			'processor_token' => $processor->initializeFormToken(),
		));
	}
}
