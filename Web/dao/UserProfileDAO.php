<?php
/**
 * @file
 * Represents a user's profile
 */
class UserProfileDAO extends DAO implements DAOInterface{

	private $user_dao;
	private $user_setting_dao;
	private $person_dao;
	private $institution_linkage_dao;
	private $institution_dao;
	private $facebook_linkage_dao;
	private $file_dao;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'first_name',
			'last_name',
			'institution',
			'institution_uri',
			'year',
			'term',
		);

		$this->user_dao                = new UserDAO($db);
		$this->user_setting_dao        = new UserSettingDAO($db);
		$this->person_dao              = new PersonDAO($db);
		$this->institution_linkage_dao = new UserInstitutionLinkageDAO($db);
		$this->institution_dao         = new InstitutionDAO($db);
		$this->file_dao                = new FileDAO($db);

		parent::__construct($db, $attr, $params);
	}

	/**
	 * Extend DAO::create()
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
		
		$this->institution_dao->read(array('name' => $params['institution']));
		$this->institution_linkage_dao->create(array(
			'institution_id' => $this->institution_dao->id,
			'user_id'        => $user_id,
		));

		$this->facebook_linkage_dao->create(array(
			'user_id' => $user_id,
			'fb_uid'  => $params['fb_uid'],
		));

		return $user_id;

	}

	/**
	 * Extend DAO::read()
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
				p.first_name,
				p.last_name,
				f.path,
				f.mime,
				i.name AS institution,
				i.uri AS institution_uri,
				iy.period AS year,
				it.name AS term
			FROM `user` u
			INNER JOIN `person` p
				ON u.id = p.user_id
			INNER JOIN `user_setting` us
				ON u.id = us.user_id
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
			LEFT JOIN (`file` f,  `file_type` ft)
				ON u.id = f.user_id
				AND f.type_id = ft.id
				AND ft.name = :file_type
		";

		if (isset($params['user_id'])) {
			$sql .= "WHERE u.id = :user_id";
			$data = $this->db->fetch($sql, array(
				'user_id' => $params['user_id'],
				'file_type' => FileType::PROFILE_IMAGE,
			));
		} elseif (isset($params['email']) && isset($params['password'])) {
			$sql .= "
				WHERE u.account = :email
					AND u.password = :password
			";
			$data = $this->db->fetch($sql, array(
				'file_type' => FileType::PROFILE_IMAGE,
				'email'    => $params['email'],
				'password' => $params['password'],
			));

		} else {
			throw new Exception('unknown user_profile identifier');
		}

		return $this->updateAttribute($data);
	}

	/**
	 * Extend DAO::update()
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
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$this->user_dao->destroy();
		$this->person_dao->destroy();
		$this->facebook_linkage_dao->destroy();
		$this->location_linkage_dao->destroy();
	}

}
