<?php
/**
 * @file
 * Handle email related tasks
 *
 * This class implements a singleton design. Be careful with that.
 */
class Emailer {

	const PRIORITY_HIGH   = 'HIGH';
	const PRIORITY_MEDIUM = 'MEDIUM';
	const PRIORITY_LOW    = 'LOW';

	const STATUS_NEW    = 'NEW';
	const STATUS_SENT   = 'SENT';
	const STATUS_FAILED = 'FAILED';

	const QUEUE_NUM = 25;

	/**
	 * Singleton instance
	 */
	public static $instance;

	/**
	 * Database connection
	 */
	private $db;

	/**
	 * Default constructor
	 * 
	 * @param array $config_db
	 *  an associative array that defines the database configuration
	 */
	function __construct($config_db) {
		$this->db = new DB($config_db);

	}

	/**
	 * Default destructor
	 */
	function __destruct() {
		$this->db = null;
	}

	/**
	 * Generate header
	 *
	 * @param array $from
	 *  - name
	 *  - address
	 * @param array $to
	 *  - name
	 *  - address
	 * @return string
	 */
	private function generateHeader(array $from, array $to) {
		return "MIME-Version: 1.0\n" .
			"From : {$from['name']} <{$from['address']}> \n" . 
			"To : {$to['name']} <{$to['address']}> \n" . 
			"Reply-To: {$from['name']} <{$from['address']}>\n" . 
			"Return-Path: {$from['name']} <{$from['address']}>\n" . 
			"Content-type: text/html; charset=utf-8\n" .
			"Organization: CourseCoffee.com\n" . 
			"X-Mailer: CourseCoffee.com\n";
	}

	/**
	 * Serialize array into json string
	 *
	 * @param array
	 * @return string
	 */
	private function toJSON($param) {
		return json_encode($param, true);
	}

	/**
	 * Unserialize json string into array
	 *
	 * @param array
	 * @return string
	 */
	private function fromJSON($param) {
		return json_decode($param, true);
	}

	/**
	 * Send email
	 *
	 * @param array $from
	 *  - name
	 *  - address
	 * @param string $to
	 *  - name
	 *  - address
	 * @param string $subject
	 * @param array $message
	 *  - html
	 *  - text: optional
	 */
	public function sendEmail(array $from, array $to, $subject, array $message) {
		$header = $this->generateHeader($from, $to);
		try {
			if (!mail($to['address'], $subject, $message['html'], $header, '-f ' . $from['address'])) {
				throw new EmailerException('message failed to send');
			}
		} catch (EmailerException $e) {
			Logger::Write($e->getMessage());
		}
	}

	/**
	 * Queue email to be sent later
	 */
	public function queueEmail(array $from, array $to, $subject, $message, $priority = self::PRIORITY_MEDIUM, $status = self::STATUS_NEW) {
		$this->db->insert("
			INSERT INTO `email_queue` (
				`from`, `to`, `subject`, `message`, `status`, `created`, `updated`, `priority`
			) VALUES (
				:from, :to, :subject, :message, :status, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :priority
			)
			",
			array(
				'from'     => $this->toJSON($from),
				'to'       => $this->toJSON($to),
				'subject'  => $subject,
				'message'  => $this->toJSON($message),
				'status'   => $status,
				'priority' => $priority,
		));
	}

	/**
	 * Process items in email queue
	 *
	 * @param int $num
	 *  number of items to be processed at a time
	 */
	public function processEmailQueue($num = self::QUEUE_NUM) {
		$sql = "
			SELECT * FROM `email_queue` 
			WHERE `status` = :status
				AND `priority` = :priority 
			LIMIT 0, :num 
		";
		$processed = 0;
		$priority_array = array(
			self::PRIORITY_HIGH,
			self::PRIORITY_MEDIUM,
			self::PRIORITY_LOW,
		);
		foreach ($priority_array as $key => $priority) {
			$records = $this->db->fetchList($sql,
				array(
					'status'   => self::STATUS_NEW,
					'priority' => $priority,
					'num'      => $num,
			));
			if (!is_array($records)) {
				break;
			}
			foreach ($records as $id => $record) {
				$this->sendEmail(
					$this->fromJSON($record['from']),
					$this->fromJSON($record['to']),
					$record['subject'],
					$this->fromJSON($record['message'])
				);
				$this->db->perform("
					UPDATE  `email_queue` SET `status` = :status WHERE `id` = :id
				",
					array('status' => self::STATUS_SENT, 'id' => $id)
				);
			}

			// finich processing email queue if the required number of items is met
			if (count($records) == $num) {
				break;
			}
		}
	}

	/**
	 * Initialize an Emailer instance
	 *
	 * This checks if an instance of this class already exists
	 */
	private static function Init() {
		global $config;
		if (self::$instance == null) {
			self::$instance = new static($config->db['default']);
		}
	}

	/**
	 * Send email
	 *
	 * @param array $from
	 *  - name
	 *  - address
	 * @param array $to
	 *  - name
	 *  - address
	 * @param string $subject
	 * @param array $message
	 *  - html
	 *  - text: optional
	 *
	 * This is a singleton factory method
	 */
	public static function Send(array $from, array $to, $subject, array $message) {
		self::Init();
		self::$instance->sendEmail($from, $to, $subject, $message);
	}

	/**
	 * Queue email to be sent later
	 *
	 * This is a singleton factory method
	 */
	public function Queue(array $from, array $to, $subject, array $message, $priority = self::PRIORITY_MEDIUM) {
		self::Init();
		self::$instance->queueEmail($from, $to, $subject, $message, $priority);
	}

	/**
	 * Process items in email queue
	 *
	 * This is a singleton factory method
	 */
	public function ProcessQueue($num = self::QUEUE_NUM) {
		self::Init();
		self::$instance->processEmailQueue($num);
	}

}

/**
 * Custumed exception for emailer
 */
class EmailerException extends Exception {
}
