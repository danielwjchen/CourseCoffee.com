<?php

require_once TEST_CORE_PATH . '/CoreSetup.php';

/**
 * Set up the environment for testing DAO.
 */
class DAOSetup extends CoreSetup {

	static private $stage;

  /**
   * Extend CoreSetup::Init()
   */
  static public function Init($db_config) {
    parent::Init($db_config);
  }

	/**
	 * Generate randomized types.
	 *
	 * @param string $model
	 *  the model which this method generates types for, e.g. location, date.
	 */
	static private function generateType($model) {
		$model = str_replace('_type', '', $model);
		$types = array(
			'location' => array('campus', 'building', 'city'),
			'date' => array('begin', 'end', 'checkpoint'),
			'quest' => array(
				'college_subject', 
				'college_course', 
				'college_section',
				'college_exam',
				'college_reading',
				'college_lab',
				'college_essay',
				'college_quiz',
			),
			'quest_attribute' => array(
				'college_session_num',
				'college_course_num',
				'college_course_abbr',
			),
			'item' => array('book', 'lab_material', 'lab_equipment'),
			'merchant' => array('retail_online', 'retail_local', 'resell'),
			'affiliation' => array(
				'college', 
				'study_group', 
				'college_fraternity', 
				'college_sorority'
			),
			'achievement' => array(
				'highest_karma',
				'most_comment',
				'most_best_reply',
			),
			'statistic' => array(
				'best_reply',
				'comment',
				'traffic',
			),
		);

		if (isset($types[$model])) {
			static $used_key;
			do {
				$key = mt_rand(0, count($types[$model]) - 1);

			} while ($used_key == $key);

			$used_key = $key;
			return $types[$model][$key];

		}else{
			throw new Exception('unknown model type - ' . $model);

		}
	}

	/**
	 * Extend CoreSetup::Prepare()
	 */
  static public function Prepare($stage) {
		$params = false;
		$object = false;

		switch ($stage) {
			// prepare stage to test implementation of TypeDAO.
			case 'quest_type':
			case 'quest_attribute_type':
			case 'date_type':
			case 'location_type':
			case 'affiliation_type':
			case 'achievement_type':
			case 'statistic_type':
			case 'item_type':
				$params['name'] = self::generateType($stage);
				self::$db->perform(
					"INSERT INTO `{$stage}` (name) VALUE (:name)",
					array('name' => $params['name'])
				);

				$record = self::$db->fetch(
					"SELECT * FROM `{$stage}` WHERE `name` = :name",
					array('name' => $params['name'])
				);

				break;

			// prepare stage to test implementation of LinkageDAO
			case 'affiliation_location_linkage':
			case 'merchant_item_linkage':
			case 'quest_date_linkage':
			case 'quest_item_linkage':
				$element = explode('_', $stage);
				$parent = self::Prepare($element[0]);
				$child = self::Prepare($element[1]);
				$parent_id = $element[0] . '_id';
				$child_id = $element[1] . '_id';
				$params = array(
					$parent_id => $parent['record']['id'],
					$child_id => $child['record']['id']
				);

				self::$db->perform(
					"INSERT INTO `{$stage}` ({$parent_id}, {$child_id}) 
						VALUES (:{$parent_id}, :{$child_id})",
					$params);

				$record = self::$db->fetch(
					"SELECT * FROM `{$stage}` 
					WHERE {$parent_id} = :{$parent_id} AND {$child_id} = :{$child_id}", 
					$params);
				break;


			case 'location':
				$params = array(
					'name' => 'East Lansing',
					'longitude' => mt_rand(12, 15),
					'latitude' => mt_rand(12, 15),
					'type' => '',
					'type_id' => '',
				);

				$type = self::Prepare('location_type');
				$params['type'] = $type['record']['name'];
				$params['type_id'] = $type['record']['id'];

				self::$db->perform(
					'INSERT INTO `location` 
						(name, longitude, latitude, type_id)
					VALUES
						(:name, :longitude, :latitude, :type_id)',
					array(
						'name' => $params['name'],
						'longitude' => $params['longitude'],
						'latitude' => $params['latitude'],
						'type_id' => $params['type_id']
					)
				);

				$record  = self::$db->fetch("
					SELECT 
						l.*,
						lt.name AS type,
						lt.id AS type_id
					FROM `location` l
					INNER JOIN `location_type` lt
						ON l.type_id = lt.id
					WHERE	l.name = :name", 
					array('name' => $params['name'])
				);
				break;

			case 'statistic':
				break;
			case 'date':
				$params = array(
					'timestamp' => mt_rand(0, time()),
					'type_id' => '',
					'type' => ''
				);

				$type = self::Prepare('date_type');
				$params['type'] = $type['record']['name'];
				$params['type_id'] = $type['record']['id'];

				self::$db->perform('
					INSERT INTO `date` (timestamp, type_id)
					VALUES (
						:timestamp, 
						(SELECT t.id FROM `date_type` AS t WHERE t.name = :type)
					)',
					array(
						'timestamp' => $params['timestamp'],
						'type' => $params['type']
					)
				);

				$record  = self::$db->fetch("
					SELECT 
						d.*,
						dt.name AS type,
						dt.id AS type_id
					FROM `date` d
					INNER JOIN `date_type` dt
						ON d.type_id = dt.id
					WHERE	d.timestamp = :timestamp", 
					array('timestamp' => $params['timestamp'])
				);
				
				break;

			case 'college':
			case 'affiliation':
				$params = array(
					'name' => 'Department of Science and Enginerring',
					'url' => mt_rand(12, 15),
					'type' => '',
					'type_id' => '',
				);

				if ($stage == 'college') {
					do {
						$type = self::Prepare('affiliation_type');

					} while ($type['params']['name'] != 'college');

				} else {
					$type = self::Prepare('affiliation_type');

				}

				$params['type'] = $type['params']['name'];
				$params['type_id'] = $type['record']['id'];

				self::$db->perform(
					'INSERT INTO `affiliation` (name, url, type_id) 
						VALUES (
							:name, 
							:url,
							(SELECT `id` FROM `affiliation_type` lt WHERE lt.name = :type)
							)',
					array(
						'name' => $params['name'],
						'url' => $params['url'],
						'type' => $params['type'],
					)
				);

				$record = self::$db->fetch("
					SELECT 
						a.*,
						t.name AS type,
						t.id AS type_id
					FROM `affiliation` a
					INNER JOIN `affiliation_type` t
							ON a.type_id = t.id
						WHERE a.name = :name",
					array('name' => $params['name']));
				break;

				$affiliation = self::Prepare('affiliation');
				$location = self::Prepare('location');
				$params = array(
					'affiliation_id' => $affiliation['record']['id'],
					'location_id' => $location['record']['id']
				);

				self::$db->perform(
					"INSERT INTO `affiliation_location_linkage` (affiliation_id, location_id) 
						VALUES (:affiliation_id, :location_id)",
					$params);

				$record = self::$db->fetch(
					"SELECT * FROM `affiliation_location_linkage` 
						WHERE affiliation_id = :affiliation_id AND location_id = :location_id", 
					$params);
				break;

			case 'user':
				$params = array(
					'account' => 's1300045',
					'password' => 'asdfasdfasdfasdf',
				);
				self::$db->perform(
					"INSERT INTO `user` (account, password) VALUES (:account, :password)",
					$params);
				$record = self::$db->fetch(
					"SELECT * FROM `user` WHERE account = :account AND password = :password",
					$params);
				break;

			case 'person':
			case 'college_subject':
				$params = array(
					'user_id' => '',
					'objective' => 'Department of Science and Enginerring',
					'description' => mt_rand(12, 15),
					'type' => '',
					'type_id' => '',
					'date' => array(
						'begin' => mt_rand(9, 10),
						'end' => mt_rand(9, 10),
						'checkpoint' => mt_rand(9, 10),
					),
				);
				break;
			case 'quest':
				$params = array(
					'user_id' => '',
					'objective' => 'Department of Science and Enginerring',
					'description' => mt_rand(0, time()),
					'type' => '',
					'type_id' => '',
				);

				$type = self::Prepare('quest_type');
				$params['type'] = $type['params']['name'];
				$params['type_id'] = $type['record']['id'];

				$user = self::Prepare('user');
				$params['user_id'] = $type['record']['id'];

				self::$db->perform(
					'INSERT INTO `quest` 
						(user_id, objective, description, type_id)
						VALUES (
							:user_id,
							:objective,
							:description,
							(SELECT `id` FROM `quest_type` lt WHERE lt.name = :type)
							)',
					array(
						'user_id' => $params['user_id'],
						'objective' => $params['objective'],
						'description' => $params['description'],
						'type' => $params['type'],
					)
				);

				$record = self::$db->fetch("
					SELECT 
						q.*,
						t.name AS type,
						t.id AS type_id
					FROM `quest` q
					INNER JOIN `quest_type` t
						ON q.type_id = t.id
					INNER JOIN `user` u
						ON q.user_id = u.id
					WHERE q.objective = :objective",
					array('objective' => $params['objective']));
				break;

			case 'quest_attribute':
				$params = array(
					'value' => mt_rand(0, time()),
					'quest_id' => '',
					'type_id' => '',
					'type' => ''
				);

				$quest = self::Prepare('quest');
				$params['quest_id'] = $quest['record']['id'];
				
				$type = self::Prepare('quest_attribute_type');
				$params['type'] = $type['record']['name'];
				$params['type_id'] = $type['record']['id'];

				self::$db->perform('
					INSERT INTO `quest_attribute` (quest_id, value, type_id)
					VALUES (
						:quest_id,
						:value, 
						(SELECT id FROM `quest_attribute_type` WHERE name = :type)
					)',
					array(
						'quest_id' => $params['quest_id'],
						'value' => $params['value'],
						'type' => $params['type']
					)
				);

				$record  = self::$db->fetch("
					SELECT 
						qa.*,
						qat.name AS type,
						qat.id AS type_id
					FROM `quest_attribute` qa
					INNER JOIN `quest_attribute_type` qat
						ON qa.type_id = qat.id
					WHERE	qa.value = :value", 
					array('value' => $params['value'])
				);
				break;

			case 'item':
				$params = array(
					'name' => 'To Kill a Mocking Bird',
					'type' => '',
					'type_id' => '',
				);

				$type = self::Prepare('item_type');
				$params['type'] = $type['params']['name'];
				$params['type_id'] = $type['record']['id'];

				self::$db->perform(
					'INSERT INTO `item` 
						(name, type_id)
						VALUES (
							:name,
							(SELECT `id` FROM `item_type` lt WHERE lt.name = :type)
							)',
					array(
						'name' => $params['name'],
						'type' => $params['type'],
					)
				);

				$record = self::$db->fetch("
					SELECT 
						i.*,
						t.name AS type,
						t.id AS type_id
					FROM `item` i
					INNER JOIN `item_type` t
						ON i.type_id = t.id
					WHERE i.name = :name",
					array('name' => $params['name']));
				break;

			case 'item_attribute':
				break;

			default:
				throw new Exception('unknown stage - ' . $stage);
		}

		return array('params' => $params, 'record' => $record);;
  }

  static public function CleanUp($stage) {
		switch ($stage) {
			// clean up the stage for implementation of TypeDAO
			case 'quest_type':
			case 'quest_attribute_type':
			case 'date_type':
			case 'location_type':
			case 'affiliation_type':
			case 'achievement_type':
			case 'statistic_type':
			case 'item_type':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				break;

			case 'college':
			case 'affiliation':
				self::$db->perform("TRUNCATE TABLE `affiliation`");
				self::CleanUp("affiliation_type");
				break;
			
			case 'date':
			case 'location':
			case 'item':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				self::CleanUp("{$stage}_type");
				break;

			case 'quest_attribute':
				self::$db->perform("TRUNCATE TABLE `quest`");
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				self::CleanUp("{$stage}_type");
				break;
			
			// clean up the stage for implementation of LinkageDAO
			case 'quest_linkage':
			case 'affiliation_location_linkage':
			case 'quest_achievement_linkage':
			case 'quest_affiliation_linkage':
			case 'quest_date_linkage':
			case 'quest_item_linkage':
			case 'quest_location_linkage':
			case 'quest_person_linkage':
			case 'quest_message_linkage':
			case 'quest_statistic_linkage':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				$table = explode('_', $stage);
				self::CleanUp($table[0]);
				self::CleanUp($table[1]);
				break;

			case 'statistic':
			case 'user':
			case 'person':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				break;

			case 'quest':
				self::$db->perform("TRUNCATE TABLE `{$stage}`");
				self::CleanUp('user');
				self::CleanUp("{$stage}_type");
				break;

			default:
				throw new Exception('unknown stage - ' . $stage);
		}
  }
}
