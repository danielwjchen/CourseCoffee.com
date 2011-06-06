<?php

require_once TEST_CORE_PATH . '/CoreSetup.php';

/**
 * Set up the environment for testing DAO.
 */
class DAOSetup extends CoreSetup {

  /**
   * Extend CoreSetup::Init()
   */
  static public function Init($db_config) {
    parent::Init($db_config);
  }

  static public function Prepare($stage) {
  }

  static public function CleanUp($stage) {
  }
}
