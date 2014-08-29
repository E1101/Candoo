<?php
abstract class Candoo_Request_Http_Abstract extends Zend_Controller_Request_Http
{
    public function getMode()
    {
        return 'http';
    }
    
    public function isOnAdminArea()
    {
    
    }
    
    public function isOnServiceArea()
    {
    
    }
    
    public static function isAvailableRewriteMode()
    {
    	if (function_exists('apache_get_modules')) {
    		$modules = apache_get_modules();
    		$mod_rewrite = in_array('mod_rewrite', $modules);
    	} else {
    		/*
    		 * Set in your .htaccess file
    		* <IfModule mod_rewrite.c>
    		SetEnv HTTP_MOD_REWRITE On
    		# rewrite mode code
    		*/
    		$mod_rewrite =  (getenv('HTTP_MOD_REWRITE')=='On') ? true : false ;
    	}
    
    	return $mod_rewrite;
    }
    
    /**
     * Parameter haaye ersaal shode be safhe raa bar migardaanad
     * $_post va $_get
     * 
     * in parameter haa shaamel e module,controller,action nist
     */
    public function getVars()
    {
        $return = $this->getParams();
        
        if (isset($return[$this->getModuleKey()])) {
            unset ($return[$this->getModuleKey()]);
        }
        
        if (isset($return[$this->getControllerKey()])) {
        	unset ($return[$this->getControllerKey()]);
        }
        
        if (isset($return[$this->getActionKey()])) {
        	unset ($return[$this->getActionKey()]);
        }
        
        return $return;
    }
    
    public static function parseQueryString($query)
    {
    	$values = array();
    
    	if (!empty($query)) {
    		$pairs = explode("&", $query);
    		foreach ($pairs as $pair) {
    			@list($k, $v) = array_map("urldecode", explode("=", $pair));
    			$values[$k] = $v;
    		}
    	}
    
    	return $values;
    }
}
