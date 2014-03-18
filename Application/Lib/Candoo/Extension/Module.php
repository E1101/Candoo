<?php
class Candoo_Extension_Module 
{	
	/**
	 * List Madule haaye core e system
	 */
	protected static $_coreModules;

	/**
	 * List Madule haaye nasb shode
	 */
	protected static $_installedModules;
	
	public static function getCoreModules()
	{
		if (self::$_coreModules == null) {
			self::$_coreModules = Util_Filesystem_File::getSubDir(APP_DIR_CORE);
		} 
		
		return self::$_coreModules;		
	}

	public static function getInstalledModules() 
	{
		// dont list uninstallded modules, labeled with .unins extension
		if (self::$_installedModules == null) {
			self::$_installedModules = Util_Filesystem_File::getSubDir(APP_DIR_MODULES,'disable');
		} 
		
		return self::$_installedModules;
	}
	
	public static function isCoreModule($name)
	{		
		$coreModule = self::getCoreModules();
		if (!is_array($coreModule) || empty($coreModule)) {
			return false;
		}
		
		foreach ($coreModule as $i=>$v) {
			$coreModule[$i] = strtolower($coreModule[$i]);
		}
		
		if (in_array(strtolower($name), $coreModule)) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function isInstalledModule($name)
	{
		$modules      = self::getInstalledModules();
		if (!is_array($modules) || empty($modules)) {
			return false;
		}
		
		foreach ($modules as $i=>$v) {
			$modules[$i] = strtolower($modules[$i]);
		}
		
		if (in_array(strtolower($name), $modules)) {
			return true;
		} else {
			return false;
		}
	}
	

	/**
	 * Masir e file e marboot be configuration raa bar migardaanad agar mojood nabood false
	 */
	public static function getConfigFilepath($module)
	{
	    $host    = Multisite_Lib_Sites::getSite();
	    
		$mpath      = (self::isCoreModule($module)) ? APP_DIR_CORE : APP_DIR_MODULES;
		
		$filepath   = $mpath .DS. $module .DS. 'Conf' .DS. 'conf.' .$host. '.ini';
		$file 	      = file_exists($filepath) ? $filepath : false;
		
		// try to find parent site configuration and use it
		if (!$file) {
		    $host = Multisite_Lib_Sites::getParent($host);
		}
		$filepath   = $mpath .DS. $module .DS. 'Conf' .DS. 'conf.' .$host. '.ini';
		$file 	      = file_exists($filepath) ? $filepath : false;
		
		return $file;
	}
	
	/**
     * Load configuration file
     *
     * @param  string $file
     * @throws Zend_Application_Exception When invalid configuration file is provided
     * @return Candoo_Dataset_Entity
     */
    public static function getConfig($module,$env = null)
    {
    	$file = self::getConfigFilepath($module);
    	$config = new Zend_Config_Ini($file, $env);
    
    	return new Candoo_Dataset_Entity($config);
    }	
	
    /* public static function getConfig($module,$env = null)
    {
    	$file = self::getConfigFilepath($module);
    	 
    	$tag = md5(__CLASS__.__FUNCTION__.$module.$env);
    	$cache  = Zend_Cache::factory('File', 'File',
    			array(
    					'master_files' => array($file),
    					'automatic_serialization' => true,
    			),
    			array('cache_dir'	=>APP_DIR_CACHE)
    	);
    	if ( ($config = $cache->load($tag)) === false ) {
    		$config = new Zend_Config_Ini($file, $env);
    		$cache->save($config,$tag);
    	}
    		
    	return new Candoo_Dataset_Entity($config);
    } */
	
	
	
	
	

	
	
	

			
	/**
	 * Shaamel Route haaie Module haaie Core System niz mishavad
	 * @return Zend_Controller_Router_Interface
	 */
	public function getRoutes() 
	{
		$modules = $this->getModuleNames(true);
		
		// agar module mojood nabood
		if ($modules == null) {
			return;
		}
		
		$router = new Zend_Controller_Router_Rewrite();
		
		foreach ($modules as $name) 
		{
			$configFiles = $this->_routeConfigsFiles($name);			
			foreach ($configFiles as $file) {
				// new zend_config with allow modification     V
				$config = new Zend_Config_Ini($file, 'routes',true);
				
				/**
				 * dar config router haa [module_name]->route agar
				 * haavie [backend] bood baa address backend site
				 * dar config.ini->web->url->backend jaaeegozin mishavad
				 */
				$backend  = Candoo_Config::getConfig()->web->url->backend;
				while($route = $config ->routes->current()) {
					// agar [backend] dar address mojood bood !!					
					if( strpos($route->route,'[backend]') !== false) {
						$route ->route = str_replace('[backend]',$backend,$route->route);
					}
				    if( strpos($route->reverse,'[backend]') !== false) {
						$route ->reverse = str_replace('[backend]',$backend,$route->reverse);
					}			
					$config ->routes->next();
				}
												 
				$router->addConfig($config, 'routes');
			}
			
		}
		
		return $router;
	}
	
	/**
	 * @return array
	 */
	private function _routeConfigsFiles($moduleName) 
	{
		$dir = APP_DIR_APPLICATION .DS. 'modules' .DS. $moduleName .DS. 'config' .DS. 'routes';
		if (!is_dir($dir)) {
			/* Dar Folder Core Ham be donbaale File haaie in module migardad */
			$dir = APP_DIR_APPLICATION .DS. 'core' .DS. $moduleName .DS. 'config' .DS. 'routes';
			if (!is_dir($dir)) {
				return array();
			}
		}
		
		$configFiles = array();
		
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $file)	{
            if ($file->isDot() || $file->isDir()) { continue; }
            
            $name = $file->getFilename();
            if (preg_match('/^[^a-z]/i', $name) || ('CVS' == $name) || ('.svn' == strtolower($name))) {
                continue;
            }
            $configFiles[] = $dir .DS. $name;
        }
		
		return $configFiles;
	}

	public static function getModules($inCore = false)
	{
		$modules = array();
		$allModules = self::getModuleNames($inCore);
		foreach ($allModules as $module) 
		{
			//if ($module == 'core' && !$inCore) { continue;	}
			if (self::isCoreModule($module)) {
				$file = APP_DIR_APPLICATION .DS. 'core' . DS . $module . DS . 'config' . DS . 'about.xml';
			} else {
				$file = APP_DIR_APPLICATION .DS. 'modules' . DS . $module . DS . 'config' . DS . 'about.xml';
			}
			if (!file_exists($file)) { continue; }
			
			$xml 	 = simplexml_load_file($file);
						
			$info = array(
				'name' 		  => (string)$xml->name,
			    'base_name'   => $module,
				'description' => (string)$xml->description,
				'thumbnail'   => (string)$xml->thumbnail,
				'author' 	  => (string)$xml->author,
				'version' 	  => (string)$xml->version,
				'license' 	  => (string)$xml->license,
			);
			
			$info['required'] = array(
				'modules' => array(),
				'libs' 	  => array(),
				'other'   => null,
			);
			if ($xml->requires) 
			{
				if ($xml->requires->requiredModules) 
				{
					foreach ($xml->requires->requiredModules->require as $mod) 
					{
						$attrs = $mod->attributes();
						$info['required']['modules'][] = (string) $attrs['name'];
					}
				}
				if ($xml->requires->libs) 
				{
					foreach ($xml->requires->libs->lib as $lib) {
						$info['required']['libs'][] = array(
							'type' 		  => (string)$lib->type,
							'name' 		  => (string)$lib->name,
							'link' 		  => (string)$lib->link,
							'description' => (string)$lib->description,
						);
					}
				}
				if ($xml->requires->other) 
				{
					$info['required']['other'] = (string)$xml->requires->other;
				}
			}//end of requires
			
			$modules[] = $info;
		}
		
		return $modules;
	}
}
