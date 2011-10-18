<?php
/**
 * @file
 * Base class for handling API rlated controller logics
 */
class APIController extends Controller implements ControllerInterface {

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'init' => array(
				'initializeAPI',
			),
		);
	}

	/**
	 * Implement ControllerInterface::action()
	 */
	public function action($callback, array $params = null) {
		call_user_func_array(array($this, $callback), $params);
		echo $this->output->render();
	}

	/**
	 * Implement ControllerInterface::getDefaultAction()
	 *
	 * Default behavior is 404 Page not found.
	 */
	public function getDefaultAction() {
		Logger::Write(self::ERROR_404 . ' - ' . $uri, Logger::SEVERITY_LOW);
		$this->output = new NotFoundPageView();
	}

}
