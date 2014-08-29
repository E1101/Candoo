<?php
abstract class Candoo_App_Bootstraper_Abstract
{
    protected $_request;
    
    /**
     * List of executed module init
     *
     * @var array()
     */
    protected static $_executed = array();
    
    protected $_classInits;
    
    public function __construct(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        
        $this->init();
    }
    
    final public function init()
    {                
        // agar in module init nashode bood method haa raa run mikonad `````````````````````````````````````````````|
        if (! $this->isExecuted()) {
            $initMethods = $this->getClassInits();
            // execute initMethods
            foreach ($initMethods as $method) {
            	$this->$method();
            }
            
            // set executed to true
            self::$_executed[] = $this->getClassName();
        }
        
    }
      
    /**
     * Method haaie _init e class raaa bar migardaanad
     * 
     */
    final public function getClassInits()
    {
    	if (null === $this->_classInits) {
    		if (version_compare(PHP_VERSION, '5.2.6') === -1) {
    			$class        = new ReflectionObject($this);
    			$classMethods = $class->getMethods();
    			$methodNames  = array();
    
    			foreach ($classMethods as $method) {
    				$methodNames[] = $method->getName();
    			}
    		} else {
    			$methodNames = get_class_methods($this);
    		}
    
    		$this->_classInits = array();
    		foreach ($methodNames as $method) {
    			if (5 < strlen($method) && '_init' === substr($method, 0, 5)) {
    				$this->_classInits[strtolower(substr($method, 5))] = $method;
    			}
    		}
    	}
    
    	return $this->_classInits;
    }
    
    final protected function getRequest()
    {
    	return $this->_request;
    }
    
    /**
     * Return module configuration
     */
    final public function getConfig()
    {
        // get module name from name of class
        $classname = $this->getClassName();
        $module    = current(explode('_',$classname));
        
        return Candoo_Module::getConfig($module);
    }
    
    /**
     * Return name of class
     */
    public function getClassName()
    {
        return get_class($this);
        
        /* $class = new ReflectionObject($this);
        return $class ->getName(); */
    }
    
    /**
     * Test mikonad ke aayaa in class (module) ghablan initalize shode ?!
     */
    final public function isExecuted()
    {
    	return in_array($this->getClassName(), self::$_executed);
    }
}