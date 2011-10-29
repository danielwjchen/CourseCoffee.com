<?php
/**
 * @file
 * Represents a user's profile
 */
class UserProfileDAO extends DAO implements DAOInterface{

	private $user_dao;
	private $user_setting_dao;
	private $user_role_dao;
	private $user_status_dao;
	private $person_dao;
	private $tale_dao;
	private $institution_linkage_dao;
	private $institution_dao;
	private $facebook_linkage_dao;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db) {
		parent::__construct($db);

		$this->user_dao                = new UserDAO($db);
		$this->user_role_dao           = new UserRoleDAO($db);
		$this->user_status_dao         = new UserStatusDAO($db);
		$this->user_setting_dao        = new UserSettingDAO($db);
		$this->tale_dao                = new TaleDAO($db);
		$this->person_dao              = new PersonDAO($db);
		$this->institution_linkage_dao = new UserInstitutionLinkageDAO($db);
		$this->institution_dao         = new InstitutionDAO($db);

	}

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'id',
			'account',
			'role',
			'status',
			'first_name',
			'last_name',
			'institution',
			'institution_uri',
			'institution_domain',
			'year',
			'term',
		);
	}

	/**
	 * Implement DAOInterface::create()
	 *
	 * @param array $params
	 *   - first_name
	 *   - last_name
	 *   - email
	 *   - password
	 *   - institution_id
	 *
	 * @return string $user_id
	 *   the primary key of the user record created
	 */
	public function create($params) {
		$user_id = $this->user_dao->create(array(
			'account'  => $params['email'],
			'password' => $params['password'],
		));

		$this->person_dao->create(array(
			'user_id'    => $user_id,
			'first_name' => $params['first_name'],
			'last_name'  => $params['last_name'],
		));

		$this->user_role_dao->read(array('name' => $params['role_name']));
		$this->user_status_dao->read(array('name' => $params['status_name']));
		
		$this->institution_dao->read(array('name' => $params['institution']));
		$this->institution_linkage_dao->create(array(
			'institution_id' => $this->institution_dao->id,
			'user_id'        => $user_id,
		));

		$this->facebook_linkage_dao->create(array(
			'user_id' => $user_id,
			'fb_uid'  => $params['fb_uid'],
		));

		$this->user_setting_dao->create(array(
			'user_id'   => $user_id,
			'role_id'   => $this->user_role_dao->id,
			'status_id' => $this->user_status_dao->id,
		));


		return $user_id;

	}

	/**
	 * Implement DAOInterface::read()
	 *
	 * @param array $params
	 *   - user_id
	 *   - email
	 *   - password
	 *
	 * @return bool
	 */
	public function read($params) {
		$data = array();
		$sql = "
			SELECT 
				u.id,
				u.account,
				p.first_name,
				p.last_name,
				u_r.name AS role,
				u_s.name AS status,
				i.name AS institution,
				i.uri AS institution_uri,
				i.domain AS institution_domain,
				iy.period AS year,
				it.name AS term
			FROM `user` u
			INNER JOIN `person` p
				ON u.id = p.user_id
			INNER JOIN `user_setting` us
				ON u.id = us.user_id
			INNER JOIN (`user_role` u_r, `user_status` u_s)
				ON us.status_id = u_r.id
				AND us.status_id = u_s.id
			LEFT JOIN `user_institution_linkage` ui_linkage
				ON u.id = ui_linkage.user_id
				AND us.institution_id = ui_linkage.institution_id
			LEFT JOIN `institution` i
				ON ui_linkage.institution_id = i.id
			LEFT JOIN `institution_year_linkage` iy_linkage
				ON i.id = iy_linkage.institution_id
			LEFT JOIN `institution_year` iy
				ON iy_linkage.year_id = iy.id
			LEFT JOIN `institution_term` it
				ON iy.id = it.year_id
		";

		if (isset($params['user_id'])) {
			$sql .= "WHERE u.id = :user_id GROUP BY u.id";
			$data = $this->db->fetch($sql, array(
				'user_id' => $params['user_id'],
			));
		} elseif (isset($params['email']) && isset($params['password'])) {
			$sql .= "
				WHERE u.account = :email
					AND u.password = :password
			 GROUP BY u.id
			";
			$data = $this->db->fetch($sql, array(
				'email'    => $params['email'],
				'password' => $params['password'],
			));

		} else {
			throw new Exception('unknown user_profile identifier');
		}

		return $this->updateAttribute($data);
	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$this->user_dao->read(array('id' => $this->attr['id']));
		$this->user_dao->account  = $this->attr['email'];
		$this->user_dao->password = $this->attr['password'];
		$this->user_dao->update();
		$this->person_dao->read(array('user_id' => $this->attr['id']));
		$this->person_dao->first_name = $this->attr['first_name'];
		$this->person_dao->last_name  = $this->attr['last_name'];
		$this->person_dao->update();
		$this->institution_linkage_dao->read(array(
			'user_id'        => $this->attr['id'],
			'institution_id' => $this->att['institution_id'],
		));
		$this->institution_dao->read(array('name' => $this->attr['institution']));
		$this->institution_linkage_dao->institution_id = $this->institution_dao->id;
		$this->institution_linkage_dao->update();

	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$this->user_dao->destroy();
		$this->person_dao->destroy();
		$this->facebook_linkage_dao->destroy();
		$this->institution_dao->update();
	}

}
