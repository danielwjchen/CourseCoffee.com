<?php

class AffiliationLocationLinkageDAO extends DAO{

	/**
	 * Implement DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$this->db = $db;

		if (!empty($params)) {
			parent::__construct($sql, $params);
		}
	}

	/**
	 * Implement DAO::create()
	 */
	public function create($params) {
		if (!isset($params['affiliation_id']) || !isset($params['location_id'])) {
			throw new Exception('unrecognized affiliation-location linkage params');
			return;

		}

		$sql = "
			INSERT INTO `affiliation_location_linkage` 
				(`affiliation_id`, `location_id`)
			VALUES 
				(:affiliation_id, :location_id)
		";

		parent::create($sql, $params);
		$this->data = $this->read($params);
	}

	/**
	 * Implement DAO::read()
	 */
	public function read() {
		$sql = "SELECT * FROM `affiliation_location_linkage` WHERE ";

		if (isset($params['affiliation_id']) && isset($params['location_id'])) {
			$params = array(
				'affiliation_id' => $affiliation_id,
				'location_id' => $location_id,
			);
			$sql .= "
				affiliation_id = :affiliation_id
				AND location_id = :location_id
			";

		} elseif (isset($params['affiliation_id'])) {
			$params = array('affiliation_id' => $params['affiliation_id']);
			$sql .= "affiliation_id = :affiliation_id";

		} elseif (isset($params['location_id'])) {
			$params = array('location_id' => $params['location_id']);
			$sql .= "location_id = :location_id";

		} else {
			throw new Exception('unknown affiliation_location_linkage identifier');
		}

		$this->data = parent::read($sql, $params);
	}

	/**
	 * Implement DAO::update()
	 */
	public function update() {
		$sql = "
			UPDATE `affiliation_location_linkage` SET
				`affiliation_id` = :affiliation_id,
				`location_id` = :location_id
			WHERE `id` = :id
		";

		parent::update($sql);

		$this->data = $this->read();
	}

	/**
	 * Implement DAO::destroy()
	 */
	public function destroy() {
		$sql = "DELETE FROM `affiliation_location_linkage` WHERE `id` = :id";
		parent::destroy($sql, array('id' => $this->id));
	}

}
