<?php

/**
 * List of methods a controller class must have.
 */
interface ControllerInterface{

	/**
	 * Execute method before action is taken
	 *
	 * This method executes before the controller method does. Typically, this 
	 * includes OAuth2 authentications.
	 */
	public function beforeAction();

	/**
	 * Execute method after action is taken
	 */
	public function afterAction();


}
