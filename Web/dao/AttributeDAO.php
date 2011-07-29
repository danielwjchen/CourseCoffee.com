<?php
/**
 * @file
 * The base class for all AttributeDAO classes
 */
abstract class AttributeDAO extends DAO implements DAOInterface{

	/**
	 * Name of the attribute table
	 */
	protected $attribute;

	/**
	 * Name of the model which this attribute defines
	 */
	protected $model;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'value',
			$this->model . '_id',
			'type',
			'type_id',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create()
	 */
	public function create($params) {
		if (!isset($params['value']) || 
				!isset($params[$this->model . '_id']) || 
				!isset($params['type'])) 
		{
			throw new Exception('incomplete attribute params - '. print_r($params, true));
			return ;

		} 

		parent::create("
			INSERT INTO `{$this->model}_attribute`
				(`value`, `{$this->model}_id`, `type_id`)
			VALUES (
				:value,
				:{$this->model}_id,
				(SELECT `id` FROM `{$this->model}_attribute_type` WHERE name = :type)
			)",
				array(
					'value' => $params['value'],
					$this->model . '_id' => $params[$this->model . '_id'],
					'type' => $params['type'],
			)
		);
		
	}

	/**
	 * Extend DAO::read()
	 */
	public function read($params) {
		$sql ="
			SELECT 
				a.*, 
				at.id AS type_id,
				at.name AS type
			FROM `{$this->model}_attribute` a
			INNER JOIN `{$this->model}_attribute_type` at
				ON a.type_id = at.id
		";

		if (isset($params['id'])) {
			$sql .= 'WHERE a.id = :id';
			$data = parent::read($sql, array('id' => $params['id']));

		} elseif (isset($params[$this->model . '_id']) && isset($params['type_id'])) {
			$sql .= 'WHERE a.' . $this->model . '_id = :' . $this->model .'_id AND at.id = :type_id';
			$data = parent::read($sql, array(
				$this->model . '_id' => $params[$this->model . '_id'],
				'type_id' => $params['type_id']
			));

		} elseif (isset($params[$this->model . '_id']) && isset($params['type'])) {
			$sql .= 'WHERE a.' . $this->model .'_id = :' . $this->model .'_id AND at.name = :type';
			$data = parent::read($sql, array(
				$this->model . '_id' => $params[$this->model . '_id'],
				'type' => $params['type']
			));

		} elseif (isset($params[$this->model . '_id'])) {
			$sql .= 'WHERE a.' . $this->model . '_id = :' . $this->model .'_id';
			$data = parent::read(
				$sql, 
				array($this->model . '_id' => $params[$this->model . '_id'])
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
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `{$this->model}_attribute` SET
				value = :value,
				{$this->model}_id = :{$this->model}_id,
				type_id = (SELECT id FROM {$this->model}_attribute_type WHERE name = :type)
			WHERE id = :id
		";

		parent::update($sql, array(
			'value' => $this->attr['value'],
			'type' => $this->attr['type'],
			$this->model . '_id' => $this->attr[$this->model . '_id'],
			'id' => $this->attr['id']
		));

		$this->read($this->attr);

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = "DELETE FROM `{$this->model}_attribute` WHERE id = :id";
		parent::destroy($sql, array('id' => $this->id));

	}
}
