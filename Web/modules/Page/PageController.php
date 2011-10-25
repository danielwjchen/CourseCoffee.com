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

	/**
	 * Implement ControllerInterface::Route()
	 */
	public static function Route() {
		return array(
			'get401Page' => array(
				'401.html',
				'unauthorized',
			),
			'get403Page' => array(
				'403.html',
				'forbidden',
			),
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

	/**
	 * Handle page reedirection
	 *
	 * @param string $url
	 *  url of the page to redirect to
	 */
	protected function redirect($url) {
		header('Location: ' . $url);
		exit();
	}

	/**
	 * Hanlid page redirection using javascript
	 */
	protected function clientRedirect($url) {
		echo <<<HTML
<script type="text/javascript">
	top.location.href='{$url}';
</script>
HTML;
		exit();
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
	 * This is not the best implementation as it introduce dependency on 
	 * Institution module, which is a sub module.
	 *
	 */
	public function getDefaultAction() {
		$this->redirect(InstitutionPageController::PAGE_WELCOME);
	}

	/**
	 * Get the 401 output
	 */
	public function get401Page() {
		$this->output = new UnauthorizedPageView();
	}
	
	/**
	 * Get the 403 output
	 */
	public function get403Page() {
		$this->output = new ForbiddenPageView();
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
