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
 * @uses Candoo_Request, Flex_Site_Config, Zend_Controller_Front
 * @package Flex_App
 */
class Candoo_App
{
	/**
     * requested Php version to run
     */
	public static $reqPhpVer = '5.2.0';
		
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
	
	public static  $ENV_LIVE 		= 'live';
	public static  $ENV_DEVELOPMENT = 'development';
	public static  $ENV_PRODUCTION	= 'production';

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
    		die('ERROR : requires PHP '.self::$reqPhpVer. ' or newer.');
		}
		
		// register namespaces for autoloading
		$this->_initAutoload();
		
		// register some default resources
		/* Register frontController */
		if (! Candoo_App_Resource::isRegistered('frontController') ) {
			Candoo_App_Resource::set( 'frontController', Zend_Controller_Front::getInstance() );
		}
		
		/* Register view */
		if (! Candoo_App_Resource::isRegistered('view') ) {
			Candoo_App_Resource::set( 'view', new Zend_View() );
		}
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$viewRenderer ->setView(Candoo_App_Resource::get('view')); // you can register your own view
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		Candoo_App_Resource::set('viewRenderer', $viewRenderer);

		/* Register request */
		if (! Candoo_App_Resource::isRegistered('request') ) {
			Candoo_App_Resource::set( 'request', new Candoo_Request() );
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
    
    /**
     * Set application options
     * 
     * Dar nahaaiat bar asaase option haaie ersaali be donbaale method e 
     * set+OptionKey migardad va value haaye optionKey raa niz be onvaane
     * Voroodi be aan midahad va method raa ejraa mikonad, har method tanzimaati
     * raa anjaam midahad 
     *
     * @param  APP_SITE_CONFIG|string|Zend_config|array $options agar string bood hatman masire file config ast
     * @return void
     */
	public function setup($enviorment = 'production', $options = null)
	{
		// enviorment used for loading separated configuration
		$this->_environment = $enviorment;
		
		if ($options == null) {
        	$options = Candoo_Extension_Module::getConfig('Multisite',$this->getEnvironment());
        	$options = $options->toArray();
        	
		} elseif (is_string($options)) {
        	
        } elseif (is_object($options) && method_exists($options, 'toArray')) {
        	$options = $options->toArray();
        } 
        
        if (!is_array($options)) {
        	throw new Zend_Application_Exception('Invalid options provided; must be location of config file, a config object, or an array');
       	} 
        $options = array_change_key_case($options, CASE_LOWER);
        
        $this->setConfigs($options);
        	
		return $this;
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
	public function setConfigs($options)
	{
		if ($options instanceof Zend_Config || $options instanceof Candoo_Dataset_Entity) {
			$options = $options ->toArray();
		} elseif (!is_array($options)) {
			throw new Zend_Exception('Invalid options provided; must be a config object, or an array');
		}
		
		Candoo_Config::getInstance()->setConfigs($options);
	}

	
    /**
     * Run the application
     *
     * @return void
     */
	public function run()
	{
	    $request = Candoo_App_Resource::get('request');
	    
		$front = Candoo_App_Resource::get('frontController');
		
		// masiri ke barnaame az aaan folder ejraa shode
		$basePah = $request ->getBaseUrl();
		// be khaatere inke baseUrl mitavaanad ta`ghir konad besePath ro be in masir tahir midahim
		// basePath masiri raa moa`yan mikonad ke masalan file haa rooie server gharaar daarand
		// /candoo/images/file.jpg dar haali ke baseUrl mitavand /candoo/fa/... baaashad
		$request->setBasePath($basePah);
		
		// front don`t throw exceptions on live
		if ($this->getEnvironment() == self::$ENV_LIVE ) {
		    $front->throwExceptions(false);
		}		
		
		// ** Initalize Application ```````````````````````````````````````````````````````````````````````|
		Candoo_App_Initalize::init();
		
		/* Just before run the application run initalize application modules */
		Candoo_App_Initalize::initModules();
		// ````````````````````````````````````````````````````````````````````````````````````````````````	
		
		// set dispatching directory
		$front->addModuleDirectory(APP_DIR_CORE);
		$front->addModuleDirectory(APP_DIR_MODULES);
		
		// Zend_Controller_Front automaticaly change request baseUrl to default Front baseUrl on dispatch
		$front->setBaseUrl(Candoo_App_Resource::get('request')->getBaseUrl());
		// i want same basePath after modules initalize
		$request->setBasePath($basePah);
		
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
	 * Yek name raa be onvaane resource sabt mikonad
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function setResource($name,$value)
	{
	 	Candoo_App_Resource::set($name, $value);
	 	
	 	return $this;
	}
	
	public function getResource($name)
	{
	    return Candoo_App_Resource::get($name);
	}
		
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
	
	
	// Application Option Seter methods ``````````````````````````````````````````````````````````````````````````````````````````|	
    /**
     * set php.ini setting
     * 
     * @see ini_get ($varname) SPL
     * @param array $settings array('phpini_key' => 'value') 
     */
    public function setPhpsettings($settings)
    {
    	if (is_array($settings)) {
    		foreach ($settings as $key => $value) {
                ini_set($key, $value);
        	}
    	}

        return $this;
    }
	// __________________________________________________________________________________________ Application Option Seter methods | 

    
    /**
     * set Autoloading namespaces and path`s
     */
    protected function _initAutoload()
    {
    	include_once 'Zend/Loader/Autoloader.php';
		$autLoader = Zend_Loader_Autoloader::getInstance();
		// don`t show warning for including not founded class
		$autLoader ->suppressNotFoundWarnings(true);
		// using for library classes
		$autLoader->registerNamespace('Candoo_');
		$autLoader->registerNamespace('Util_');
		// estefaadeie emkaanat e system
		set_include_path(PATH_SEPARATOR. APP_DIR_EXTENSION .PATH_SEPARATOR. get_include_path());
		$autLoader->registerNamespace('Hooks_');
		$autLoader->registerNamespace('Plugins_');
		$autLoader->registerNamespace('Widgets_');
		
		// baraaie estefaade az -> new Modulename(); yaa estefaade az har resource digar tavasote autoload
		set_include_path(PATH_SEPARATOR. APP_DIR_CORE .PATH_SEPARATOR. get_include_path());
		$coreModules = Candoo_Extension_Module::getCoreModules();
   		 foreach ($coreModules as $module) {
   		 	$autLoader->registerNamespace($module.'_');
		}
		
		set_include_path(PATH_SEPARATOR. APP_DIR_MODULES .PATH_SEPARATOR. get_include_path());
    	$modules = Candoo_Extension_Module::getInstalledModules();
		foreach ($modules as $module) {
   		 	$autLoader->registerNamespace($module.'_');	
		}
    }
	
    /**
     * Retrieve current environment
     *
     * @return string
     */
    public function getEnvironment()
    {        
        return strtolower($this->_environment);
    }
    
    protected function setEnviorment($env) 
    {
        $this->_environment = $env;
    }
    
    
	 
}