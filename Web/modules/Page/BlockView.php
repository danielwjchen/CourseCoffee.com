<?php
/**
 * @file
 * Base class for all child BlockViews
 *
 * @author Daniel Chen <daniel@coursecoffee.com>
 */
interface BlockViewInterface {
	public function getContent();
}

abstract class BlockView extends HTMLView implements ViewInterface {

	/**
	 * Override View::__construct()
	 */
	function __construct() {
	}

	/**
	 * Implement ViewInterface::render()
	 */
	public function render($data = null) {
		$this->data = $data;
		$this->data['content'] = $this->getContent();
		return $this->data;
	}
}
