<?php
/**
 * @file
 * Manage institution session variable
 *
 * In future, we will support multiple institutions
 */
class InstitutionSessionModel extends Model {

	/**
	 * Set the primary institution in session
	 *
	 * @param array $instittuion
	 *  - id
	 *  - domain
	 *  - name
	 */
	public function setPrimaryInstitution($instituion) {
		$session = array('primary' => $instittuion);
		Session::Set('institution', $session);
	}

	/**
	 * Get the primary institution in session
	 */
	public function getPrimaryInstitution() {
		$session = Session::Get('institution');
		return $session['primary'];
	}
}

