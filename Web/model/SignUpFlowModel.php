<?php
/**
 * @file
 * Oversee the registration progress from begin to finish
 */
class SignUpFlowModel extends Model {

	/**
	 * Implement ModelInterface::path()
	 */
	public static function path() {
		return array(
			'uploadSyllabus',
			'selectSchoolAndClass',
			'registerAccount',
		);
	}
}
