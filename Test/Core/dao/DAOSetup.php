<?php

require_once TEST_CORE_PATH . '/CoreSetup.php';

/**
 * Set up the environment for testing DAO.
 */
class DAOSetup extends CoreSetup{

	/**
	 * Generate randomized types.
	 *
	 * @param string $model
	 *  the model which this method generates types for, e.g. location, date.
	 * @oaram string $mode
	 *  a flag to dictate how the type(s) are generated
	 *
	 * @return string $type
	 *  name of the type
	 */
	static protected function generateType($model, $mode = 'random') {
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
			'item_attribute' => array(
				'isbn',
				'weight',
				'dimension',
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

			switch ($mode) {
				case 'random':
					$key = mt_rand(0, count($types[$model]) - 1);
					return $types[$model][$key];
				default:
					throw new Exception('unknown mode - ' . $mode);
			}

		}else{
			throw new Exception('unknown model type - ' . $model);

		}
	}

	/**
	 * Extend CoreSetup::Prepare()
	 */
  static public function Prepare($stage, $params = null) {
		$params = false;
		$object = false;

		// temporarily hack before refactoring is finished
		if (strpos($stage, 'DAO')) {
			require_once __DIR__ . '/setups/' . $stage . 'Setup.php';
			return call_user_func($stage. 'Setup::Prepare', $params);
		}

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
			case 'item_attribute_type':
				$params['name'] = empty($params) ? self::generateType($stage) : $params;
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

			case 'quest_linkage':
				$parent = self::Prepare('quest');
				$child = self::Prepare('quest');
				$params = array(
					'parent_id' => $parent['record']['id'],
					'child_id' => $child['record']['id']
				);

				self::$db->perform("
					INSERT INTO `quest_linkage` (parent_id, child_id) 
						VALUES (:parent_id, :child_id)",
					$params);

				$record = self::$db->fetch(
					"SELECT * FROM `quest_linkage` 
					WHERE parent_id = :parent_id AND child_id = :child_id", 
					$params);
				break;

			case 'location':
				$params = array(
					'name' => self::generateString(128),
					'longitude' => mt_rand(0, 15),
					'latitude' => mt_rand(0, 15),
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

			case 'begin_date':
			case 'end_date':
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
					'name' => self::generateString(128),
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
				break;

			case 'college_subject':
				$params = array(
					'user_id' => 1,
					'subject' => self::generateString(128),
					'description' => self::generateString(512),
					'type' => 'college_subject',
					'college' => '',
					'college_id' => '',
					'abbr' => self::generateString(5),
				);

				$college = self::Prepare('college');
				$params['college'] = $college['record']['name'];
				$params['college_id'] = $college['record']['id'];
				self::$db->perform(
					"INSERT INTO `quest_type` (name) VALUE (:name)",
					array('name' => 'college_subject')
				);

				$type = self::$db->fetch(
					"SELECT * FROM `quest_type` WHERE `name` = :name",
					array('name' => 'college_subject')
				);

				self::$db->perform(
					"INSERT INTO `quest_attribute_type` (name) VALUE (:name)",
					array('name' => 'college_subject_abbreviation')
				);

				// create the quest
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
						'objective' => $params['subject'],
						'description' => $params['description'],
						'type' => $params['type'],
					)
				);

				$record = self::$db->fetch("
					SELECT 
						q.id,
						q.objective AS subject,
						q.description,
						t.name AS type,
						t.id AS type_id
					FROM `quest` q
					INNER JOIN `quest_type` t
						ON q.type_id = t.id
					WHERE q.objective = :objective",
					array('objective' => $params['subject'])
				);

				self::$db->perform('
					INSERT INTO `quest_attribute` (quest_id, value, type_id)
					VALUES (
						:quest_id,
						:value, 
						(SELECT id FROM `quest_attribute_type` WHERE name = :type)
					)',
					array(
						'quest_id' => $record['id'],
						'value' => $params['abbr'],
						'type' => 'college_subject_abbreviation',
					)
				);

				$abbr = self::$db->fetch("
					SELECT 
						qa.*,
						qat.name AS type,
						qat.id AS type_id
					FROM `quest_attribute` qa
					INNER JOIN `quest_attribute_type` qat
						ON qa.type_id = qat.id
					WHERE	qa.value = :value", 
					array('value' => $params['abbr'])
				);

				$record['abbr'] = $abbr['value'];
				$record['college'] = $college['record']['name'];
				$record['college_id'] = $college['record']['id'];
				break;

			case 'college_semester':
				$date_begin = self::Prepare('begin_date');
				$date_end = self::Prepare('end_date');
				$college = self::Prepare('college');
				$params = array(
					'college' => $college['record']['name'],
					'college_id' => $college['record']['id'],
				);
				break;

			case 'college_course':
				$college_subject = self::Prepare('college_subject');

				$params = array(
					'user_id' => 1,
					'college' => $college_subject['record']['college'],
					'college_id' => $college_subject['record']['id'],
					'subject' => $college_subject['record']['subject'],
					'subject_id' => $college_subject['record']['id'],
					'title' => self::generateString(128),
					'num' => self::generateString(4),
					'description' => self::generateString(512),
				);

				// create the quest
				self::$db->perform(
					"INSERT INTO `quest_type` (name) VALUE (:name)",
					array('name' => 'college_course')
				);

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
						'objective' => $params['title'],
						'description' => $params['description'],
						'type' => 'college_course',
					)
				);

				$record = self::$db->fetch("
					SELECT 
						q.id,
						q.objective AS title,
						q.description,
						t.name AS type,
						t.id AS type_id
					FROM `quest` q
					INNER JOIN `quest_type` t
						ON q.type_id = t.id
					WHERE q.objective = :objective",
					array('objective' => $params['title'])
				);

				// create the quest attribute
				self::$db->perform(
					"INSERT INTO `quest_attribute_type` (name) VALUE (:name)",
					array('name' => 'college_course_number')
				);

				self::$db->perform('
					INSERT INTO `quest_attribute` (quest_id, value, type_id)
					VALUES (
						:quest_id,
						:value, 
						(SELECT id FROM `quest_attribute_type` WHERE name = :type)
					)',
					array(
						'quest_id' => $record['id'],
						'value' => $params['num'],
						'type' => 'college_course_number',
					)
				);

				$num = self::$db->fetch("
					SELECT qa.* FROM `quest_attribute` qa
					INNER JOIN `quest_attribute_type` qat
						ON qa.type_id = qat.id
					WHERE	qa.value = :value", 
					array('value' => $params['num'])
				);

				$record['num'] = $num['value'];
				$record['college'] = $college_subject['record']['college'];
				$record['college_id'] = $college_subject['record']['college_id'];
				$record['subject'] = $college_subject['record']['subject'];
				$record['subject_id'] = $college_subject['record']['id'];
				break;

			case 'quest':
				$params = array(
					'user_id' => '',
					'objective' => self::generateString(128),
					'description' => self::generateString(256),
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

			case 'item_attribute':
			case 'quest_attribute':
				$model = str_replace('_attribute', '', $stage);
				$model_id = $model . '_id';

				$params = array(
					'value' => self::generateString(128),
					$model_id => '',
					'type_id' => '',
					'type' => ''
				);


				$model_record = self::Prepare($model);
				$params[$model_id] = $model_record['record']['id'];
				
				$type = self::Prepare($stage . '_type');
				$params['type'] = $type['record']['name'];
				$params['type_id'] = $type['record']['id'];

				self::$db->perform('
					INSERT INTO `' . $stage . '` (' . $model_id . ', value, type_id)
					VALUES (
						:' . $model_id . ',
						:value, 
						(SELECT id FROM `' . $stage . '_type` WHERE name = :type)
					)',
					array(
						$model_id => $params[$model_id],
						'value' => $params['value'],
						'type' => $params['type']
					)
				);

				$record  = self::$db->fetch("
					SELECT 
						qa.*,
						qat.name AS type,
						qat.id AS type_id
					FROM `{$stage}` qa
					INNER JOIN `{$stage}_type` qat
						ON qa.type_id = qat.id
					WHERE	qa.value = :value", 
					array('value' => $params['value'])
				);
				break;

			case 'item':
				$params = array(
					'name' => self::generateString(128),
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
		if (strpos($stage, 'DAO')) {
			require_once __DIR__ . '/setups/' . $stage . 'Setup.php';
			return call_user_func($stage. 'Setup::CleanUp');
		}

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
			case 'item_attribute_type':
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

			case 'item_attribute':
			case 'quest_attribute':
				$model = str_replace('_attribute', '', $stage);
				self::$db->perform("TRUNCATE TABLE `{$model}`");
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

			case 'college_course':
			case 'college_subject':
				self::CleanUp('quest');
				self::CleanUp('affiliation');
				self::CleanUp('quest_affiliation_linkage');
				self::CleanUp('quest_attribute');
				break;

			default:
				throw new Exception('unknown stage - ' . $stage);
		}
  }
}
