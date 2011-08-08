<?php
/**
 * @file
 * Represent book recors
 */

class BookDAO extends DAO implements DAOInterface {

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array(
			'id',
			'isbn',
		);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Extend DAO::create()
	 */
	public function create($params) {
		if (!isset($params['isbn'])) 
		{
			throw new Exception('incomplete book params - ' . print_r($params, true));
			return false;

		} else {

			return $this->db->insert(
				"INSERT INTO `book` (`isbn`) VALUE (:isbn)",
				array('isbn' => $params['isbn'])
			);
			
		}
		
	}

	/**
	 * Extend DAO::read()
	 */
	public function read($params) {
		$sql ="SELECT * FROM `book`";

		if (isset($params['id'])) {
			$sql .= 'WHERE id = :id';
			$data = $this->db->fetch($sql, array('id' => $params['id']));

		} elseif (isset($params['isbn'])) {
			$sql = 'WHERE isbn =:isbn';

			$data = $this->db->fetch($sql, array('isbn' => $params['isbn']));

		} else {
			throw new Exception('unknown book identifier - ' . print_r($params, true));
			return ;

		}
		
		return $this->updateAttribute($data);

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `book` SET
				isbn = :isbn
			WHERE id = :id
		";

		$this->db->perform($sql, array(
			'isbn' => $this->attr['isbn'],
			'id' => $this->attr['id']
		));

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$sql = '
      DELETE b, bs_linkage FROM `book` b
			LEFT JOIN	`book_section_linkage` bs_linkage
				ON b.id = bs_linkage.book_id
			WHERE b.id = :id';
		$this->db->perform($sql, array('id' => $this->id));

	}


}
