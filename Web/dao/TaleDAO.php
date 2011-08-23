<?php
/**
 * @file
 * Represent a tale
 */
class TaleDAO extends DAO implements DAOInterface{

	private $version_dao;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'vid',
			'user_id',
			'title',
			'story',
			'created',
			'updated',
		);

		$this->version_dao = new TaleVersionDAO($db);
		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create()
	 */
	public function create($params) {
		if (!isset($params['user_id']) || 
				!isset($params['title']) ||
				!isset($params['story'])) 
		{
			throw new Exception('incomplete tale params - ' . print_r($params, true));
			return false;

		} else {
			$vid = $this->version_dao->create(array('story' => $params['story']));
			return $this->db->insert("
				INSERT INTO `tale`
					(`title`, `story`, `user_id`, `vid`, `created`, `updated`)
				VALUES (
					:title,
					:story,
					:user_id,
					:vid,
					UNIX_TIMESTAMP(),
					UNIX_TIMESTAMP()
				)",
				array(
					'user_id' => $params['user_id'],
					'title'   => $params['title'],
					'vid'     => $vid
				)
			);
			
		}
		
	}

	/**
	 * Extend DAO::read()
	 */
	public function read($params) {
		$sql ="
			SELECT t.*, v.story 
			FROM `tale`
			INNER JOIN tale_version v
				ON t.vid = v.id
		";

		if (isset($params['id']) && !empty($params['id'])) {
			$sql .= 'WHERE t.id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['vid']) && !empty($params['vid'])) {
			$sql .= 'WHERE t.vid = :vid';
			$data = $this->db->fetch($sql, array('vid' => $params['vid']));

		} elseif (isset($params['title'])) {
			$sql .= 'WHERE t.title = :title';
			$data = $this->db->fetch($sql, array('title' => $params['title']));

		} else {
			throw new Exception('unknown tale identifier - ' . print_r($params, true));
			return ;

		}
		
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$vid = $this->version_dao->create(array('story' => $this->attr['story']));
		$sql = "
			UPDATE `tale` SET
				vid = :vid,
				user_id = :user_id,
				title = :title,
				updated = UNIX_TIMESTAMP()
			WHERE id = :id
		";

		$this->db->perform($sql, array(
			'vid'     => $vid,
			'title'   => $this->attr['title'],
			'user_id' => $this->attr['user_id'],
			'id'      => $this->attr['id']
		));

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = '
			DELETE t, v FROM tale 
			INNER JOIN tale_version v
				ON t.vid = v.id
			WHERE id = :id
		';
		$this->db->perform($sql, array('id' => $this->attr['id']));

	}


}
