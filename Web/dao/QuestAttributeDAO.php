<?php
/**
 * @file
 * Represent a quest_attribute
 */
class QuestAttributeDAO extends AttributeDAO{

	/**
	 * Implements AttributeDAO::defineDAOTable().
	 */
	protected function defineDAOTable() {
		return 'quest';
	}
}
