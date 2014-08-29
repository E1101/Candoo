<?php
class Candoo_Module 
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
	
	/**
	 * Module haaee ra ke baraaie in site dar file config gharaar daarad
	 * raa bar migardaanad
	 * 
	 * in module haa faghat shaamel e module haaye install hastand
	 * az core chizi entekhaabi nist
	 */
	public static function getActiveModules()
	{
	    $config = Candoo_App::getConfig();
	    if (!$config->extension && !$config->extension->modules) {
	        return array();
	    }
	    
	    return explode(',', $config->extension->modules);
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
	public static function getConfigDirPath($module)
	{
		$host    = Candoo_Site::getSite();
		 
		$mpath   = (self::isCoreModule($module)) ? APP_DIR_CORE : APP_DIR_MODULES;
		
		return $mpath.DS.$module.DS.'Conf';
	}

	/**
	 * Masir e file e marboot be configuration raa bar migardaanad agar mojood nabood false
	 */
	public static function getConfigFilepath($module)
	{
	    $mpath = self::getConfigDirPath($module);
		
	    $host    = Candoo_Site::getSite();
	    
		$filepath   = $mpath .DS. 'conf.' .$host. '.ini';
		$file 	    = file_exists($filepath) ? $filepath : false;
		
		return $file;
	}
	
	/**
     * Load configuration file
     *
     * @param  string $file
     * @throws Zend_Application_Exception When invalid configuration file is provided
     * @return Candoo_Dataset_Entity
     */
    public static function getConfig($module)
    {
    	$file = self::getConfigFilepath($module);
    	
    	return Candoo_Config::getConfig($file);
    }
	
}
