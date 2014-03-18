<?php
/**
 * TODO : class e template haa baayad az yek class e abstract Candoo_template
 * Extend shode baashand.
 * faghat nahveie dastresi be file haa va serv kardane aanhaa fargh mikonad
 * 
 * @author root
 *
 */
class Templates_Lib_Template_Object extends Zend_Layout
{
	const DEFAULT_LAYOUT   = '_layout';
	
	/**
	 * Name of template 
	 * baraaie dastresi be file e config va layout haaa va ... 
	 * dar folder e template haaa bar asaase in name estefaade mishavad
	 * @var string
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
    protected $_pluginClass  = 'Templates_Plugins_Render';
    
    /**
     * simpleXml storage cache
     * 
     * @var SimpleXMLElement
     */
    protected $_simpleXml = null;
    
    public function __construct($templatename)
    {        
        $this->_name = $templatename;
        
        /**
         * @parent
         */
        $this->_initVarContainer();
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
        $this->_initMvc();

        return $this;
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
    
    	Candoo_App::getInstance()->registerPlugin(new $pluginClass($this),$index-10);
    }
    
    /**
     * Template name that class constructed on it
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
    	
	/**
	 * Naame Layout i raa ke baayad baraaie in template dar in darkhaast(request) estefaade shavad
	 * raa bar migardaanad
	 * .
	 */
	public function getLayoutName()
	{
		$request = Candoo_App_Resource::get('request');
	
		// get request Module/Controller/Action + request params `````````````````````````````````````````|
		$data = $request->getParams();
		unset($data['error_handler']);
		/* module/controller/action ro baayad az request bekhaanim
		 * chon agar request az daroon e barnaame avaz shavad yaa error
		 * ettefagh bioftad M/C/A e params maghaadire darkhaaste avalie raa
		 * daaraast
		 */
		$data['module']     = $request->getModuleName();
		$data['controller'] = $request->getControllerName();
		$data['action']     = $request->getActionName();
		// ````````````````````````````````````````````````````````````````````````````````````````````````
		
		// baraaie be dast aavardane LAYOUT,file xml e config e template ro az folder aaan mikhaanam    		    	
    	$xml  = $this->_simpleXml();
    	if (!$xml) {
    		return self::DEFAULT_LAYOUT;
    	}
    	// be te`daade param haaye request majboorim ke query besaazim ke route haai ke shaamel aan param haa hastand moshakhas shavad
    	$count = count($data); $depcValue = array();
    	for ($i=0;$i<$count;$i++) {
    		// make xpath parameter query. @controller='index' and @action='index' and @module='default'
    		$query = '';
    		foreach ($data as $param => $value) {
    			$query .= ($query == '') ? '' : ' and ';
    			$query .= "@{$param}='{$value}'";
    		}

    		// layout haaii raa ke route aanhaa shaamel in attrib haast raa bar migardaanad
    		$list = $xml->xpath("//layouts//layout/route[{$query}]/..");
    		
    		// agar query natije nadaasht param haa ro hazf mikonim
    		if (!is_array($list) && !count($list) > 0 || $list==false) {
    			/**
    			 * TODO : be nazar miaaiad inke az aakhar parameter i raa hazf konim dorost nist
    			 * 		   baayad parameter i hazf shavad ke dar attrib e route haa estefaade nashode
    			 */
    			// sort array, baraaie inke kelid haa be in goone estefaade shavand module,controller,action
    			if (count($data)<=3) {krsort($data);}	
    			foreach ($data as $p=>$v) {
    				if ($p != 'module' && $p != 'controller' && $p != 'action' && count($data)>3) {
    					unset($data[$p]);
    					$depcValue[$p] = $v;
    					break;
    				} else {
    					end($data);
    					$depcValue[key($data)] = array_pop($data);
    					break;
    				}
    			}
    							
				continue;
    		}
    		  		    		
			// $list nabaayad haavie $depcValue baashad, baraaie inke emkaan daarad haalate zir pish aayad
			// {m="default" c="index" } {m="default" c="index" a="test"}, maa dar default/index/index hastim
			// chon xpath darkhaaste module va action ro baraaie attrib mikonad ke dar har do daarim
			// vali har do raa dar $list daarim a="test" ke dar darkhaast nabood raa nemikhaaeem    		
    		foreach ($list as $index=>$layout) {
				$route = (array) $layout->route;
				
				if (is_array($depcValue)) {				
					// route param e attrib e marboot be in darkhaast raa dashte
					$flag = true;
					foreach (array_keys($depcValue) as $k) {
						if (array_key_exists($k, $route['@attributes'])) {
							$flag = false;
							break;
						}
					}
					if ($flag) {		
						return (string) $layout->file;
					}
				}
			}
			
    	}// end for ....................................................................................................................|
    	
    	/* layout e default baraaie in request vojood nadaarad, pas file default bargasht mishavad */
    	$list = $xml->xpath("//layouts//layout[@default='default']");
    	if (is_array($list) && count($list) > 0) {
    		return (string) $list[0] ->file;
    	} else {
    		return self::DEFAULT_LAYOUT;
    	}
	}
	
	/**
	 * Board(region) haaye marboot be template raa bar migardaanad
	 * 
	 * @param string $layout | agar set shode bood board haaye in layout raa bar migardaanad
	 */
	public function getBoardsName($layout=null)
	{
		if ($layout == null) {
		    /**
		     * @FIXME : self::getRequestTemplateLayout mojood nist
		     */
			$layout = self::getRequestTemplateLayout($template);
		}
		    	    	
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
	
	public function getBoardWidgets($board, $layout=null)
	{
		if ($layout == null) {
		    /**
		     * @FIXME : self::getRequestTemplateLayout mojood nist
		     */
			$layout = self::getRequestTemplateLayout($template);
		}
		    	    	
    	$xml  = $this->_simpleXml();
    	if (!$xml) {
    		return array();
    	}
    	$list = $xml->xpath("//layouts//layout/file[contains(text(),'{$layout}')]/../boards/board/name[contains(text(),'{$board}')]/../widget");
    	    	
    	if (!is_array($list) || !@count($list)>0) {
    		return array();
    	}
    	
    	/**
    	 * array(1) {
  			[0] => object(SimpleXMLElement)#63 (2) {
    			["@attributes"] => array(2) {
      				["widget"] => string(19) "Widgets_Html_Widget"
      				["action"] => string(7) "display"
    			}
    			["content"] => object(SimpleXMLElement)#62 (0) {
    			}
  			}
		 }
    	 */
    	$return = array();$widget = array();
    	foreach ($list as $w) {
    		$w = (array) $w ; // to access on @attributes, instead $w->@attributes
    		
    		$attr =  $w['@attributes'];
    		$widget['class'] = $attr['widget'];
    		 unset($attr['widget']);
    		$widget['params'] = $attr;
    		unset($w['@attributes']);
    		// add inner child of widget tag as params
    		foreach ($w as $k => $v) {
    		    $widget['params'][$k] = (string) $v;
    		}
    		array_push($return, $widget);
    	}
    	        	
    	return $return;
	}
	
	
	/**
	 * Az file config theme (about.xml) value e parameter e darkhaast shode
	 * raa bar migardaanad
	 *
	 * TODO: daghighan baraabar ast baa Candoo_Config_Xml va mitavaanad jaaigozin shavad
	 *
	 * @param string $param
	 * @return string
	 */
	public function getParam($param)
	{	 
		$xml  = $this->_simpleXml();
		if (!$xml) {
			return null;
		}
		
		$list = $xml->xpath("//params//param[@name='".$param."']//value");		
		if (!$list) {
			return null;
		}
		 
		/**
		 * Farz bar in ast ke faghat mitavaanad yek value daashte baashim
		 */
		$list = (string) $list[0];
	
		return $list;
	}
	
	public function getLayout()
	{
		/* if ($this->_layout != null) {
			return $this->_layout;
		} */
	
		return $this->getLayoutName();
	}
	
	/**
	 * Url path e in template raa bar migardaanad
	 *
	 * @return string
	 */
	public function getUrl()
	{
		$request = Candoo_App_Resource::get('request');
		return $request->getBasePath().'/Web/Templates/'.$this->getName();
	}
	
	/**
	 * Folder e marboot be zakhire template haa raa bar migardaanad
	 *
	 */
	public function getTemplatesFolderPath()
	{
		return APP_DIR_FRONTEND .DS. 'Templates';
	}
	
	public function getDir()
	{
		return $this->getTemplatesFolderPath().DS.$this->getName();
	}
	
	/**
	 * System folder path e marboot be file config/about e template raa bar migardaanad
	 * @return string | null
	 */
	public function getConfFilepath()
	{
		$xmlFile = $this->getTemplatesFolderPath() .DS. $this->getName() .DS. 'about.xml';
		if (!file_exists($xmlFile)) {
			return null;
		}
	
		return $xmlFile;
	}
	
	protected function _simpleXml()
	{
	    if ($this->_simpleXml == null) {
	        $this->_simpleXml = simplexml_load_file($this->getConfFilepath());
	    }
	    
	    return $this->_simpleXml;
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
        }
        
        $request = Candoo_App_Resource::get('request');
        
        /** Rendering widgets ---------------------------------------------------------------------------| */
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
        			if(isset($wdg['params']['action'])) {
        				$action = $wdg['params']['action'];
        				unset($wdg['params']['action']);
        				$this->$board .= $widget->$action($wdg['params']);
        			} else {
        				// display[Action]() pishfarz ast
        				$this->$board .= $widget->display($wdg['params']);
        			}	
        		} else {
        		    $this->$board .= '<p>Widget <b>'.$class.'</b> not found.</p>';
        		}
        	} // end foreach widgets
        }
        /** ``````````````````````````````````````````````````````````````````````````````````````````````` */
        
        // agar ViewScript path tavasote method set nashode bood folder template raa dar nazar migirad 
        if ($this->getViewScriptPath() == null) {
        	$this->setLayoutPath($this->getDir());
        }
  
        if ($this->inflectorEnabled() && (null !== ($inflector = $this->getInflector())))
        {
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
                
        return $view->render($name);
    }
    
    
    public function getView()
    {
        return Candoo_App_Resource::get('view');
    }
    
}
