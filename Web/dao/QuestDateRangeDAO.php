<?php
/**
 * @file
 * Represent a range of time which the quest takes place
 */
class QuestDateRangeDAO extends DAO implements DAOInterface{

	protected $quest;
	protected $end_date;
	protected $begin_date;
	protected $end_linkage;
	protected $begin_linkage;

	/**
	 * Extend DAO::__construct().
	 */
	function __construct($db, $params = NULL) {
		$attr = array('quest_id', 'begin_date', 'end_date');
		$this->quest = Factory::DAO('Quest');
		$this->end_date = Factory::DAO('Date');
		$this->end_date->type = 'end_date';
		$this->begin_date = Factory::DAO('Date');
		$this->begin_date->type = 'begin_date';
		$this->end_linkage = Factory::DAO('QuestDateLinkage');
		$this->begin_linkage = Factory::DAO('QuestDateLinkage');

		parent::__construct($db, $attr, $params);
	}

	/**
	 * Override DAO::create()
	 *
	 * Please note that this does not create the quest for you! It creates the 
	 * dates and the linkage with the quest
	 */
	public function create($params) {

		if (!isset($params['quest_id']) ||
				!isset($params['end_date']) ||
				!isset($params['begin_date'])) 
		{
			throw new Exception('imcomplete date range params - ' . print_r($params, true));
			return ;

		}

		$this->quest->read(array(
			'id' => $params['quest_id']
		));

		// create the end date and the linkage
		$end_date_param = array(
			'type' => 'end_date',
			'timestamp' => $params['end_date']
		);

		$this->end_date->create($end_date_param);
		$this->end_date->read($end_date_param);

		$this->end_linkage->create(array(
			'quest_id' => $this->quest->id,
			'date_id' => $this->end_date->id,
		));

		// create the begin date and the linkage
		$begin_date_param = array(
			'type' => 'begin_date',
			'timestamp' => $params['begin_date']
		);

		$this->begin_date->create($begin_date_param);
		$this->begin_date->read($begin_date_param);

		$this->begin_linkage->create(array(
			'quest_id' => $this->quest->id,
			'date_id' => $this->begin_date->id,
		));

	}

	/**
	 * Override DAO::read().
	 *
	 * This reads the end and begin date together with the quest id.
	 */
	public function read($params) {
		if (!isset($params['quest_id'])) {
			throw new Exception('unkown quest date range identifier - ' . print_r($params, true));
			return ;
			
		}

		$this->quest->read(array(
			'id' => $params['quest_id']
		));

		$sql = "
			SELECT d.id, d.timestamp FROM `date` d
			INNER JOIN `quest_date_linkage` linkage
				ON linkage.date_id = d.id
			INNER JOIN `date_type` dt
				ON d.type_id = dt.id
			WHERE dt.name = :type
				AND linkage.quest_id = :quest_id
		";

		$end_date = $this->db->fetch($sql, array(
			'type' => 'end_date', 
			'quest_id' => $this->quest->id
		));

		$begin_date = $this->db->fetch($sql, array(
			'type' => 'begin_date', 
			'quest_id' => $this->quest->id
		));

		$this->attr['end_date'] = $end_date['timestamp'];
		$this->end_date->id = $end_date['id'];
		$this->attr['begin_date'] = $begin_date['timestamp'];
		$this->begin_date->id = $begin_date['id'];
		$this->attr['quest_id'] = $this->quest->id;

	}

	/**
	 * Override DAO::update()
	 */
	public function update() {
		$this->end_date->timestamp = $this->attr['end_date'];
		$this->end_date->update();
		$this->begin_date->timestamp = $this->attr['begin_date'];
		$this->begin_date->update();
	}

	/**
	 * Override DAO::destroy()
	 */
	public function destroy() {
		$this->end_date->destroy();
		$this->begin_date->destroy();
		$this->end_linkage->destroy();
		$this->begin_linkage->destroy();
	}

}
