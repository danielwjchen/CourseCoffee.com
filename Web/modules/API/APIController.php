<?php
/**
 * @file
 * Base class for handling API rlated controller logics
 */
class APIController extends Controller implements ControllerInterface {

	function __construct($config) {
		parent::__construct($config);
		$this->user_session = new UserSessionModel($this->sub_domain);
	}

	/**
	 * Get institution id
	 */
	protected function getInstitutionId() {
		$domain = $this->getRequestedSubDomain();
		$institution = new InstitutionModel($this->sub_domain);
		$record = $college->getInstitutionByDomain($domain);
		return $record['id'];
	}

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
		$this->output = new APIJSONView(array(
			'error' => '404 Page Not Found.',
		));
	}

}
