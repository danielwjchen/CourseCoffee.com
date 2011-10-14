<?php
/**
 * @file
 * Base class for controller logics that handle outputs in HTML format
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
class PageController extends Controller implements ControllerInterface {

	protected $output;
	protected $domain;

	const ERROR_404 = 'Fail to map request to callback.';

	function __construct($config) {
		$this->domain = $config->domain;
	}

	/**
	 * Implement ControllerInterface::defineRouting()
	 */
	public static function Route() {
		return array(
			'get404Page' => array(
				'404.html',
				'page-not-found',
			),
			'get500Page' => array(
				'505.html',
				'all-system-down',
			),
		);
	}

	public function beforeAction() {
	}

	/**
	 * Execute method after action is taken
	 */
	public function afterAction() {
		echo $this->output->render();
	}

	/**
	 * Get the 404 output
	 */
	public function get404Page() {
		$this->output = new NotFoundPageView();
	}

	/**
	 * Get the 500 output
	 */
	public function get500Page() {
		$this->output = new InternalErrorPageView();
	}

}
