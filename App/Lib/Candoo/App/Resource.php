<?php
class Candoo_App_Resource extends ArrayObject
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
    
    /**
     * getter method, basically same as offsetGet().
     *
     * This method can be called from an object of type Zend_Registry, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index - get the value associated with $index
     * @return mixed
     * @throws Zend_Exception if no entry is registerd for $index.
     */
    public static function get($index, $throwException = true)
    {
    	$instance = self::getInstance();
    	
    	if (!$instance->offsetExists($index)) {
    		if ($throwException) {
    			throw new Zend_Exception("No entry is registered for key '$index'");
    		}
    	
    		return false;
    	}
    	
    	//search for getterClass
    	$method = 'get'.ucfirst($index);
    	if (method_exists($instance, $method)) {
    	    return $instance->$method();
    	}
    	
    	return $instance->offsetGet($index);
    }
    
    /**
     * setter method, basically same as offsetSet().
     *
     * This method can be called from an object of type Zend_Registry, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index The location in the ArrayObject in which to store
     *   the value.
     * @param mixed $value The object to store in the ArrayObject.
     * @return void
     */
    public static function set($index, $value)
    {
        $instance = self::getInstance();
        
        //search for setterClass
        $method = 'set'.ucfirst($index);
        if (method_exists($instance, $method)) {
        	// agar yek resource be soorat external dar method e
        	// setter register shavad meghdaar e true bar migardaanad
        	// ke tavasote offsetSet digar zakhire nashavad
        	// dar barkhi method haaye setter ke mikhaahim no`e object raa
        	// test konim kaarbord daarad, masalan agar bekhaahim baraaie 
        	// request instanceof Candoo_request raa faghat register konim
            if ( $instance->$method($value) ) {
        	    //$value = 'external';
        	}
        }

    	$instance->offsetSet($index, $value);
    }
    
    /**
     * Returns TRUE if the $index is a named value in the registry,
     * or FALSE if $index was not found in the registry.
     *
     * @param  string $index
     * @return boolean
     */
    public static function isRegistered($index)
    {
        $self = self::getInstance();
    	return $self->offsetExists($index);
    }
    
    /**
     * @param string $index
     * @returns mixed
     *
     * Workaround for http://bugs.php.net/bug.php?id=40442 (ZF-960).
     */
    public function offsetExists($index)
    {
    	return array_key_exists($index, $this);
    }
    
    /**
     * Yek array az asaaamie resource haaye register shode (dar key) 
     * be hamraahe no`e object e aaanhaa (dar value) bar migardaanad
     * 
     * @return array
     */
    public static function getResourcesName()
    {
        $resources = self::getInstance()->getArrayCopy();
        foreach ($resources as $key=>$obj) {
            $resources[$key] = get_class($obj);
        }
        
        return $resources;
    }
    
    /**
     * Set View object using during application running proccess
     * with adding this object to viewRenderer
     */
    public function setView(Zend_View $view)
    {	 
    	$viewRenderer = self::get('viewRenderer',false);
    	if (! $viewRenderer ) {
    		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
    		self::set('viewRenderer', $viewRenderer);
    	}
    	 
    	$viewRenderer ->setView($view);
    	
    	if (! Zend_Controller_Action_HelperBroker::hasHelper('viewRenderer')) {
    		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    	}
    }
    
    // ``````````````````````````````````````````````````````````````````
    
    public function setLocale($val)
    {
        Zend_Registry::set('Zend_Locale', $val);
        
        return true;
    }
    
    public function getLocale()
    {
    	return Zend_Registry::get('Zend_Locale');
    }
    
    // ``````````````````````````````````````````````````````````````````
    
    public function getTranslator()
    {
    	return Zend_Registry::get('Zend_Translate');
    }
    
    public function setTranslator($val)
    {
    	Zend_Registry::set('Zend_Translate', $val);
    	
    	return true;
    }
    
    // ``````````````````````````````````````````````````````````````````
    // allow to add specific objects for each resource
    public function setRequest(Candoo_Request_Http_Abstract $val)
    {
        
    }
    
    public function setFrontcontroller(Zend_Controller_Front $val)
    {
        
    }
    
}