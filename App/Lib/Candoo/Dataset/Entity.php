<?php
class Candoo_Dataset_Entity implements Countable, Iterator/* , ArrayAccess */
{
    protected $_properties = array();
    
    protected $_iterator_index = 0;
    
    public function __construct($data)
    {
    	$this->setParams($data);
    }
    
    public function __set($key, $value)
    {
    	$this->_properties[$key] = $value;
    }
    
    public function setParam($key,$value)
    {
    	$this->_properties[$key] = $value;
    }
    
    public function setParams($params)
    {
        if (is_object($params)) {
            if (method_exists($params, 'toArray')) {
                $params = $params->toArray();
            } else {
                $params = (array) $params;
            }
        }
        
    	if ($params != null && is_array($params)) {
    		foreach ($params as $key=>$val) {
    			$this->setParam($key, $val);
    		}
    	}
    	    
    	return $this;
    }
        
    public function getParam($key,$default = null)
    {
    	if (array_key_exists($key, $this->_properties)) {
    	    if (is_array($this->_properties[$key])) {
    	        return new Candoo_Dataset_Entity($this->_properties[$key]);
    	    }
    		return $this->_properties[$key];
    	}
    
    	return $default;
    }
    
    public function __get($key)
    {
    	return $this->getParam($key);
    }
    
    public function __isset($key)
    {
    	return isset($this->_properties[$key]);
    }
    
    public function __unset($key)
    {
    	if (isset($this->$key)) {
    		unset($this->_properties[$key]);
    	}
    }
    
    
    public function toArray()
    {
    	return $this->_properties;
    }
    
    public function toJson()
    {
        return Zend_Json::encode($this->toArray());
    }
    
    public function toZendConfig()
    {
        return new Zend_Config($this->toArray(),true);
    }
    
    /**
     * return entity properties as Json format
     */
    public function __toString()
    {
        return $this->toJson();
    }
    
    /*
     * Implement Countable interface ````````````````````````````````````
    */
    public function count()
    {    
    	return count($this->_properties);
    }
    
    /*
     * Implement Iterator interface ````````````````````````````````````
    */
    public function key()
    {
        return key($this->_properties);
    }
    
    public function rewind()
    {
    	$this->_iterator_index = 0;
    	
    	return reset($this->_properties);
    }
    
    public function next()
    {
    	$this->_iterator_index++;
    	
    	return next($this->_properties);
    }
    
    public function current()
    {
        $key = key($this->_properties);
                
        return $this->getParam($key);
    }
    
    public function valid() 
    {
        return ( $this->_iterator_index < $this->count() );
    }
}