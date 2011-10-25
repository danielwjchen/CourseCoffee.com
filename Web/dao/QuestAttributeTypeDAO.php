<?php
/**
 * @file
 * Represent quest_attribute types
 */
class QuestAttributeTypeDAO extends TypeDAO{

	/**
	 * Implement TypeDAO::defineType()
	 */
	protected function defineType() {
		return 'quest_attribute_type';
	}

}
