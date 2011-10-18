<?php
/**
 * @file
 */
class TalePageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function definePath() {
		return array(
			'how-to-find-syllabus' => 'getTutorialPage',
		);
	}

	/**
	 * Get the tutorial page
	 */
	public function getTutorialPage() {
		$this->output = new TutorialPageView(array());
	}
}
