<?php
/**
 * @file
 * Manage user status
 */
class UserRoleModel extends Model {

	/**
	 * @def_group user roles
	 * @{
	 * Default role for regular users
	 */
	const DEFAULT_ROLE = 'member';
	const ADMIN_ROLE   = 'admin';
	/**
	 * @}
	 */

	/**
	 * Implement Model::defineDAO()
	 */
	protected function defineDAO() {
		return array(
			'role_dao' => array(
				'dao' => 'UserRoleDAO',
				'db'  => self::DEFAULT_DB,
			),
		);
	}

}
