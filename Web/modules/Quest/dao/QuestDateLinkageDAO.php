<?php
/**
 * @file
 * Represent a linkage between a quest and a date
 */
class QuestDateLinkageDAO extends LinkageDAO {

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'quest_id', 
			'child_id'  => 'date_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'quest_date_linkage';
	}
}
