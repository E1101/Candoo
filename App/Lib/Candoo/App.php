<?php
/**
 * Candoo_App
 *
 * Tamaamie darkhastha az injaa shoroo be pardaazesh mishavand,
 * > autoloading raa fa`aal mikonad
 * > config haaie barnaame raa bar asaase configuration daade shode set mikonad
 * > plugin haaie pish farz raa baar gozaari mikonad
 * > baseUrl va parameter haaie global mesle language va gheire raa set mikonad
 * > tavasote Zend_Controller_Front amal dispatch raa anjaam midahad
 * 
 * @uses Candoo_Request_Abstract, Flex_Site_Config, Zend_Controller_Front
 * @package Flex_App
 */
class Candoo_App
{
	/**
     * requested Php version to run
     */
	public static $reqPhpVer = '5.3.0';
		
    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Candoo_App
     */
    protected static $_instance = null;
 
	/**
     * enviorement ke yaa tavasote htaccess be barname daade shode 
     * va ya dar hengaame const, mitavan config haaye motafaaveti 
     * raa bar in asaas load kard
     * @var string
     */
	protected $_environment;
				
	/**
	 * Zamaani ke tavasote method run barnaame dispatch mishavad
	 * in flag true set mishavad
	 * @link isRun()
     * @var array
     */
	protected $_isAppRun = false;
	
	/**
	 * Plugin haaee ke tavasote user register mishavad index
	 * migirad va be tartib register mishavand
	 * 
	 * @link registerPlugin()
     */	
	protected static $_plugIndex = 50;
	
	const ENV_LIVE 		  = 'live';
	const ENV_DEVELOPMENT = 'development';
	const ENV_PRODUCTION  = 'production';
	const ENV_DESIGNING   = 'designing';
	
    /**
     * Constructor
     *
     * Agar $option ersaal nashavd tanzimaate site ra az folder e config
     * va be nesbate $environment mikhaanad
     *
     * @param string $environment
     * @param string|Zend_Config|array $options
     * @return void
     */
	protected function __construct() 
	{
		// check for required php version
		if (version_compare(phpversion(), self::$reqPhpVer, '<') === true) {
		    throw new Zend_Exception('ERROR : requires PHP '.self::$reqPhpVer. ' or newer.');
		}
		
		/* Register view */
		if (! Candoo_App_Resource::isRegistered('view') ) {
			Candoo_App_Resource::set('view', new Zend_View());
		}
				
		// register some default resources
		/* Register frontController */
		if (! Candoo_App_Resource::isRegistered('frontController') ) {
			Candoo_App_Resource::set( 'frontController', Zend_Controller_Front::getInstance() );
		}
		
		/* Register request */
		if (! Candoo_App_Resource::isRegistered('request') ) {
			Candoo_App_Resource::set( 'request', new Candoo_Request_Http() );
		}
	}

    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone() { }
    
    /**
     * Singleton instance
     *
     * @return Zend_Controller_Front
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    public function run()
    {
        $options    = self::getConfig();
        $options    = $options->toArray();
        $this->setConfigs($options);
        
        // register hooks 
        $this->_initHooks();
        
        /**
         * Executing hooks on startup
         */
        Candoo_Addon_Registry::exec('candoo', 'onAppRun');
        
        
    	$request = Candoo_App_Resource::get('request');
    	if ($request->isOnServiceArea()){
    		$this->execSeMod();
    	} else {
    		$this->exec();
    	}
    }
    
    protected function _initHooks()
    {
    	$config = self::getConfig();
    	 
    	$this->_initHooks = true;
    
    	if(!$config->extension && !$config->extension->hook) {
    		return;
    	}
    
    	// register addons as hook
    	$hooks = $config->extension->hook;
    
    	Candoo_Addon_Registry::registerAddons($hooks->toArray());
    	 
    	/**
    	 * register shutdown hooks
    	 */
    	function candoo_app_shutdown_call() {
    		Candoo_Addon_Registry::exec('candoo', 'onScriptShutdown');
    	}
    	register_shutdown_function('candoo_app_shutdown_call');
    }
    
	
    /**
     * Run the application
     *
     * @return void
     */
	protected function exec()
	{
	    $request = Candoo_App_Resource::get('request');
		$front = Candoo_App_Resource::get('frontController');
		
		// front don`t throw exceptions on live
		if ($this->getEnvironment() == self::ENV_LIVE ) {
		    $front->throwExceptions(false);
		}
		
		/**
		 * Executing hooks 
		 */
		//Candoo_Addon_Registry::exec('candoo', 'beforeCoreInitalize');
		
		// ** Initalize Application ```````````````````````````````````````````````````````````````````````|
		Candoo_App_Initalize::init();
		
		/**
		 * Executing hooks
		 */
		//Candoo_Addon_Registry::exec('candoo', 'afterCoreInitalize');
		//Candoo_Addon_Registry::exec('candoo', 'beforeModulesInitalize');
		
		/* Just before run the application run initalize application modules */
		Candoo_App_Initalize::initModules();
		// ````````````````````````````````````````````````````````````````````````````````````````````````	
		
		/**
		 * Executing hooks
		 */
		//Candoo_Addon_Registry::exec('candoo', 'afterModulesInitalize');
		
		// set dispatching directory
		$front->addModuleDirectory(APP_DIR_CORE);
		$front->addModuleDirectory(APP_DIR_MODULES);
		
		// Zend_Controller_Front automaticaly change request object baseUrl to default Front baseUrl on dispatch
		$front->setBaseUrl(Candoo_App_Resource::get('request')->getBaseUrl());
		
		/*
		 * Hengaame dispatch shodan e yek module bootstrap e aan raa ejraa mikonad
		 */
		$this->registerPlugin(new Candoo_App_Bootstraper_Plugin);
		
        // run application
        $this->_isAppRun = true;
        $front ->dispatch(Candoo_App_Resource::get('request'));
	}
	
	/**
	 * Run the application
	 *
	 * @return void
	 */
	protected function execSeMod()
	{	    
		$request = Candoo_App_Resource::get('request');		 
		$front = Candoo_App_Resource::get('frontController');
			
		// Zend_Controller_Front automaticaly change request object  baseUrl to default Front baseUrl on dispatch
		$front->setBaseUrl(Candoo_App_Resource::get('request')->getBaseUrl());
		
		// front don`t throw exceptions
		$front->throwExceptions(false);
		$front->setParam('noErrorHandler',true);
				
		// `````````````````````````````````````````````````````````````````````````````````````|
		// set controller directory to service folder inside modules 
		$front->setModuleControllerDirectoryName('services'.DS.'controllers');
		
		// set dispatching directory
		$front->addModuleDirectory(APP_DIR_CORE);
		$front->addModuleDirectory(APP_DIR_MODULES);
		// `````````````````````````````````````````````````````````````````````````````````````
		
		$view = Candoo_App_Resource::get('view');
		
		/**
		 * Add view helpers ```````````````````````````````````````````````````````````````````````````````````|
		 */
		$view->addHelperPath(APP_DIR_EXTENSION .DS. 'Helpers' .DS. 'View' , 'Helpers_View_');
		
		// Helper haaie Module haaie core ghaabeliat e dastresi daashte baashand
		$coreModules = Candoo_Module::getCoreModules();
		foreach ($coreModules as $module) {
			$helperDir = APP_DIR_CORE .DS. $module .DS. 'Helpers' .DS. 'View';
			if (is_dir($helperDir)) {
				$view->addHelperPath($helperDir, $module.'_Helpers_View_');
			}
		}
		
		/*
		 * Hengaame dispatch shodan e yek module bootstrap e aan raa ejraa mikonad
		*/
		$this->registerPlugin(new Candoo_App_Bootstraper_Plugin);
	
		// run application
		$this->_isAppRun = true;
		$front ->dispatch(Candoo_App_Resource::get('request'));
	}
	
    /**
     * Check for program that is dispatched 
     *
     * @return boolean
     */
	public function isRun()
	{
		return $this->_isAppRun;
	}
	
	/**
	 * Add application options
	 *
	 * Option haai ke injaa set mishavand, faghat zakhire mishavand va dar barnaame ghaabel
	 * dastresi hastend
	 *
	 * @param  Zend_config|array $options
	 * @return void
	 */
	protected function setConfigs($options)
	{
		if ($options instanceof Zend_Config || $options instanceof Candoo_Dataset_Entity) {
			$options = $options ->toArray();
		} elseif (!is_array($options)) {
			throw new Zend_Exception('Invalid options provided; must be a config object, or an array');
		}
	
		Candoo_App_Config::setConfigs($options);
	}
	
	// ````````````````````````````````````````````````````````````````````````````````````````
	
	
	/**
	 * Masiri raa ke app az aan ejraa shode ast raa bar migardaanad
	 * /subfolder/index.php
	 *
	 * mitavaan baraaie address dehi be resource haa dar web estefaade shavad
	 */
	public static function getScriptUri()
	{
		$filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';
	
		if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
			$baseUrl = $_SERVER['SCRIPT_NAME'];
		} elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
			$baseUrl = $_SERVER['PHP_SELF'];
		} elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
			$baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
		} else {
			// Backtrack up the script_filename to find the portion matching
			// php_self
			$path    = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
			$file    = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
			$segs    = explode('/', trim($file, '/'));
			$segs    = array_reverse($segs);
			$index   = 0;
			$last    = count($segs);
			$baseUrl = '';
			do {
				$seg     = $segs[$index];
				$baseUrl = '/' . $seg . $baseUrl;
				++$index;
			} while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
		}
	
		return $baseUrl;
	}
	
	public static function getBasePath()
	{
	    return dirname(self::getScriptUri());
	}
	
	// ````````````````````````````````````````````````````````````````````````````````````````
	
		
    /**
     * Register plugin
     *
     * @return Candoo_App
     */
	public function registerPlugin($plugin,$index=null)
	{
		// agar barnaame ejraa shode bood
		if ($this->isRun()) {
			throw Zend_Exception ('Plugins can`t register after running program');
		}
		
		if ($index == null) {
			$index = &self::$_plugIndex;			
			$index +=10;
		}
		
		$front = Candoo_App_Resource::get('frontController');
    	
    	if (is_string($plugin)) {
    		if (class_exists($plugin)) {
    			$front->registerPlugin(new $plugin(), $index);
    		} else {
    			throw Zend_Exception ($plugin.' Not Found.',E_USER_ERROR);
    		}
    		
    	} elseif (is_object($plugin)) {
    		if ($plugin instanceof Zend_Controller_Plugin_Abstract) {
    			$front->registerPlugin($plugin, $index);
    		} else {
    			throw Zend_Exception ('Plugin must instance of Zend_Controller_Plugin_Abstract.',E_USER_ERROR);
    		}
    		  		
    	} elseif (is_array($plugin)) {
    		foreach ($plugin as $p) {
    			if (class_exists($p)) {
    				$front->registerPlugin(new $p(), $index);
    				$index +=10;
    			} else {
    				throw Zend_Exception ($plugin.' Not Found.',E_USER_ERROR);
    			}
    		}
    	}

    	$index +=10;
    	
		return $this;
	}
	
	public static function getPluginIndex($class)
	{
	    $front = Candoo_App_Resource::get('frontController');
	    
	    if (! $front->hasPlugin($class)) {
	        return false;
	    }
	    
	    $plugins = $front ->getPlugins();
	    foreach ($plugins as $i => $pl) {
	        if (get_class($pl) == $class) {
	            return $i;
	        }
	    }
	    
	    return false;
	}
	
	public static function getLastPluginIndex()
	{
		return self::$_plugIndex;
	}
	
	// ````````````````````````````````````````````````````````````````````````````````````````
	
	
	/**
	 * Yek name raa be onvaane resource sabt mikonad
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public static function setResource($name,$value)
	{
		Candoo_App_Resource::set($name, $value);
		 
		return $this;
	}
	
	public static function getResource($name)
	{
		return Candoo_App_Resource::get($name);
	}
	
	// ````````````````````````````````````````````````````````````````````````````````````````
	
	public static function getConfDirPath()
	{
		return APP_DIR_APPLICATION .DS. 'Conf';
	}
	
	/**
	 * Masir e file e marboot be configuration raa bar migardaanad agar mojood nabood false
	 */
	public static function getConfigFilepath()
	{
		$host    = Candoo_Site::getSite();
						 
		$filepath = self::getConfDirPath() .DS. 'conf.' .$host. '.ini';
		$file 	  = file_exists($filepath) ? $filepath : false;
		
		return $file;
	}
	
	/**
	 * Load configuration file
	 *
	 * @param  string $file
	 * @throws Zend_Application_Exception When invalid configuration file is provided
	 * @return Candoo_Dataset_Entity
	 */
	public static function getConfig()
	{
	    $env  = self::getInstance()->getEnvironment();
		$file = self::getConfigFilepath();
		
		if (!$file) {
		    throw new Zend_Exception('Application Configuration file not found.');
		}
	
		return Candoo_Config::getConfig($file, $env);
	}

	// ````````````````````````````````````````````````````````````````````````````````````````
	
	/**
	 * Retrieve current environment
	 *
	 * @return string
	 */
	public function getEnvironment()
	{
		if ($this->_environment === null) {
			// TODO: try to detect enviorment from user IP if APP_ENV not set. 127.0.0.1 for local and production
			$this->_environment = getenv('APP_ENV') ? getenv('APP_ENV') : self::ENV_PRODUCTION;
		}
	
		return $this->_environment;
	}
	
	protected function setEnviorment($env)
	{
		$this->_environment = strtolower($env);
	}
	
	
	// ````````````````````````````````````````````````````````````````````````````````````````
	
	/**
     * set php.ini setting
     * 
     * @see ini_get ($varname) SPL
     * @param array $settings array('phpini_key' => 'value') 
     */
    public static function setPhpsettings($settings)
    {
    	if (is_array($settings)) {
    		foreach ($settings as $key => $value) {
                ini_set($key, $value);
        	}
    	}

        return $this;
    }
    
    	
	 
}