<?php
/**
 * @file
 * Represent date types
 */
class DateTypeDAO extends TypeDAO {

	/**
	 * Implement TypeDAO::defineType()
	 */
	protected function defineType() {
		return 'date_type';
	}

}
