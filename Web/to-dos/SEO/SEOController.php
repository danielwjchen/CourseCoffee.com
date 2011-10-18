<?php
/**
 * @file
 * Manage SEO related tasks and visits made by search engine spiders.
 */
class SEOController extends Controller {

	/**
	 * Implement Controller::path()
	 */
	public static function definePath() {
		return array(
			'robot.txt' => 'handleRobots',
		);
	}
}
