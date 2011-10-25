<?php
/**
 * @file
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class QuestPageController extends InstitutionPageController implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'getQuestPage' => array(
				'task',
			),
		);
	}

	/**
	 * Get Quest page
	 */
	public function getQuestPage() {
		global $config;
		$this->redirectUnsupportedDomain();

		$this->output = new ItemSearchPageView(array(
			'base_url'   => 'http://' . $config->domain,
			'is_loggedIn' => $this->getUserId(),
		));
	}
}
