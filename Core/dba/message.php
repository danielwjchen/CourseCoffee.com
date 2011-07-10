<?php

class MessageDBA implements DBATemplate{
	public function schema() {
		return array(
			'name' => 'message',
			'description' => 'define a message',
			'column' => array(
				'id' => array(
					'type' => 'serial',
					'unsigned' => TRUE,
					'not null' => TRUE,
					'description' => 'the primary key that identifies a message',
				),
				'sender_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'the user id of the sender',
				),
				'type_id' => array(
					'type' => 'int',
					'signed' => TRUE,
					'not null' => TRUE,
					'default' => 0,
					'description' => 'type of the message, e.g. private, discussion.',
				),
				'body' => array(
					'type' => 'text',
					'not null' => TRUE,
					'description' => 'body of the message',
				),
			),
			'primary' => array('id'),
			'unique' => array(
				'type_id' => array('type_id'),
			),
			'index' => array(
				'user_id' => array('user_id')
			),
		);
	}
}
