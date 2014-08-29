<?php
class Core_Init extends Candoo_App_Initalize_Abstract
{
    protected $_config;
    
    public function __construct()
    {
        $this->_config = $this->getConfig();
    }
    
    public function _initRoutes()
    {
        // remove default routes, default routes registered via module inital
        $front   = Candoo_App_Resource::get('frontController');
        $request = Candoo_App_Resource::get('request');
        
        // admin area use default route system (module)
        if (! $request->isOnAdminArea() ) {
            $front->getRouter()->removeDefaultRoutes();
        }
        
    }
    
    /**
     * Dar ba`zi az server haa momken ast ke automatic magic quote fa`aal baashad
     * ke mojeb mishavad charachter haaye "\ ro ezaafe konad
     * 
     * baraaie jolo giri az in mored
     */
    public function _initMagicQuotes()
    {
        if (get_magic_quotes_gpc())
        {
        	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
        	while (list($key, $val) = each($process)) {
        		foreach ($val as $k => $v) {
        			unset($process[$key][$k]);
        			if (is_array($v)) {
        				$process[$key][stripslashes($k)] = $v;
        				$process[] = &$process[$key][stripslashes($k)];
        			} else {
        				$process[$key][stripslashes($k)] = stripslashes($v);
        			}
        		}
        	}
        	unset($process);
        }
    }
    
    public function _initDb()
    {
        if (!$this->_config->db) {
            return;
        }
        
        $config = $this->_config->db->toZendConfig();
        
        // seting database charset ```````````````````````````````````````````````````````````|
        // this way don`t open connection on each page request (app_run) and leave this on demand
        $charset = $config->params->charset;
        $charset = ($charset) ? $charset : 'utf8';
        $config->params->charset = $charset;
                
        /* Configures PDO to execute the 'SET NAMES UTF8;' SQL query just before
         any other query. If no query is executed on your page, this will not be       
         executed. */
        $pdoParams = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8;');
        //$pdoParams = array(MYSQLI_INIT_COMMAND => 'SET NAMES UTF8;');
        $config->params->driver_options = $pdoParams;
        // ````````````````````````````````````````````````````````````````````````````````````
         
    	$db = Zend_Db::factory($config);
    	$db ->setFetchMode(Zend_Db::FETCH_OBJ);
    	
    	// this way open a db connection on each app run and init DB
    	//$db ->query("SET NAMES '$charset'");
    	//$db ->query("SET CHARACTER SET '$charset'");
    	//$db ->query("SET character_set_connection='$charset'");
    	
    	// set metadata caching 
    	if (Candoo_Cache::isEnabled()) {
    	    Zend_Db_Table_Abstract::setDefaultMetadataCache(new Candoo_Cache_Core());
    	}
    	    	
    	// storing database object instance
    	Candoo_App_Resource::set('db', $db);
    }
    
    public function _initHelperAction()
    {
        /**
         * Ta`rif kardan e helper action haa baraaie barnaame
         */
        Zend_Controller_Action_HelperBroker::addPath(APP_DIR_EXTENSION .DS. 'Helpers' .DS. 'Action','Helpers_Action_');
        
        $config = $this->_config;
        
        if ($config->extension && $config->extension->helper && $config->extension->helper->action) {
            foreach ($config->extension->helper->action as $helper) {
            	Zend_Controller_Action_HelperBroker::addHelper(new $helper());
            }
        }
    }
    
    public function _initView()
    {
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
        /** ````````````````````````````````````````````````````````````````````````````````````````````````` */

        $config = $this->_config;
        
        $view->headTitle ($config->web->title) ->setSeparator(' :: ');
        
        $view->headMeta() ->appendName ('GENERATOR','Candoo '.Candoo_Version::VERSION);
        
        $view->headMeta()->appendName('keywords',    $config->web->meta->keyword);
        $view->headMeta()->appendName('description', $config->web->meta->description);
        
        $view->headMeta() ->appendName ('copyright 2012','Payam Naderi. Programmer and Web Developer , naderi.payam@gmail.com');
    }
    
    /**
     * Baraaie estefaade az jQuery dar barnaame aaan raaa enable mikonim
     *
     */
    public function _initJQuery()
    {
        $config = $this->_config;
        
        $view = Candoo_App_Resource::get('view');
        // add helperPath to ZendX/JQuery/View/Helper
        // ba`d az in mitavaan be jQuery tavasato view helper e ->jQuery() dastresi daasht
        ZendX_JQuery::enableView($view);
    	
        /**
         * You can access jQuery helper this way:
         * $jQuery = Candoo_App_Resource::get('view')->jQuery();
         */
        $jQuery = $view->jQuery();
        
    	// use local library of jQuery
    	if ($config->web->jQuery->useCdn != 'true') {
    	    $jQuery->setLocalPath(Candoo_Assets::getURL('candoo/js/jquery').'/jquery-1.7.2.min.js');
    	    $jQuery->setVersion('1.7.2');
    	    
    	    $jQuery->setUiLocalPath(Candoo_Assets::getURL('candoo/js/jquery.ui').'/jquery-ui-1.8.21.custom.js');
    	    $jQuery->setUiVersion('1.8.21');
    	}
    	// get and set version of CDN library
    	else {
    	    $jQuery->setVersion(ZendX_JQuery::DEFAULT_JQUERY_VERSION);
    	    $jQuery->setUiVersion(ZendX_JQuery::DEFAULT_UI_VERSION);
    	}
    }
    
    public function _initPlugins()
    {
        $config  = $this->_config;
        if (!$config->extension && !$config->extension->plugin) {
            return;
        }
        
        $plugins = $config->extension->plugin;
        if ($plugins) {
            Candoo_App::getInstance()->registerPlugin($plugins->toArray());
        }     
    } 
    
}