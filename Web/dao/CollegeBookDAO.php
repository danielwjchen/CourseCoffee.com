<?php
/**
 * @file
 * Represent a college text book
 */
class CollegeBookDAO extends DAO implements DAOInterface{

	private $item;
	private $image_src;
	private $isbn;
	private $linkage;

	/**
	 * Extend DAO::__construct()
	 */
	function __construct($db, $params = null) {
		$attr = array(
			'section_id',
			'id',
			'name',
			'image_src',
			'isbn',
		);

		$this->item      = new ItemDAO($db);;
		$this->image_src = new ItemAttributeDAO($db);
		$this->isbn      = new ItemAttributeDAO($db);
		$this->linkage   = new QuestItemLinkageDAO($db);

		parent::__construct($db, $attr, $params);

	}

	/**
	 * Override DAO::create()
	 *
	 * @param array $params
	 *  - section_id: the id of a college section
	 *  - name: name of the book
	 *  - image_src: url to the book's cover image
	 *  - isbn: book's isbn number
	 *
	 * @return string $item_id
	 * - the primary key that identifies the book
	 */
	public function create($params) {
		$item_id = $this->item->create(array(
			'name' => $params['name'],
			'type' => 'book',
		));

		$this->linkage->create(array(
			'quest_id' => $params['section_id'],
			'item_id'  => $item_id,
		));

		$this->image_src->create(array(
			'item_id' => $item_id,
			'value'   => $params['image_src'],
			'type'    => 'book_cover',
		));

		$this->isbn->create(array(
			'item_id' => $item_id,
			'value'   => $params['isbn'],
			'type'    => 'isbn',
		));

		return $item_id;

	}

	/**
	 * Override DAO::read()
	 *
	 * @param array $params
	 *  - section_id: the id of a college section
	 *  - name: name of the book
	 */
	public function read($params) {
		$sql = "
			SELECT * FROM item i
			INNER JOIN quest_item_linkage linkage
				ON linkage.item_id = i.id
			INNER JOIN item_attribute ia
				ON ia.item_id = i.id
			INNER JOIN item_attribute_type iat
				ON iat.id = ia.type_id
			WHERE i.name  = :name
				AND linkage.quest_id = :section_id
		";

		$item = $this->db->fetch($sql, $params);
		$image_src_result = $this->image_src->read(array(
			'item_id' => $item['id'],
			'type'    => 'book_cover',
		));

		$isbn_result = $this->isbn->read(array(
			'item_id' => $item['id'],
			'type'    => 'isbn',
		));

		if (empty($item) || empty($image_src_result) || empty($isbn_result)) {
			return false;

		} else {

			$this->attr['section_id'] = $params['section_id'];
			$this->attr['id']         = $item['id'];
			$this->attr['name']       = $item['name'];
			$this->attr['image_src']  = $this->image_src->value;
			$this->attr['isbn']       = $this->isbn->value;

			return true;
		}

	}

	/**
	 * Extend DAO::update()
	 */
	public function update() {
		$this->item->id   = $this->attr['id'];
		$this->item->name = $this->attr['name'];
		$this->item->update();

		$this->image_src->value = $this->attr['image_src'];
		$this->image_src->update();

		$this->isbn->value = $this->attr['isbn'];
		$this->isbn->update();

	}

	/**
	 * Extend DAO::destroy()
	 */
	public function destroy() {
		$this->linkage->destroy();
		$this->image_src->destroy();
		$this->isbn->destroy();
		$this->item->destroy();

	}

}
