<?php
/**
 * @file
 * Represent a linkage among quest and user
 */
class QuestUserLinkageDAO extends LinkageDAO{

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'quest_id', 
			'child_id'  => 'user_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'quest_user_linkage';
	}
}
