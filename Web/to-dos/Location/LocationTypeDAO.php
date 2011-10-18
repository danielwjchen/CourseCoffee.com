<?php
/**
 * @file
 * Represent location types
 */
class LocationTypeDAO extends TypeDAO{

	/**
	 * Implement TypeDAO::defineType()
	 */
	protected function defineType() {
		return 'location_type';
	}

}
