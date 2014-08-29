<?php
class Candoo_App_Config
{
    protected static $_setterclass = null;
       
    public static function setConfigs(array $configs)
    {        
        // setter confis
        $class = self::getSetterClass();
        $class->start($configs);
    }
    
    public static function setSetterClass($class)
    {
    	if (is_string($class)) {
    		if(class_exists($class)) {
    			$class = new $class();
    		}
    	}
    	
    	if (is_object($class)) {
    		if (!$class instanceof Candoo_App_Config_Setter ) {
    			throw new Zend_Exception('Setter class must be instance of Candoo_App_Config_Setter');
    		}
    	}
    
    	self::$_setterclass = $class;
    }
    
    /**
     * Claass i ke option haaie barnaame raa migirad va method haaie marboote
     * raa ejraa mikonad
     * 
     */
    protected static function getSetterClass($configs = null)
    {
    	if (self::$_setterclass == null) {
    		self::setSetterClass(new Candoo_App_Config_Setter());
    	}
    
    	return self::$_setterclass;
    }
}