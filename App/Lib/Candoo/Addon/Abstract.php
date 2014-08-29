<?php
class Candoo_Addon_Abstract extends Candoo_Dataset_Entity
{
    /**
     * Agar dar yek ejraa be dalile manteghi mikhaastim ke masaln action render
     * nashavad in meghdaar raa az daroon e action return mikonim 
     * exp. return self::BREAK_ACTION
     * 
     */
    const BREAK_ACTION = 'BREAK_ACTION_LOGICALY';
    
	/**
	 * Yek ID uniq bar asaase time va naame addon tolid mikonad
	 * ke dar zamaani ke ehtiaaj be ID daarim, masalan dar script 
	 * haaie jquery az in estefaade mikonim
	 * 
	 * @var string
	 */
	protected $_uniqID;
	
	/**
	 * Name of addOn
	 * 
	 * @var string
	 */
	protected $_name;
	
	
	/**
	 * @var Zend_Controller_Request_Abstract
	 */
	protected $_request;
	
	/**
	 * @var Zend_Controller_Response_Abstract
	 */
	protected $_response;
	
	/**
	 * @var Zend_View_Abstract
	 */
	protected $_view;

	/**
	 * Name of viewScript file to render for each actions method
	 * 
	 * @var string
	 */
	protected $_layout;
		
	/**
	 * Shayad yek Action ehtiaj be render e script nadaashte baashad
	 * 
	 * @var boolean
	 */
	protected $_noRender = false;
	
	/**
	 * Yek flag baraaie test kardan e inke file haaie translate e addOn 
	 * be translate helper add shode yaaa na
	 * 
	 * @var boolean
	 */
	protected $_translatorized = false;
	
	public function __construct($data = null)
	{
	    // setParams
	    if (! $data || empty($data)) { 
	        // hengaame __construct config raa az file migirad
	        $data = array();
	        
	        $file = $this->getConfigFilepath();
	        if ($file) {
	            $data = Candoo_Config::getConfig($file);
	            if ($data) {
	            	$data = $data->toArray();
	            }    
	        }
	    }
	    
	    parent::__construct($data);
	    
		$front 	  = Candoo_App_Resource::get('frontController');
		
		$request  = Candoo_App_Resource::get('request');
		// ma mikhaahim tamaami e parameter haaye request ham dar widget daashte baashim exp. lang	
		$this->_request  = clone $request;
		$this->_response = new Zend_Controller_Response_Http();
		$this->_view     = $this->getView();
		
		
		// addTranslation files to translator view helper ``````````````````````````````````````````
		if (!$this->_translatorized) {
		    $filepath = $this->getDirRoot().DS.'Langs';
		    if (is_dir($filepath)) {
		    	// baraaie inke helper e translate faghat yek baar ->_translator raa set
		    	// mikonad va dar darkhaast haaie ba`d az aan estefaade mikonad
		    	// baraaie hamin az Candoo_App_Resource estefaade nakardam
		    	$this->_view->translate()->getTranslator()->addTranslation($filepath);
		    }
		}
		//`````````````````````````````````````````````````````````````````````````````````````````
		
		$this->init();
	}
	
	public function getConfDirPath()
	{
	    return $this->getDirRoot().DS.'Conf';
	}
	
	/**
	 * Masir e file e marboot be configuration raa bar migardaanad agar mojood nabood false
	 */
	public function getConfigFilepath()
	{
		$host    = Candoo_Site::getSite();
	
		$filepath = $this->getConfDirPath() .DS. 'conf.' .$host. '.ini';
		$file 	  = file_exists($filepath) ? $filepath : false;
	
		return $file;
	}
	
    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init() { }
    
	/**
	 * exp: ('show',array('question_id'=>5,'container'=>'pollContainer'))
	 * @param string $act
	 * @param string $arguments
	 */
	public function __call($act,$arg)
	{
		$result = null;
			
		// ejraaie action e widget
		$methodAction = $act.'Action';
		if (method_exists($this, $methodAction)) { 
			// reset class 
			$this->_reset();

			// because of : run each method with own argument
			/* 
			// set params for this action
			if ($arg != null && is_array($arg) && count($arg) > 0) {
				$this->setParams($arg[0]);
			} */
			
			try {
			    // run each method with own argument
			    /* $result = $this->$methodAction(); */
			    $result = call_user_func_array(array($this,$methodAction), $arg);
				// agar dar hengaame ejraaie action error e manteghi daashtim script view render nemishavad
				if ($result !== self::BREAK_ACTION ) {
				    // agar gharaar nabood script i render shavad meghdaar e baazgashti action raa bar migardaanad
				    if ($this->_noRender) 
				    {
				        return $result;
				    }
				    else 
				    {
				        // agar az action chizi return shode bood, dar haali ke noRender ham nistim 
				        // be ebtedaaie khorooji ezaafe mishavad
				        if ($result) {
				            $this->getResponse()->appendBody($result);
				        }
				        
				        // exp: rendering displayAction view
				        return $this->render($act);
				    }
				}
			} catch (Exception $e) {
				return 'Addon: '.$this->getName().' Error running method '.$methodAction.' >> '.$e ->getMessage();
			}
		} else {
			return 'Addon: '.$this->getName().' Method '.$methodAction.' not found.';
		}
		
		return $this;
	}
	
	private function _reset()
	{
		/*$params = $this->_request->getUserParams();
		 foreach (array_keys($params) as $key) {
		$this->_request->setParam($key, null);
		}*/
		 
		// there is no need to reset global params, each method run with own parameters
		/* $this->_properties = array(); */
	
		// farz barin ast ke har action yek script raa render mikonad
		$this->setNoRender(false);
	
		$this->getResponse()->clearBody();
		$this->getResponse()->clearHeaders()->clearRawHeaders();
	}
	
	/**
	 * Pas az inke $this->method tavasote __call
	 * ejraa shod in method vazifeie render e .phtml script
	 * raa daarad
	 * 
	 * @param string $call
	 */
	private function render($action)
	{
		// no render script if noRender set to true
		if ($this->getNoRender()) {
			return ;
		}
		
		// old view settings
		$viewScriptPath = $this->getView()->getScriptPaths();
		$viewHelperPath = $this->getView()->getHelperPaths();
								
			// get viewScript path, render .phtml file and append to response
			$script = $this->getViewScript($action);		
			$this->getResponse()->appendBody($this->getView()->render($script));
		
		// return view to previous setting
		// baraaie inke tartib scriptPath haaa be soorat e ghabli hefz shavad array_reverse
		$this->getView()->setScriptPath(array_reverse($viewScriptPath));
		
		$this->getView()->setHelperPath(null);
		foreach ($viewHelperPath as $prefix => $val) {
			$this->getView()->addHelperPath($val,$prefix);
		}
		
		return $this->getResponse()->getBody();
	}
		
	/**
	 * Bar asaase har method call viewScript e aan raa bar migardaanad
	 * Hamchenin helper haa ra baraaie render be view moarefi mikonad
	 * 
	 * @param string $call
	 */
	protected function getViewScript($call)
	{
	    // moshakhas mikonad ke addon marboot be kodaam module ast, yaa inke addon e mostaghel ast
		$depName  = $this->getDepenName();
		$ownName  = $this->getClassName();
		
		// add addOn HelperPath ``````````````````````````````````````````````````````````````````````````````|
		$helperPath   = $this->getDirRoot().DS.'Helpers';
			
		$cnameExp = explode('_', $this->getClassName());
		// akharin ghesmat postfix class haaie addon ast ke kenaar gozaashte mishavad
		// Addons_AddonGroup_Test_Addon
		//                        -----
		array_pop($cnameExp);
		
		$helperPrefix = implode('_', $cnameExp).'_Helpers_';
		
		$this->getView()->addHelperPath($helperPath,$helperPrefix);
		
		// looking on the template folder ````````````````````````````````````````````````````````````````````|
		$viewSuffix = '.'.Candoo_App_Resource::get('viewRenderer')->getViewSuffix();
		
		if (Candoo_App_Resource::isRegistered('template')) {
		    $scriptPath = Candoo_App_Resource::get('template')->getDir() .DS. 'views' .DS. 'Addons'
		    .DS. implode(DS, $cnameExp) .DS. $call;
		    
		    $scriptFile = $this->getLayout().$viewSuffix;
		
		    if (file_exists($scriptPath.DS.$scriptFile)) {
		    	$this->getView()->addScriptPath($scriptPath);
		    	return $scriptFile;
		    }
		}
		
		// looking for own directory of widget ``````````````````````````````````````````````````````````````|
		$scriptPath = $this->getDirRoot().DS.'views'.DS.$call;
		$scriptFile = $this->getLayout().$viewSuffix;
		if (file_exists($scriptPath.DS.$scriptFile)) {
			$this->getView()->addScriptPath($scriptPath);
			return $scriptFile;
		}
	}
	
	/* public function __toString()
	{
		return $this->getResponse()->getBody();
	} */
	
		
	public function setNoRender($bool = true)
	{
		$this->_noRender = $bool;
	}
	
	public function getNoRender()
	{
		return ($this->_noRender == true) ? true : false;
	}
	
	public function setLayout($name)
	{
		$this->_layout = $name;
	}
	
	/**
	 * naame layout (.phtml) file i ke baayad render shavad
	 */
	public function getLayout()
	{
		if ($this->_layout == null) {
			$this->setLayout('default');
		} 
		
		return $this->_layout;
	}
	
    /**
     * Return the Request object
     *
     */
    public function getRequest()
    {
        return $this->_request;
    }
        
    /**
     * Return the Response object
     *
     * @return Zend_Controller_Response_Abstract
     */
    public function getResponse()
    {
        return $this->_response;
    }
   
    /**
     * Get a helper by name
     *
     * @param  string $helperName
     * @return Zend_Controller_Action_Helper_Abstract
     */
    public function getHelper($helperName)
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper($helperName);
    }

    /**
     * Get a clone of a helper by name
     *
     * @param  string $helperName
     * @return Zend_Controller_Action_Helper_Abstract
     */
    public function getHelperCopy($helperName)
    {
        return clone Zend_Controller_Action_HelperBroker::getStaticHelper($helperName);
    }
	
	public function getView()
	{
		if (!$this->_view) {
			$view = Candoo_App_Resource::get('view');
	
			$this->_view = &$view;
		}
	
		return $this->_view;
	}
	
	
	
	public function getClassName()
	{	    
	    $class = new Zend_Reflection_Class($this);
	    // Addons_AddonGroup_Test_Addon
	    return $class->getShortName();
	}
	
	/**
	 * Masire directory root e Addon raa bar migardaanad
	 * exp. /opt/lampp/htdocs/candoo/Application/Extension/Addons/AddonGroup/Test
	 */
	public function getDirRoot()
	{
	    return pathinfo($this->getDeclaringFile()->getFileName(),PATHINFO_DIRNAME);
	}
	
	/**
	 * get file of class
	 */
	protected function getDeclaringFile()
	{
		$class = new Zend_Reflection_Class($this);
	
		$name =  $class->getDeclaringFile();
	
		return $name;
	}
	
	/**
	 * return name of addon
	 */
	/* public function getName()
	{
		if ($this->_name == null) {
			$class = new Zend_Reflection_Class($this);
				
			// Widget_StaticHtml_Widget , Users_Login_Widget
			//        ----------                -----
			$name =  explode('_', $class->getName());
			$name = $name[count($name)-2];
				
			$this->_name = $name;
		}
	
		return $this->_name;
	} */
	
	/**
	 * Moshakhas mikonad ke in widget mota`alegh be kodaam module ast?
	 * va yaa inke yek widget e mostaghel ast ke dar folder Widget gharaar 
	 * daarad
	 * 
	 * baraaie render kardan va yaaftan e viewscript dar template fe`lan 
	 * morede estefaade ast
	 * 
	 */
	protected function getDepenName()
	{
		// Addons_StaticHtml_Addon , Users_Addon_Login_Addon
		// ------                    -----
		$class = new Zend_Reflection_Class($this);
		
		return current(explode('_',$class->getName()));
	}
	
	/**
	 * return current widget ID
	 *
	 */
	public function getId()
	{
		if ($this->_uniqID == null) {
			$this->resetId();
		}
	
		return $this->_uniqID;
	}
	
	/**
	 * Yek Id Baraaie in widget dar nazar migirad in ID 
	 * mitavaanad dar script haa ie mesle jquery estefaade shavad
	 * @param string $id
	 */
	public function resetId(string $id=null)
	{
		if ($id==null) {
			$id = $this->_generateID();
		}
		
		$this->_uniqID = $id;
		
		return $this;
	}
	
	/**
	 * generate a unique id for widget
	 * 
	 */
	protected function _generateID()
	{
		$prefix = 'widget_'.strtolower($this->getName()).'_';
		return uniqid($prefix);
	}
	
	
}