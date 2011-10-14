<?php
/**
 * @file
 * The base class for all AttributeDAO classes
 */
abstract class AttributeDAO extends DAO implements DAOInterface{

	/**
	 * The table of the DAO which this attribute complements.
	 */
	protected $dao_table;

	/**
	 * Define the DAO class which this AttributeDAO complements.
	 *
	 * @return string
	 */
	abstract protected function defineDAOTable() ;

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		$this->dao_table = $this->defineDAO();
		return array(
			'id',
			'value',
			$this->dao_table . '_id',
			'type',
			'type_id',
		);
	}

	/**
	 * Implement DAOInterface::create()
	 */
	public function create($params) {
		if (!isset($params['value']) || 
				!isset($params[$this->dao_table . '_id']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete attribute params - '. print_r($params, true));
			return ;

		} 

		parent::create("
			INSERT INTO `{$this->dao_table}_attribute`
				(`value`, `{$this->dao_table}_id`, `type_id`)
			VALUES (
				:value,
				:{$this->dao_table}_id,
				(SELECT `id` FROM `{$this->dao_table}_attribute_type` WHERE name = :type)
			)",
				array(
					'value' => $params['value'],
					$this->dao_table . '_id' => $params[$this->dao_table . '_id'],
					'type' => $params['type'],
			)
		);
		
	}

	/**
	 * Implement DAOInterface::read()
	 */
	public function read($params) {
		$sql ="
			SELECT 
				a.*, 
				at.id AS type_id,
				at.name AS type
			FROM `{$this->dao_table}_attribute` a
			INNER JOIN `{$this->dao_table}_attribute_type` at
				ON a.type_id = at.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE a.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params[$this->dao_table . '_id']) && isset($params['type_id'])) {
			$sql .= 'WHERE a.' . $this->dao_table . '_id = :' . $this->dao_table .'_id AND at.id = :type_id';
			$data = parent::read($sql, array(
				$this->dao_table . '_id' => $params[$this->dao_table . '_id'],
				'type_id' => $params['type_id']
			));

		} elseif (isset($params[$this->dao_table . '_id']) && isset($params['type'])) {
			$sql .= 'WHERE a.' . $this->dao_table .'_id = :' . $this->dao_table .'_id AND at.name = :type';
			$data = parent::read($sql, array(
				$this->dao_table . '_id' => $params[$this->dao_table . '_id'],
				'type' => $params['type']
			));

		} elseif (isset($params[$this->dao_table . '_id'])) {
			$sql .= 'WHERE a.' . $this->dao_table . '_id = :' . $this->dao_table .'_id';
			$data = parent::read(
				$sql, 
				array($this->dao_table . '_id' => $params[$this->dao_table . '_id'])
			);

		} elseif (isset($params['type_id']) && isset($params['value'])) {
			$sql .= "WHERE at.id = :type_id AND a.value = :value";
			$data = parent::read($sql, array(
				'type_id' => $params['type_id'],
				'value' => $params['value'],
			));

		} elseif (isset($params['type']) && isset($params['value'])) {
			$sql .= "WHERE at.name = :type AND a.value = :value";
			$data = parent::read($sql, array(
				'type' => $params['type'],
				'value' => $params['value'],
			));

		} else {
			throw new Exception("unknown {$this->attribute} identifier - " . print_r($params, true));
			return ;

		}

		return $this->updateAttribute($data);

	}

	/**
	 * Implement DAOInterface::update()
	 */
	public function update() {
		$sql = "
			UPDATE `{$this->dao_table}_attribute` SET
				value = :value,
				{$this->dao_table}_id = :{$this->dao_table}_id,
				type_id = (SELECT id FROM {$this->dao_table}_attribute_type WHERE name = :type)
			WHERE id = :id
		";

		parent::update($sql, array(
			'value' => $this->attr['value'],
			'type' => $this->attr['type'],
			$this->dao_table . '_id' => $this->attr[$this->dao_table . '_id'],
			'id' => $this->attr['id']
		));

		$this->read($this->attr);

	}

	/**
	 * Implement DAOInterface::destroy()
	 */
	public function destroy() {
		$sql = "DELETE FROM `{$this->dao_table}_attribute` WHERE id = :id";
		parent::destroy($sql, array('id' => $this->id));

	}
}
