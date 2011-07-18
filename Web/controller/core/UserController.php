<?php
/**
 * @file
 * Handle user creation and management
 */

class UserController extends Controller implements ControllerInterface{

	/**
	 * Extend Controller::__construct()
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Implement ControllerInterface::path()
	 */
	public function path() {
		return array(
			'base' => 'user',
			'action' => array(
				'register' => array(
					'callback' => 'register',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'update' => array(
					'callback' => 'update',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'remove' => array(
					'callback' => 'addClient',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'detail' => array(
					'callback' => 'addClient',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'login' => array(
					'callback' => 'login',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
				'logout' => array(
					'callback' => 'logout',
					'params' => array(
						'post' => array(
							'client_id',
							'client_secret',
						),
					),
				),
			),
		);
	}

	/**
	 * Register a new user
	 */
	public function register() {
	}

	/**
	 * Update user's information
	 */
	public function update() {
	}

	/**
	 * Remove a user
	 */
	public function remove() {
	}

	/**
	 * Detail a user's information
	 */
	public function detail() {
	}

	/**
	 * Log in a user
	 */
	public function login() {
	}

	/**
	 * Log out a user
	 */
	public function logout() {
	}

}
