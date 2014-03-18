<?php
class Candoo_Config extends Candoo_Dataset_Entity
{
    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Candoo_App_Resource
     */
    protected static $_instance = null;
    
    protected $_setterclass = null;
    
    
    /**
     * Retrieves the default registry instance.
     *
     * @return Candoo_App_Resource
     */
    public static function getInstance()
    {
    	if (self::$_instance === null) {
    		self::$_instance = new self();
    	}
    
    	return self::$_instance;
    }
    
    public function __construct()
    {
                
    }
    
    public function setConfigs(array $configs)
    {
        parent::__construct($configs);
        
        // setter confis
        $this->getSetterClass($configs);
    }
    
    
    public function setSetterClass($class)
    {
    	if (is_string($class)) {
    		if(class_exists($class)) {
    			$class = new $class();
    		}
    	}
    	
    	if (is_object($class)) {
    		if (!$class instanceof Candoo_Config_Setter ) {
    			throw new Zend_Exception('Setter class must be instance of Candoo_Config_Setter');
    		}
    	}
    
    	$this->_setterclass = $class;
    }
    
    /**
     * Claass i ke option haaie barnaame raa migirad va method haaie marboote
     * raa ejraa mikonad
     * TODO : agar class e digari be jaaie default set shode baashad $options
     * 		   haaa be aan pass nemishavad
     */
    protected function getSetterClass($configs = null)
    {
    	if ($this->_setterclass == null) {
    		$this->setSetterClass(new Candoo_Config_Setter($configs));
    	}
    
    	return $this->_setterclass;
    }
    
    
}