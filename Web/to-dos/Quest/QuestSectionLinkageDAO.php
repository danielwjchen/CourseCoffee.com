<?php
/**
 * @file
 * Represent a linkage among quest and section
 */
class QuestSectionLinkageDAO extends LinkageDAO {

	/**
	 * Implement LinkageDAO::defineColumn().
	 */
	protected function defineColumn() {
		return array(
			'parent_id' => 'quest_id', 
			'child_id'  => 'section_id',
		);
	}

	/**
	 * Implement LinkageDAO::defineLinkageTable().
	 */
	protected function defineLinkageTable() {
		return 'quest_section_linkage';
	}
}
