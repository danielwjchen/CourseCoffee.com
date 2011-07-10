<?php

require_once __DIR__ . '/bootstrap.php';

class fileTestCase extends UnitTestCase{

	public function testScanDirectory() {
		$result = FILE::ScanDirectory(DBA_PATH, '/DBA\.php$/');

		print_r($result);
	}
}
