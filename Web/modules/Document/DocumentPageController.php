<?php
/**
 * @file
 * Manage some default output
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class DocumentPageController extends InstitutionPageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'getDocumentEditorPage' => array(
			 	'doc/edit',
			),
		);
	}

	/**
	 * Provide an interactive task editor
	 */
	public function getDocumentEditorPage() {
		$this->redirectUnsupportedDomain();

		$processor = new DocumentProcessorFormModel($this->sub_domain);
		$process_state = $this->isUserLoggedIn() ? 'redirect' : 'sign-up';
		$college    = new CollegeModel($this->sub_domain);
		$document     = Input::Get('document');
		$file_id      = Input::Get('file_id');
		$mime         = Input::Get('mime');
		$section_id   = Input::Get('section_id');
		$section_code = null;
		if (!empty($section_id)) {
			$college_class = new CollegeClassModel($this->sub_domain);
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
