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
    public static function get($index)
    {
    	$instance = self::getInstance();
    	
    	//search for getterClass
    	$method = 'get'.ucfirst($index);
    	if (method_exists($instance, $method)) {
    	    return $instance->$method();
    	}
    	
    	if (!$instance->offsetExists($index)) {
    		throw new Zend_Exception("No entry is registered for key '$index'");
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
        	return $instance->$method($value);
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
        $self = Candoo_App_Resource::getInstance();
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
    
    public function getLocale()
    {
        return Zend_Registry::get('Zend_Locale');
    }
    
    public function setLocale($val)
    {
        return Zend_Registry::set('Zend_Locale', $val);
    }
    
    public function getTranslator()
    {
    	return Zend_Registry::get('Zend_Translate');
    }
    
    public function setTranslator($val)
    {
    	return Zend_Registry::set('Zend_Translate', $val);
    }
    
}