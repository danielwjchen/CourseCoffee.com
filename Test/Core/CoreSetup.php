<?php

/**
 * Set up the environment for testing
 *
 */

class CoreSetup {

  static protected $db;

  /**
   * Initialize Setup
   *
   * @param array $db_config
   *  an associative array that defines the database connection
   */
  static public function Init($db_config) {
    self::$db = new DB($db_config);
  }
}
