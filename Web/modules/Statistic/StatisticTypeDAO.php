<?php
/**
 * @file
 * Represent statistic types
 */
class StatisticTypeDAO extends TypeDAO{

	/**
	 * Implement TypeDAO::defineType()
	 */
	protected function defineType() {
		return 'statistic_type';
	}

}
