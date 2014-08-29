<?php
/**
 * TODO : class e template haa baayad az yek class e abstract Candoo_template
 * Extend shode baashand.
 * faghat nahveie dastresi be file haa va serv kardane aanhaa fargh mikonad
 * 
 * @author root
 *
 */
class cTemplate_Lib_Template extends Zend_Layout
{
    /**
     * get name of used template
     * 
     */
    protected $_name;
    
    /**
     * Layout view
     * @var string
     */
    protected $_layout;
		
    /**
     * Plugin class
     * @var string
     */
    protected $_pluginClass  = 'cTemplate_Plugins_Render';
    
    /**
     * simpleXml storage cache
     * 
     * @var SimpleXMLElement
     */
    protected $_simpleXml = null;
    
    public function __construct($templatename, $initMvc = false)
    {
        $temDir = $this->getTemplateFolderPath().DS.$templatename;
        if (! is_dir($temDir)) {
        	throw new Exception('Template '.$templatename.' not found in:'.$temDir);    
        }
        
        $this->setTemplate($templatename);
        
        /**
         * @parent
         */
        $this->_initVarContainer();
        
        if ($initMvc) {
        	$this->enableMVC();
        } else {
        	$this->_setMvcEnabled(false);
        }
    }
	
    /**
     * Static method for initialization with MVC support
     *
     * @param  string|array|Zend_Config $options
     * @return Zend_Layout
     * @note be khaatere new self majboor be in shodam ke in method raa injaa gharaar
     * 		 daham ke instance e in class raa bar gardaanad
     */
    public function enableMVC()
    {
        $this->_setMvcEnabled(true);
        $this->_initPlugin();
        $this->_initHelper();
        
        $this->_registerInstance();

        return $this;
    }
    
    /**
     * Register layout object to resources that can instancing from layout() helper
     */
    protected function _registerInstance()
    {
        // store template resource to use from other classes
        Candoo_App_Resource::set('template', $this);
    }
    
    protected function _initHelper()
    {
        // afzoodan e helper haa va detect kardan e script e samte template
        // az in tarigh mitavaan khorooji haaie pish farz raa dar template avaz kard
        Zend_Controller_Action_HelperBroker::getStack()->offsetSet(-95, new cTemplate_Helpers_Action_Scripts());
        
        return parent::_initHelper();
    }
    
    /**
     * @overwriten parent
     *
     * Initialize front controller plugin
     *
     * @return void
     */
    protected function _initPlugin()
    {
    	$pluginClass = $this->getPluginClass();
    
    	// register to run last | BUT before the ErrorHandler (if its available)
    	$index = Candoo_App::getPluginIndex('Zend_Controller_Plugin_ErrorHandler');
    	if ($index === false) {
    		$index = Candoo_App::getLastPluginIndex();
    	}
    	
    	Candoo_App::getInstance()->registerPlugin(new $pluginClass($this),$index-5);
    }
    
    /**
     * Template name that class constructed on it
     * 
     * @return string
     */
    public function getTemplate()
    {
        return $this->_name;
    }
    
    public function setTemplate($name)
    {
        $this->_name = $name;
                
        /** Ezaafe kardane helper haaaye morede estefade dar template */
        // be in soorat pas az ta`ghir e template helper haaye aan ezafe mishavad
        $view = $this->getView();
        
        $helperPrefix = $this->getTemplate().'_Helpers_';
        if ( false === $view->getPluginLoader('helper')->getPaths($helperPrefix) ) {
             $view ->addHelperPath(
            		$this->getDir().DS.'views'.DS.'helpers',
            		$helperPrefix
             );
        }
    }
	
	/**
	 * Board(region) haaye marboot be template raa bar migardaanad
	 * 
	 * @param string $layout | agar set shode bood board haaye in layout raa bar migardaanad
	 */
	public function getBoardsName()
	{    	    	
    	$xml  = $this->_simpleXml();
    	if (!$xml) {
    		return array();
    	}
    	
    	$list = $xml->xpath("//layouts//layout/file[contains(text(),'{$layout}')]/../boards");
    	if (!is_array($list) || !@count($list)>0) {
    		return array();
    	}
    	
    	$return = array();
    	foreach ($list[0] as $board) {
    		$return[] = (string) $board ->name;
    	}
    	
    	return $return;
	}
	
	
	/**
	 * Masir e file e marboot be configuration raa bar migardaanad agar mojood nabood false
	 */
	public function getConfigFilepath()
	{
		$host     = Candoo_App_Resource::get('request')->getSitename();
		$filepath = $this->getDir() .DS. 'Conf' .DS. 'conf.' .$host. '.ini';
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
	public function getConfig()
	{
		$file = self::getConfigFilepath();
		 
		return Candoo_Config::getConfig($file);
	}
	
	/**
	 * Object e config e marboot be template raaa baraaie dastresi be motaghaieraat e aan
	 * bar migardaanad
	 */
	public function getParams()
	{
	    return $this->getConfig()->params;
	}
	
	
	/**
	 * Folder e marboot be zakhire template haa raa bar migardaanad
	 *
	 */
	public function getTemplateFolderPath()
	{
		return APP_DIR_FRONTEND .DS. 'Templates';
	}
		
	/**
	 * Url path e in template raa bar migardaanad
	 *
	 * @return string
	 */
	public function getUrl()
	{
	    // build relative url from root /[seg1]/[seg2]
		return Candoo_Uri::build(array(
			Candoo_App::getBasePath(),
			APP_FRONTEND,
			'Templates',
			$this->getTemplate()
		));
	}
	
	public function getDir()
	{
		return $this->getTemplateFolderPath().DS.$this->getTemplate();
	}
	
	/**
	 * System folder path e marboot be file config/about e template raa bar migardaanad
	 * @return string | null
	 */
	public function getConfFilepath()
	{
		$xmlFile = $this->getTemplateFolderPath() .DS. $this->getTemplate() .DS. 'about.xml';
		if (!file_exists($xmlFile)) {
			return null;
		}
	
		return $xmlFile;
	}
	
	public function getLayout()
	{
	    // get default layout from xmltemplate config file
	    if ( null === $this->_layout ) {
	        $xml  = $this->_simpleXml();
	        
	        $list = $xml->xpath("//layouts//layout[@default='default']/file"); 
	        if (!is_array($list) || !@count($list)>0) {
	        	throw new Zend_Exception('Default layout not defined in template config file.');
	        }
	        
	        $this->_layout = (string) $list[0];
	    }
	    
	    return $this->_layout;
	}
	
    /**
     * Render layout
     *
     * Sets internal script path as last path on script path stack, assigns
     * layout variables to view, determines layout name using inflector, and
     * renders layout view script.
     *
     * $name will be passed to the inflector as the key 'script'.
     *
     * @param  mixed $name
     * @return mixed
     */
    public function render($name = null)
    {
        if (null === $name) {
            $name = $this->getLayout();
            
            if ($name == null) {
                throw new Exception('Layout not defined in '.__CLASS__);
            }
        }
        
    	
        
        /* Widget rendering must be moved out of there,
         * masalan mitoone be page bere va dar oonjaa baraaie har page address widget haa load misheh
         
        // get Layout boards from template config and layout section
        $layScript  = $name;
        
        $layoutBoards = $this->getBoardsName($layScript);
        // get widgets on each board
        foreach ($layoutBoards as $board) {
        	$widgets = $this->getBoardWidgets($board,$layScript);
        	        	 
        	// new widgets(params) and render to template board
        	foreach ($widgets as $wdg) {
        	    // test widget route against current route 
        	    if (isset($wdg['params']['_route'])) {
        	        $route = explode(':', $wdg['params']['_route']);
        	        unset($wdg['params']['_route']);
        	        
        	        // check index[0] for ! sign
        	        $sign = true;
        	        if (substr($route[0], 0,1) == '!') {
        	            $route[0] = str_replace('!', '', $route[0]);
        	            $sign = false;
        	        }
        	        
        	        $mca   = array($request->getModuleName(), $request->getControllerName(), $request->getActionName());
        	        $isOnRoute = true;
        	        for ($i=0;$i<count($route);$i++) {        	            
        	            $isOnRoute = ($route[$i] == $mca[$i]) && $isOnRoute;
        	        }
        	        
        	        if (! (!$sign XOR $isOnRoute)) {
        	            continue;
        	        }
        	    }
        	    
        		// shayad class widget rooie system mojood nabaashad
        		$class = $wdg['class'];
        		if (class_exists($class,true)) {
        			// converted to __toString
        			$widget = new $class();
        			if(isset($wdg['params']['layout'])) {
        				$widget->setLayout($wdg['params']['layout']);
        				unset($wdg['params']['layout']);
        			}
        			
        			$widget->setParams($wdg['params']);
        			
        			if(isset($wdg['params']['action'])) {
        				$action = $wdg['params']['action'];
        				unset($wdg['params']['action']);
        				$this->$board .= $widget->$action();
        			} else {
        				// display[Action]() pishfarz ast
        				$this->$board .= $widget->display();
        			}	
        		} else {
        		    $this->$board .= '<p>Widget <b>'.$class.'</b> not found.</p>';
        		}
        	} // end foreach widgets
        	
        }*/
        
        // agar ViewScript path tavasote method set nashode bood folder template raa dar nazar migirad 
        if ($this->getViewScriptPath() == null) {
        	$this->setLayoutPath($this->getDir());
        }
  
        if ($this->inflectorEnabled() && (null !== ($inflector = $this->getInflector()))) {
            $name = $this->_inflector->filter(array('script' => $name));
        }

        $view = $this->getView();
        if (null !== ($path = $this->getViewScriptPath())) {        	
            if (method_exists($view, 'addScriptPath')) {
                $view->addScriptPath($path);
            } else {
                $view->setScriptPath($path);
            }
        } elseif (null !== ($path = $this->getViewBasePath())) {
            $view->addBasePath($path, $this->_viewBasePrefix);
        }
        
        try 
        {
            $templateRender = $view->render($name);
        }
        catch(Exception $e) 
        {
        	throw new Zend_Exception('Error rendering template layout "'.$name.'"'.'<br/>'.$e->getMessage());    
        }
        
                
        return $templateRender;
    }
    
    public function getView()
    {
        return Candoo_App_Resource::get('view');
    }
    
    
    protected function _simpleXml()
    {
    	if ($this->_simpleXml == null) {
    	    set_error_handler(array($this, '_loadFileErrorHandler')); // Warnings and errors are suppressed
    		$this->_simpleXml = simplexml_load_file($this->getConfFilepath());
    		restore_error_handler();
    	}
    
    	return $this->_simpleXml;
    }
    /**
     * Handle any errors from simplexml_load_file
     *
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     */
    protected function _loadFileErrorHandler($errno, $errstr, $errfile, $errline)
    {
    	throw new Zend_Exception($errstr, $errno);
    }
    
}
