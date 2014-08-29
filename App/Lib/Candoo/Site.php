<?php
class Candoo_Site
{
    protected static $_site;
    
    // Site and sub sites control `````````````````````````````````````````````````````````````````````````````````````````````|
    /**
     * Naame Site-i- raa ke barnaame baraaie aan ejraa shode raa bar migaardaanad
     */
    public static function getSite()
    {
        if(self::$_site === null) {
            $request = Candoo_App_Resource::get('request');
            
            if ($request->getMode() == 'http') {
                $host = $request->getHttpHost();
                // one domain maybe have other aliases exp: localhost aliases 127.0.0.1
                $host = self::getParentAlias($host);
                
                // looking for subsite detection from uri ````````````````````````````````````````````|
                $baseUrl = Candoo_App::getScriptUri();
                if ($request->isAvailableRewriteMode()) {
                	$baseUrl = dirname($baseUrl);
                }
                $requestUri = $request->getRequestUri();
                
                // az basePath be ba`d /candoo/[index.php]/route
                // 				  yaaa /candoo/route
                $ptInf = ltrim(substr($requestUri, strlen($baseUrl)),'/');
                $ptInf = explode('/', $ptInf);
                $msub    = $ptInf[0];
                
                if (self::isSubsited($msub,$host)) {
                	$host = $msub;
                }
                // ```````````````````````````````````````````````````````````````````````````````````
                
                self::setSite($host);
            }
        }
           
        return self::$_site;
    }
    
    public static function setSite($name)
    {
        self::$_site = strtolower($name);
    }
    
    /**
     * Ba`zi site haa mitavaanand alias e yek domain e asli baashand
     * masalan 127.0.0.1 ham mitavaanad localhost baashad
     * yaa     domain.com baa domain.ir 
     * 
     * @param string $host
     */
    public static function getParentAlias($host)
    {
    	$zfConf = self::_loadConfig();

    	if ($zfConf->get($host)) {
    		// Be donbaale in migardad ke aayaa az domain digar extend shode ??
    		$extends = $zfConf->getExtends();
    		    		
    		if (array_key_exists($host, $extends)){
    			return $extends[$host];
    		}
    			 
    		return $host;
    	}

    	return $host; 
    }
    
    /**
     * yek site momken ast subsite e yek host e digar baashad in method
     * site e parent raa bar migardaanad
     * 
     * @param string $name
     */
    public static function getParent($host)
    {
        $host = self::getParentAlias($host);
        
        $zfConf = self::_loadConfig();
        $config = $zfConf->toArray();
        
        foreach ($config as $parentHost => $subHost) {
            // test mikonim ke in parentHost yek naame alias nabaashad
            if (self::getParentAlias($parentHost) != $parentHost) {
                continue;
            }
            
            if(is_array($subHost)) {
                foreach ($subHost as $h) {
                    if ($h == $host) {
                        return $parentHost;
                    }
                }
            }
        }
               
        return $host;
    }
    
    public static function isSubsited($name,$host)
    {
    	$zfConf = self::_loadConfig();
    	
    	$config = $zfConf->toArray();
    
    	$name = strtolower($name);
    	$host = strtolower($host);
    
    	if (isset($config[$host]))
    	{
    		if (isset($config[$host][$name])){
    			return in_array($name, $config[$host][$name]);
    		}
    	}
    
    	return false;
    }
    
    protected static function _loadConfig()
    {
        $file = Candoo_App::getConfDirPath() .DS. 'sites.ini';
        
        /* agar config be in soorat load shavad getExtends() meghdaar e khaali bar migardaanad
         * $config  = Candoo_Config::loadConfig($file); */
         
        /* // baraaie inke extend haa ro ehtiaaj daarim
         $zfConf  = $config->toZendConfig(); */
         
        $zfConf = Candoo_Config::loadConfig($file);
        
        return $zfConf;
    }
        
}