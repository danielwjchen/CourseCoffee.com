<?php
/**
 * @file
 */
class TalePageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'getTutorialPage' => array(
				 'how-to-find-syllabus',
			),
		);
	}

	/**
	 * Get the tutorial page
	 */
	public function getTutorialPage() {
		$this->output = new TutorialPageView(array());
	}
}
