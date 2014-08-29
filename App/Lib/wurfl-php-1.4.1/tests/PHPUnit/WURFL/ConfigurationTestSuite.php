<?php
/**
 * test case
 */
require_once dirname(__FILE__).'/classautoloader.php';
rApp 'Configuration/ArrayConfigTest.php';
rApp 'Configuration/XmlConfigTest.php';
rApp 'Configuration/InMemoryConfigTest.php';

/**
 * Static test suite.
 */
class WURFL_ConfigurationTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'ConfigurationTestSuite' );		
		$this->addTestSuite ( 'WURFL_Configuration_XmlConfigTest' );
		$this->addTestSuite ( 'WURFL_Configuration_ArrayConfigTest' );
        $this->addTestSuite ( 'WURFL_Configuration_InMemoryConfigTest' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ( );
	}
}

