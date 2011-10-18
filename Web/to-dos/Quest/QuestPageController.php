<?php
/**
 * @file
 */
class QuestPageController extends PageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::path()
	 */
	public static function definePath() {
		return array(
			'task' => 'getQuestPage',
		);
	}

	/**
	 * Get Quest page
	 */
	public function getQuestPage() {
		global $config;
		$this->redirectUnsupportedDomain();

		$this->output = new BookSearchPageView(array(
			'base_url'   => 'http://' . $config->domain,
			'is_loggedIn' => $this->getUserId(),
		));
	}
}
