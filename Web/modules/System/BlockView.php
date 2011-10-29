<?php
/**
 * @file
 * Base class for all child BlockViews
 */

interface BlockViewInterface {
	public function getContent();
}

abstract class BlockView extends HTMLView implements ViewInterface {

	/**
	 * Implement ViewInterface::render()
	 */
	public function render($data = null) {
		$this->data = $data;
		$this->data['content'] = $this->getContent();
		return $this->data;
	}
}
