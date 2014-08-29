<?php

require_once 'PHPUnit/Framework/TestSuite.php';

rApp 'WURFL/WURFLManagerTest.php';
rApp 'WURFL/ReloaderTestSuite.php';
rApp 'WURFL/ConfigurationTestSuite.php';
rApp 'WURFL/HandlersTestSuite.php';
rApp 'WURFL/XmlTestSuite.php';
rApp 'WURFL/Request/UserAgentNormalizerTestSuite.php';
rApp 'WURFL/DeviceRepositoryBuilderTest.php';
rApp 'WURFL/Issues/IssuesTest.php';

/**
 * Static test suite.
 */
class WURFLTestsSuite extends PHPUnit_Framework_TestSuite {
	
	
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'WURFLTestsSuite' );
		
		$this->addTestSuite ( 'WURFL_ConfigurationTestSuite' );
		$this->addTestSuite ( 'WURFL_XmlTestSuite' );
		$this->addTestSuite ( 'WURFL_WURFLManagerTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizerTestSuite' );
		$this->addTestSuite ( 'WURFL_HandlersTestSuite' );
		$this->addTestSuite ( 'WURFL_DeviceRepositoryBuilderTest' );
		$this->addTestSuite ( 'WURFL_Issues_IssuesTest' );
		$this->addTestSuite ( 'WURFL_ReloaderTestSuite' );
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

