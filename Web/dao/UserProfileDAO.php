<?php
/**
 * @file
 * Represents a user's profile
 */
class UserProfileDAO extends DAO implements DAOInterface{

	private $user_dao;
	private $person_dao;
	private $affiliation_linkage_dao;
	private $college_dao;
	private $facebook_linkage_dao;
	private $file_dao;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'fb_uid',
			'college',
			'college_id',
			'first_name',
			'last_name',
			'email',
			'password',
		);

		$this->user_dao                = new UserDAO($db);
		$this->person_dao              = new PersonDAO($db);
		$this->affiliation_linkage_dao = new UserAffiliationLinkageDAO($db);
		$this->college_dao             = new CollegeDAO($db);
		$this->facebook_linkage_dao    = new UserFacebookLinkageDAO($db);
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
	 *   - college
	 *   - college_id
	 *   - fb_uid
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
		
		$this->college_dao->read(array('name' => $params['college']));
		$this->affiliation_linkage_dao->create(array(
			'affiliation_id' => $this->college_dao->id,
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
	 *   - id
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
				u.account AS email,
				p.first_name,
				p.last_name,
				u.password,
				f.path,
				f.mime,
				ua_linkage.affiliation_id AS college_id,
				a.name AS college,
				uf_linkage.fb_uid
			FROM `user` u
			INNER JOIN `person` p
				ON u.id = p.user_id
			INNER JOIN `user_affiliation_linkage` ua_linkage
				ON u.id = ua_linkage.user_id
			INNER JOIN `affiliation` a
				ON ua_linkage.affiliation_id = a.id
			LEFT JOIN `user_facebook_linkage` uf_linkage
				ON u.id = uf_linkage.user_id
			LEFT JOIN `file` f
				ON u.id = f.user_id
			LEFT JOIN `file_type` ft
				ON f.type_id = ft.id
		";

		if (isset($params['id'])) {
			$sql .= "
				WHERE u.id = :id
					OR ft.name = :file_type
			";
			$data = $this->db->fetch($sql, array(
				'file_type' => FileType::PROFILE_IMAGE,
				'id' => $params['id'],
			));
		} elseif (isset($params['email']) && isset($params['password'])) {
			$sql .= "
				WHREE u.account = :email
					AND u.password = :password
					OR ft.name = :file_type
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
		$this->affiliation_linkage_dao->read(array(
			'user_id'        => $this->attr['id'],
			'affiliation_id' => $this->att['college_id'],
		));
		$this->college_dao->read(array('name' => $this->attr['college']));
		$this->affiliation_linkage_dao->affiliation_id = $this->college_dao->id;
		$this->affiliation_linkage_dao->update();

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
