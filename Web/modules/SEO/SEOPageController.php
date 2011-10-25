<?php
/**
 * @file
 * Manage SEO related tasks and visits made by search engine spiders.
 */
class SEOPageController extends PageController {

	/**
	 * Implement Controller::Route()
	 */
	public static function Route() {
		return array(
			'handleRobots' => array(
				'robot.txt',
			),
		);
	}

	/**
	 * Feed robots
	 */
	public function handleRobots() {
	}
}
