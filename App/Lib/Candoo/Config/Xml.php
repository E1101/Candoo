<?php
class Candoo_Config_Xml
{
    /**
     * SimpleXml object instance
     * 
     * @var simpleXml
     */
    protected $_simpleXml;
    
    /**
     * Parameter section 
     * <production>
     * 	<params>
     * 	 <param type="select" name="frontend" ..>
			<value>websplash</value>
		 </param>
     * 	</params>
     * </production>
     * @var string
     */
    protected $_env;
    
    /**
     * Load file error string.
     *
     * Is null if there was no error while file loading
     *
     * @var string
     */
    protected $_loadFileErrorStr = null;
    
    public function __construct($xml, $env = null)
    {
        set_error_handler(array($this, '_loadFileErrorHandler')); // Warnings and errors are suppressed
        if (strstr($xml, '<?xml')) {
        	$simpleXml = simplexml_load_string($xml);
        } else {
        	$simpleXml = simplexml_load_file($xml);
        }
        restore_error_handler();
        
        // Check if there was a error while loading file
        if ($this->_loadFileErrorStr !== null) {
        	require_once 'Zend/Config/Exception.php';
        	throw new Zend_Exception($this->_loadFileErrorStr);
        }
        
        $this->_simpleXml = $simpleXml;
       	$this->_env   = $env;
    }
    
    public function getParam($param, $default = null)
    {
        $query = '';
        if ($this->getEnv()) {
            $query = '//'.$this->getEnv(); 
        }
        $query .= "//params//param[@name='".$param."']//value";
        
        $list = $this->simpleXml()->xpath($query);
        if (!$list) {
        	return $default;
        }
         
        /**
         * Farz bar in ast ke faghat mitavaanad yek value daashte baashim
         */
        $list = (string) $list[0];
        
        return $list;
    }
    
    public function setEnv($section)
    {
    	$this->_env = $section;
    	
    	return $this;
    }
    
    public function getEnv()
    {
        return $this->_env;
    }
    
    /**
     * Return simpleXml object instance
     * 
     */
    protected function simpleXml()
    {
        return $this->_simpleXml;
    }
    
    
    /**
     * Handle any errors from simplexml_load_file or parse_ini_file
     *
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     */
    protected function _loadFileErrorHandler($errno, $errstr, $errfile, $errline)
    {
    	if ($this->_loadFileErrorStr === null) {
    		$this->_loadFileErrorStr = $errstr;
    	} else {
    		$this->_loadFileErrorStr .= (PHP_EOL . $errstr);
    	}
    }
}
