<?php
/**
 * @file
 * Base class for all views that generates a text/HTML response
 */
interface PageViewInterface {

	/**
	 * Define blocks on page
	 *
	 * This function defines the association of a block of information and the 
	 * BlockView child class responsible for generating the content. It also 
	 * specifies the params expected.
	 *
	 * @return array
	 *   - block variable name: the variable name that's expected by getContent()
	 *      - callback: the BlockView child class reponsible for generating the 
	 *        content.
	 *      - params: an array of parameters needed to generate the content.
	 */
	public function getBlocks();

	/**
	 * Define page content
	 */
	public function getContent();

}

abstract class PageView extends View implements ViewInterface {

	const CONTENT_TYPE = 'text/html; charset=utf-8';

	/**
	 * HTML header
	 */
	const HTML_HEADER = 'Content-type: text/html';

	private $cache;

	/**
	 * Override the default constructor
	 *
	 * @param array $data
	 *  an associative array that holds blocks to be rendered into a HTML page
	 *  - header
	 *  - body
	 *  - footer
	 */
	function __construct($data) {
		$this->data = $data;
		$this->data['js']   = array();
		$this->data['css']  = array();
		
		$this->setPageTitle('CourseCoffee.com');
		// $this->addJQuery();
		$this->addJS('main.js');
		$this->addCSS('layout.css');
		$this->addCSS('main.css');
		//$this->cache = new FileCache();
		$this->cache = new DBCache();
	}

	/**
	 * Set page title
	 *
	 * @param string $title
	 *  a string to be used as page title
	 */
	public function setPageTitle($title) {
		$this->data['title'] = 'CourseCoffee.com - ' . $title;
	}

	/**
	 * Add JQuery
	 *
	 * This is not used at the moment because Google API is faster
	 */
	protected function addJQuery() {
		$this->addJS('lib/jquery-1.6.2.js');
	}

	/**
	 * Add JQuery UI
	 */
	protected function addJQueryUI() {
		//$this->data['jquery_ui']['js'][] = "/js/lib/jquery-ui/jquery-ui-1.8.14.custom.min.js";
		$this->data['jquery_ui']['css'][] = "/js/lib/jquery-ui/themes/smoothness/jquery-ui-1.8.14.custom.css";
	}

	/**
	 * Add JQuery UI plugin
	 */
	public function addJQueryUIPlugin($name) {
		switch ($name) {
			case 'datetime':
				$this->data['jquery_ui']['js'][]  = "/js/lib/jquery-ui/plugins/datetime/jquery.ui.datetime.src.js";
				$this->data['jquery_ui']['css'][] = "/js/lib/jquery-ui/plugins/datetime/jquery.ui.datetime.css";
				break;

		}
	}

	/**
	 * Set <script> tag
	 *
	 * This is a helper function for PageView::renderJS()
	 *
	 * @param string $src
	 *  source of the javascript file
	 */
	private function setScriptTag(&$src) {
		$src =<<<JS
<script type="text/javascript" src="{$src}" ></script>
JS;
	}

	/**
	 * Get <link> tag
	 *
	 * This is a helper function for PageView::renderCSS()
	 *
	 * @param string $href
	 *  source of the CSS file
	 */
	private function setLinkTag(&$href) {
		$href =<<<CSS
<link rel="stylesheet" type="text/css" href="{$href}" />
CSS;
	}


	/**
	 * Render the CSS files
	 *
	 * This file also creates cache if the compress flag is set true but no cached
	 * value is available.
	 *
	 * It'is using DBCache(), which should be changed to FileCache() or even 
	 * MemCache() in the future.
	 */
	protected function renderCSS() {
		global $config;
		if (!$config->compressCSS) {
			array_walk($this->data['css'], 'PageView::setLinkTag');
			return implode("\n", $this->data['css']);
		}

		$cache_key   = sha1(implode('', $this->data['css']) . $config->build);
		$cache_value = $this->cache->get($cache_key);
		if (!$cache_value) {
			foreach ($this->data['css'] as $css) {
				$cache_value .= file_get_contents(ROOT_PATH . $css);
			}
			/* remove comments */
			$cache_value = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $cache_value);
			/* remove tabs, spaces, newlines, etc. */
			$cache_value = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $cache_value);
			// expire not defined so it gets flushed when cron runs
			$this->cache->set($cache_key, $cache_value);
		}

		$css_tag = '/css/' . $cache_key . '.css';

		$this->setLinkTag($css_tag);
		return $css_tag;
		
	}

	/**
	 * Render the JS files
	 *
	 * This file also creates cache if the compress flag is set true but no cached
	 * value is available.
	 *
	 * It'is using DBCache(), which should be changed to FileCache() or even 
	 * MemCache() in the future.
	 */
	protected function renderJS() {
		global $config;
		if (!$config->compressJS) {
			array_walk($this->data['js'], 'PageView::setScriptTag');
			return implode("\n", $this->data['js']);
		}

		require_once LIB_PATH . '/JSMin.php';

		$cache_key   = sha1(implode('', $this->data['js']) . $config->build);
		$cache_value = $this->cache->get($cache_key);
		if (!$cache_value) {
			foreach ($this->data['js'] as $js) {
				$cache_value .= file_get_contents(ROOT_PATH . $js);
			}
			$cache_value = JSMIN::minify($cache_value);
			$this->cache->set($cache_key, $cache_value);
		}

		$js_tag = '/js/' . $cache_key . '.js';

		$this->setScriptTag($js_tag);
		return $js_tag;
		
	}

	/**
	 * Get Google Analytics
	 */
	protected function getGoogleAnalytics() {
		global $config;
		return <<<HTML
<script type="text/javascript">
  var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '{$config->google['analytics']}']);
	_gaq.push(['_setDomainName', '.coursecoffee.com']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();

</script>
HTML;
	}

	/**
	 * Get Facebook Javascript SDK 
	 *
	 * This loads the library asynchronously and provides FBSDK() as a callback
	 * function to execute facebook api calls.
	 */
	protected function getFacebookSDK() {
		global $config;
		return <<<HTML
<div id="fb-root"></div>
<script>
	window.fb_app_id = '{$config->facebook['id']}';

	var \$FB = function(callback) {

		var FBReady = function () {
			FB.init({
				appId : '{$config->facebook['id']}',
				status : true,
				cookie : true,
				xfbml : true,
				oauth: true
			});
			callback();
		}

		if (window.FB) {
			FBReady();
		} else {
			window.fbAsyncInit = FBReady;
		}

	};
		if (window.FB) {
			FBReady();
		} else {
			window.fbAsyncInit = function() {
			FB.init({
				appId : '{$config->facebook['id']}',
				status : true,
				cookie : true,
				xfbml : true,
				oauth: true
			});
			}
		}

	(function() {
		var e = document.createElement('script'); e.async = true;
		e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
		document.getElementById('fb-root').appendChild(e);
}());
</script>
HTML;
	}

	/**
	 * Render block css, js and html
	 */
	private function renderBlocks() {
		$blocks   = $this->getBlocks();
		foreach ($blocks as $name => $info) {
			$block = array();

			$block_object = new $info['callback'];

			if (isset($info['params'])) {
				$params = array_flip($info['params']);
				foreach ($params as $key => $value) {
					$params[$key] = $this->data[$key];
				}
				$block = $block_object->render($params);
			} else {
				$block = $block_object->render();
			}

			$this->data[$name] = $block['content'];

			if (isset($block['js'])) {
				$this->data['js'] = array_merge($this->data['js'], $block['js']);
			}
			if (isset($block['css'])) {
				$this->data['css'] = array_merge($this->data['css'], $block['css']);
			}

		}
	}

	/**
	 * Implement ViewInteface::render()
	 */
	public function render() {
		$this->setHeader();
		$this->renderBlocks();
		$js       = $this->renderJS();
		$css      = $this->renderCSS();
		// Add Jquery UI stuff because it doesn't play nice with others when cached
		if (isset($this->data['jquery_ui']['js'])) {
			array_walk($this->data['jquery_ui']['js'], 'PageView::setScriptTag');
			$js .= implode("\n", $this->data['jquery_ui']['js']);
		}
		if (isset($this->data['jquery_ui']['css'])) {
			array_walk($this->data['jquery_ui']['css'], 'PageView::setLinkTag');
			$css .= implode("\n", $this->data['jquery_ui']['css']);
		}
		$content  = $this->getContent();
		$title    = $this->data['title'];
		$facebook = $this->getFacebookSDK();
		$google_analytics = $this->getGoogleAnalytics();

		return <<<HTML
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
		<meta http-equiv='data-type' data='text/html;charset=UTF-8' /> 
		<meta http-equiv='Pragma' data='no-cache' /> 
		<script type="text/javascript">
			(function() {
				var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				po.src = 'https://apis.google.com/js/plusone.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			})();
		</script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
		<title>{$title}</title>
		{$js}
		{$css}
	</head>
	<body>
		{$content}
		{$facebook}
		{$google_analytics}
	</body>
</html>
HTML;
	}

}
