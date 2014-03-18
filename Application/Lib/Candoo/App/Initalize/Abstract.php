<?php
abstract class Candoo_App_Initalize_Abstract
{
    /**
     * List of executed module init
     * 
     * @var array()
     */
    protected static $_executed = array();
    
    /**
     * Saf e init shode va dar entezaar e ejraaa
     * 
     * @var array()
     */
    protected static $_quee     = array();
    
    
    protected $_classInits;
    
    final public function init($module = null)
    {
        if ($module) {
            if ( ! (Candoo_Extension_Module::isCoreModule($module) || Candoo_Extension_Module::isInstalledModule($module)) ) {
                throw new Zend_Exception($module.' module not present.');
            }
            
            $initClass = $module.'_Init';
            if (class_exists($initClass,true)) {
            	$initClass = new $initClass();
            
            	if (! $initClass instanceof Candoo_App_Initalize_Abstract) {
            		throw new Zend_Exception('Module initalize class must instance of Candoo_App_Initalize_Abstract');
            	}
            
            	// initalize module
            	$initClass ->init();
            }

            return;
        }
                
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
    
    /**
     * Return module configuration
     */
    final public function getConfig()
    {
        // get module name from name of class
        $classname = $this->getClassName();
        $module    = current(explode('_',$classname));
        
        return Candoo_Extension_Module::getConfig($module);
    }
    
    /**
     * Return name of class
     */
    public function getClassName()
    {
        $class = new ReflectionObject($this);
        return $class ->getName();
    }
    
    /**
     * Test mikonad ke aayaa in class (module) ghablan initalize shode ?!
     */
    final public function isExecuted()
    {
        return in_array($this->getClassName(), self::$_executed);
    }
}