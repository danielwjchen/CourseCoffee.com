<?php
/**
 * @file
 * Represent quest types
 */
class QuestTypeDAO extends TypeDAO{

	/**
	 * Implement TypeDAO::defineType()
	 */
	protected function defineType() {
		return 'quest_type';
	}

}
