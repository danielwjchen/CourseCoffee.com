<?php
/**
 * @file
 * Represent book recors
 */

class BookDAO extends DAO implements DAOInterface {

	/**
	 * Implement DAO::defineAttribute().
	 */
	protected function defineAttribute() {
		return array('id', 'isbn');
	}

	/**
	 * Implement DAOInterface::create()
	 */
	public function create($params) {
		if (!isset($params['isbn']))  {
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
	 * Implement DAOInterface::read()
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
	 * Implement DAOInterface::update()
	 */
	public function update() {
		if (!empty($this->attr['title'])) {
			$sql = "
				UPDATE `book` SET
					title = :title
				WHERE id = :id
			";

			$this->db->perform($sql, array(
				'title' => $this->attr['title'],
				'id' => $this->attr['id']
			));
		}

		if (!empty($this->attr['isbn'])) {
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

	}

	/**
	 * Implement DAOInterface::destroy()
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
