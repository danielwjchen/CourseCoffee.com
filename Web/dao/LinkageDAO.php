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
	 * Update the object Attribute
	 *
	 * @param array $data
	 *  an assciative array that contains data
	 *
	 * @return bool
	 */
	protected function updateAttribute($data) {
		if (!empty($data)) {
			foreach ($this->attr as $key => $value) {
				$this->attr[$key] = isset($data[$key]) ? $data[$key] : null;

			}

			return true;

		} else {
			return false;

		}

	}


	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		$sql = "
			INSERT INTO `{$this->linkage}` (
				`{$this->column[0]}`, 
				`{$this->column[1]}`
			) VALUES (
				:{$this->column[0]}, 
				:{$this->column[1]}
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
		$sql = "SELECT * FROM `{$this->linkage}` WHERE ";

		if (isset($params['id'])) {
			$params = array('id' => $params['id']);
			$sql .= "id = :id";

		} elseif (isset($params[$this->column[0]]) && 
			isset($params[$this->column[1]])) 
		{
			$params = array(
				$this->column[0] => $params[$this->column[0]],
				$this->column[1] => $params[$this->column[1]],
			);

			$sql .= "{$this->column[0]} = :{$this->column[0]} 
				AND {$this->column[1]} = :{$this->column[1]}";

		// return list of record based on parent
		} elseif (isset($params[$this->column[0]])) {
			$params = array($this->column[0] => $params[$this->column[0]]);
			$sql .= "{$this->column[0]} = :{$this->column[0]}";
			$this->list = $this->db->fetch($sql, $params);
			return count($this->list);

		// return list of record based on child
		} elseif (isset($params[$this->column[1]])) {
			$params = array($this->column[1] => $params[$this->column[1]]);
			$sql .= "{$this->column[1]} = :{$this->column[1]}";
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
				`{$this->column[0]}` = :{$this->column[0]}, 
				`{$this->column[1]}` = :{$this->column[1]}
			WHERE `id` = :id
		";
		parent::update($sql);
		$this->read($this->attr);

	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = "DELETE FROM `{$this->linkage}` WHERE `id` = :id";
		parent::destroy($sql, array('id' => $this->id));

	}

}
