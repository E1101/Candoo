<?php
/**
 * test case
 */
require_once dirname(__FILE__).'/../classautoloader.php';
require_once 'UserAgentNormalizerTest.php';
rApp 'UserAgentNormalizer/LocaleRemoverTest.php';
rApp 'UserAgentNormalizer/BlackBerryTest.php';
rApp 'UserAgentNormalizer/ChromeTest.php';
rApp 'UserAgentNormalizer/SafariTest.php';
rApp 'UserAgentNormalizer/FirefoxTest.php';
rApp 'UserAgentNormalizer/MSIETest.php';
rApp 'UserAgentNormalizer/MaemoTest.php';
rApp 'UserAgentNormalizer/AndroidTest.php';

rApp 'UserAgentNormalizer/SerialNumbersTest.php';
rApp 'UserAgentNormalizer/NovarraGoogleTranslatorTest.php';

/**
 * Static test suite.
 */
class WURFL_Request_UserAgentNormalizerTestSuite extends PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		$this->setName ( 'UserAgentNormalizerSuite' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizerTest' );
        $this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_AndroidTest' );
        $this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_LocaleRemoverTest' );		        
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_BlackBerryTest' );		
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_ChromeTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_FirefoxTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_MSIETest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_SafariTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_MaemoTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_SerialNumbersTest' );
		$this->addTestSuite ( 'WURFL_Request_UserAgentNormalizer_NovarraGoogleTranslatorTest' );
	
	}
	
	/**
	 * Creates the suite.
	 */
	public static function suite() {
		return new self ();
	}
}

