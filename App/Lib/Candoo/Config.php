<?php
class Candoo_Config
{
    /**
     * Using to store config file to memory 
     * baraaie inke dar dastresi haaie ba`d har baar az file e config
     * khaande nashavad
     * 
     * @var array
     */
    protected static $_internalCache = array();
    
    /**
     * Data haaye config raa bar migardaanad,
     * maghaadir e baazgashti masalan agar haavie CONST haaye barnaame
     * bood (APP_DIR_TMP) jaaigozin mishanand
     * 
     * @note az yek cache daakheli estefaade mikonad ke pas az avalin daeaakhaani
     * data haa ra dar haafeze negah midaarad ke az load kardane file jolo giri 
     * shavad
     * 
     * @param string $filepath
     * @param string $env
     * @return boolean|Candoo_Dataset_Entity
     */
    public static function getConfig($filepath, $env = null)
    {
        // try to find config in internal cache
        $key = md5($filepath.$env);
        if ( isset(self::$_internalCache[$key]) ) 
        {
            $config = self::$_internalCache[$key];
        }
        else 
        {
            $config = self::loadConfig($filepath, $env);
            
            if (! $config ) {
            	return false;
            }
            
            $config = $config->toArray();
            $config = self::_buildValues($config);
            
            self::$_internalCache[$key] = $config;
        }
        
        return new Candoo_Dataset_Entity($config);
    }
    
	/**
     * Load configuration file
     * @todo baayad bar asaase .ext (extension e file) adapter e dorost raa entekhaab konad
     * 
     * @param  string $file
     * @throws Zend_Application_Exception When invalid configuration file is provided
     * @return Candoo_Dataset_Entity
     */
    public static function loadConfig($filepath, $env = null)
    {
        if (! file_exists($filepath)) {
        	throw new Zend_Exception('Config file not found in: '.$filepath);
        }
        
        // if application not setup or cache is not present
        if (! Candoo_Cache::isEnabled())
        {
            $config = new Zend_Config_Ini($filepath,null,array('allowModifications'=>true));
        }
        else 
        {
            $cache  = new Candoo_Cache_File($filepath,array($env));
            if ( ($config = $cache->load()) === false )
            {
            	$config = new Zend_Config_Ini($filepath,null,array('allowModifications'=>true));
             
            	$cache->save($config);
            }
        }
        
        if ( isset($config->$env) ) {
            $return = $config->$env;
        } elseif ( $env !== null ) {
            throw new Zend_Config_Exception();
        } else {
            $return = $config;
        }
        
        // yek config momken ast baraaie yek locale maghaadir e mokhtalefi daashte baashad
        // dar config:
        // [fa : production]
        // web.title = "رایا مدیا"
        /**
         * @todo be nazaram miaad ke injaa baayad ye hook baraaie khaandan e info ejraa
         * 		 shavad, masalan mitaavand az db bekhaanad config ra, ya dar in mored
         * 		 yek locale raa load mikonam
         */
        if (Candoo_App_Resource::isRegistered('locale')) {
            $locale = (string) Candoo_App_Resource::get('locale');
            if ( isset($config->$locale) ) {
            	$return = $return->merge($config->$locale);
            }
        }
        
    	return $return;
    }
    
    public static function resetInternalCache()
    {
        self::$_internalCache = array();
    }
    
    private static function _buildValues($vals) 
    {
        if (! is_array($vals)) {
        	   $vals = self::_replaceConsts($vals);
        	   return $vals;
        }
        
        $newOptions = array();
    	foreach ($vals as $key => $value) {
    	    $newOptions[$key] = self::_buildValues($value);
    	}
    	return $newOptions;
    }
    
    private static function _replaceConsts($value)
    {
        $search 	= array('{DS}', '{APP_DIR_TEMP}');
        $replace 	= array(DS, APP_DIR_TEMP);
        
       	$value = str_replace($search, $replace, $value);
       
        return $value;
    }
	
}
