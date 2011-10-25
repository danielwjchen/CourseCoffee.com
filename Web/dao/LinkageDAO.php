<?php

/**
 * Represent a linkage among quests and items
 */
abstract class LinkageDAO extends DAO implements DAOInterface{

	/**
	 * Name of the linkage table
	 */
	protected $linkage;

	/**
	 * An array to define the name of the parent and child id column
	 */
	protected $column;

	/**
	 * Define the columns
	 *
	 * @return array
	 */
	abstract protected function defineColumn() ;

	/**
	 * Define the linkage table
	 *
	 * @return string
	 */
	abstract protected function defineLinkageTable() ;

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array(
			'id',
			$this->column['parent_id'],
			$this->column['child_id'],
		);
	}

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db) {
		$this->column  = $this->defineColumn();
		$this->linkage = $this->defineLinkageTable();
		parent::__construct($db);
	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		$sql = "
			INSERT INTO `{$this->linkage}` (
				`{$this->column['parent_id']}`, 
				`{$this->column['child_id']}`
			) VALUES (
				:{$this->column['parent_id']}, 
				:{$this->column['child_id']}
			)
		";

		return $this->db->insert($sql, $params);
	}

	/**
	 * Implement DAO::read()
	 *
	 * If only one column is returned, it would read the records as a list and 
	 * return the record count.
	 *
	 */
	public function read($params) {
		error_log(print_r($params, true));
		$sql = "SELECT * FROM `{$this->linkage}` WHERE ";

		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "id = :id";

		} elseif (isset($params[$this->column['parent_id']]) && 
			isset($params[$this->column['child_id']])) 
		{
			$params = array(
				$this->column['parent_id'] => $params[$this->column['parent_id']],
				$this->column['child_id'] => $params[$this->column['child_id']],
			);

			$sql .= "{$this->column['parent_id']} = :{$this->column['parent_id']} 
				AND {$this->column['child_id']} = :{$this->column['child_id']}";

		// return list of record based on parent
		} elseif (isset($params[$this->column['parent_id']])) {
			$params = array($this->column['parent_id'] => $params[$this->column['parent_id']]);
			$sql .= "{$this->column['parent_id']} = :{$this->column['parent_id']}";
			$this->list = $this->db->fetch($sql, $params);
			return count($this->list);

		// return list of record based on child
		} elseif (isset($params[$this->column['child_id']])) {
			$params = array($this->column['child_id'] => $params[$this->column['child_id']]);
			$sql .= "{$this->column['child_id']} = :{$this->column['child_id']}";
			$this->list = $this->db->fetch($sql, $params);
			return count($this->list);

		} else {
			throw new Exception("unknown {$this->linkage} identifier - " . print_r($params, true));
			return ;

		}

		$data = $this->db->fetch($sql, $params);

		return $this->updateAttribute($data);

	}

	/**
	 * Implement DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `{$this->linkage}` SET
				`{$this->column['parent_id']}` = :{$this->column['parent_id']}, 
				`{$this->column['child_id']}` = :{$this->column['child_id']}
			WHERE `id` = :id
		";
		$this->db->perform($sql, $this->attr);
	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = "DELETE FROM `{$this->linkage}` WHERE `id` = :id";
		$this->db->perform($sql, array('id' => $this->id));

	}

}
