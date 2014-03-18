<?php
class Multisite_Lib_Sites
{
    protected static $_host;
          
    // Site and sub sites control `````````````````````````````````````````````````````````````````````````````````````````````|
    /**
     * Naame domain i raa ke barnaame baraaie aan ejraa shode raa bar migaardaanad
     */
    public static function getSite()
    {
    	if (isset(self::$_host)) {
    		return self::$_host;
    	}
    
    	$host = strtolower($_SERVER['HTTP_HOST']);
    	// one domain maybe have other aliases exp: localhost aliases 127.0.0.1
    	$host = self::getSiteAliasOf($host);
    
    	// looking for subsite detection from uri
    	$req = Candoo_App_Resource::get('request');
    	$pathinf = $req ->getPathInfo();    	
    	$pathinf = explode('/', $pathinf);
    	
    	$msub    = $pathinf[1];
    	if (self::isSubsited($msub,$host)) {
    		$host = $msub;
    	}
    	
    	/**
    	 * Emkaan daarad stack e aval az uri dar ekhtiaare /admin baashad
    	 * 
    	 */
    	/* $msub    = $pathinf[2];
    	if (self::isSubsited($msub,$host)) {
    		$host = $msub;
    	} */
    	    
    	self::$_host = strtolower($host);
    
    	return self::$_host;
    }
    
    public static function getSiteAliasOf($host)
    {
    	$domains = APP_DIR_CORE .DS. 'Multisite' .DS. 'Conf' .DS. 'sites.ini';
    	$config = new Zend_Config_Ini($domains);
    
    	if (isset($config->$host)){
    		/* Be donbaale in migardad ke aayaa az domain digar extend shode ?? */
    		$extends = $config->getExtends();
    			
    		if (array_key_exists($host, $extends)){
    			return $extends[$host];
    		}
    			
    		return $host;
    	}
    
    	return $host;
    }
    
    /**
     * Domain e asli raa bar migardaanad, emkaan daarad ye domain alias baashad
     * va yaa inke host e konooni yek subsite baashad vali in dastoor domain e 
     * maadar raa bar migardaanad
     * 
     * @param string $name
     */
    public static function getParent($name)
    {
        $host = strtolower($_SERVER['HTTP_HOST']);
        $host = self::getSiteAliasOf($host);
        
        return $host;
    }
    
    public static function isSubsited($name,$host)
    {
    	$domains = APP_DIR_CORE .DS. 'Multisite' .DS. 'Conf' .DS. 'sites.ini';
    
    	$config = new Zend_Config_Ini($domains);
    	$config = $config->toArray();
    
    	$name = strtolower($name);
    	$host = strtolower($host);
    
    	if (isset($config[$host]))
    	{
    		if (isset($config[$host]['subsite'])){
    			return in_array($name, $config[$host]['subsite']);
    		}
    	}
    
    	return false;
    }
        
}