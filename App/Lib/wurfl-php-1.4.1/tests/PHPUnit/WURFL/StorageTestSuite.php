<?php
/**
 * test case
 */
require_once dirname(__FILE__).'/classautoloader.php';
rApp 'Storage/FileTest.php';
rApp 'Storage/ApcTest.php';
rApp 'Storage/MemcacheTest.php';
rApp 'Storage/MemoryTest.php';

/**
 * Static test suite.
 */
class WURFL_StorageTestSuite extends PHPUnit_Framework_TestSuite {

	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'StorageTestSuite' );
		$this->addTestSuite ( 'WURFL_Storage_FileTest' );
		//$this->addTestSuite ( 'WURFL_Storage_ApcTest' );
        $this->addTestSuite ( 'WURFL_Storage_MemcacheTest' );
        $this->addTestSuite ( 'WURFL_Storage_MemoryTest' );

	}

	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

