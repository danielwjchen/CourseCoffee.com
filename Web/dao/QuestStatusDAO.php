<?php
/**
 * @file
 * Represent quest status
 */
class QuestStatusDAO extends StatusDAO{

	/**
	 * Implement TypeDAO::defineType()
	 */
	protected function defineType() {
		return 'quest_status';
	}

}
