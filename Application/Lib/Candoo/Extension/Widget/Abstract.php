<?php
/**
 * TODO: Har widget mitavaanad script haaaie khod raa daashte baashad, va yaa class haaye marboot be khod
 * be jaaie inke in script haa va yaa class haa dar view ta`rif shavad dar har action e aaan ezaafe
 * shavad, vali emkaaan e disable shodan raaa daashte baashand
 * 
 * @author root
 */
class Candoo_Extension_Widget_Abstract extends Candoo_Dataset_Entity
{	
	/**
	 * Yek ID uniq bar asaase time va naame widget tolid mikonad
	 * ke dar zamaani ke ehtiaaj be ID daarim, masalan dar script 
	 * haaie jquery az in estefaade mikonim
	 * 
	 * @var string
	 */
	protected $_uniqID;
	
	/**
	 * Name of widget
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
	 * Yek flag baraaie test kardan e inke file haaie translate e widget 
	 * be translate helper add shode yaaa na
	 * 
	 * @var boolean
	 */
	protected $_translatorized = false;
	
	public function __construct()
	{
		$front 	  = Candoo_App_Resource::get('frontController');
		
		$request  = Candoo_App_Resource::get('request');
		// ma mikhaahim tamaami e parameter haaye request ham dar widget daashte baashim exp. lang	
		$this->_request  = clone $request;
		$this->_response = new Zend_Controller_Response_Http();
		$this->_view     = $this->getView();
		
		
		// addTranslation files to translator view helper ``````````````````````````````````````````
		if (!$this->_translatorized) {
		    $filepath = pathinfo($this->getDeclaringFile()->getFileName(),PATHINFO_DIRNAME).DS.'Langs';
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
		$methodAction = strtolower($act).'Action';
		if (method_exists($this, $methodAction)) {
			// reset class 
			$this->_reset();
						
			// set params for this action
			if ($arg != null && is_array($arg) && count($arg) > 0) {
				$this->setParams($arg[0]);
			}
			
			try {
			    $result = $this->$methodAction();
				// agar dar hengaame ejraaie action error e manteghi daashtim script view render nemishavad
				if ($result !== false ) {
					// store return value from action to response
					$this->getResponse()->appendBody($result);
				
					// exp: rendering displayAction view
					$this->render($act);
				}
			} catch (Exception $e) {
				return 'Widget: '.$this->getName().' Error running method '.$methodAction.' >> '.$e ->getMessage();
			}
		} else {
			return 'Widget: '.$this->getName().' Method '.$methodAction.' not found.';
		}
		
		return $this;
	}
	
	/**
	 * Pas az inke $this->method tavasote __call
	 * ejraa shod in method vazifeie render e .phtml script
	 * raa daarad
	 * 
	 * @param string $call
	 */
	protected function render($action)
	{
		// no render script if noRender set to true
		if ($this->getNoRender()) {
			return;
		}
		
		// old view settings
		$viewScriptPath = $this->getView()->getScriptPaths();
		$viewHelperPath = $this->getView()->getHelperPaths();
								
			// get viewScript path, render .phtml file and append to response
			$script = $this->getViewScript($action);		
			$this->getResponse()->appendBody($this->getView()->render($script));
		
		// return view to previous setting
		$this->getView()->setScriptPath($viewScriptPath);
		$this->getView()->setHelperPath(null);
		foreach ($viewHelperPath as $prefix => $val) {
			$this->getView()->addHelperPath($val,$prefix);
		}
	}
		
	/**
	 * Bar asaase har method call viewScript e aan raa bar migardaanad
	 * 
	 * @param string $call
	 */
	protected function getViewScript($call)
	{
	    // moshakhas mikonad ke widget marboot be kodaam module ast, yaa inke widget e mostaghel ast
		$depName  = $this->getDependent();
		$widName  = $this->getName();
		
		// add widget HelperPath ``````````````````````````````````````````````````````````````````````````````|
		$helperPath   = pathinfo($this->getDeclaringFile()->getFileName(),PATHINFO_DIRNAME).DS.'Helpers';
		
		$helperPrefix = explode('_', $this->getDeclaringFile()->getClass()->name);
		array_pop($helperPrefix);
		$helperPrefix = implode('_', $helperPrefix).'_Helpers_';
		
		$this->getView()->addHelperPath($helperPath,$helperPrefix);
		
		// looking on the template folder ````````````````````````````````````````````````````````````````````|
		$viewSuffix = '.'.Candoo_App_Resource::get('viewRenderer')->getViewSuffix();
		
		$scriptPath = Candoo_App_Resource::get('template') ->getDir()
					  .DS. 'Widgets' .DS. $depName .DS. $widName .DS. $call;
		$scriptFile = $this->getLayout().$viewSuffix;
		if (file_exists($scriptPath.DS.$scriptFile)) {		    
			$this->getView()->addScriptPath($scriptPath);
			return $scriptFile;
		}
		
		// looking for own directory of widget ``````````````````````````````````````````````````````````````|
		$scriptPath = pathinfo($this->getDeclaringFile()->getFileName(),PATHINFO_DIRNAME).DS.$call.DS;
		$scriptFile = $this->getLayout().$viewSuffix;
		if (file_exists($scriptPath.DS.$scriptFile)) {
			$this->getView()->addScriptPath($scriptPath);
			return $scriptFile;
		}	
	}
	
	public function __toString()
	{
		return $this->getResponse()->getBody();
	}
	
	private function _reset()
	{
		/*$params = $this->_request->getUserParams();
		 foreach (array_keys($params) as $key) {
		$this->_request->setParam($key, null);
		}*/
	    
	    $this->_properties = array();
	
		// farz barin ast ke har action yek script raa render mikonad
		$this->setNoRender(false);
	
		$this->getResponse()->clearBody();
		$this->getResponse()->clearHeaders()->clearRawHeaders();
	}
		
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
	
	/**
	 * return name of widget
	 */
	public function getName()
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
	}
	
	/**
	 * get filepath of class
	 */
	protected function getDeclaringFile()
	{
		$class = new Zend_Reflection_Class($this);
		
		$name =  $class->getDeclaringFile();

		return $name;
	}
	
	/**
	 * Moshakhas mikonad ke in widget mota`alegh be kodaam module ast?
	 * va yaa inke yek widget e mostaghel ast ke dar folder Widget gharaar 
	 * daarad
	 * 
	 * baraaie render kardan va yaaftan e viewscript dar template fe`lan 
	 * morede estefaade ast
	 * 
	 */
	protected function getDependent()
	{
		// Widget_StaticHtml_Widget , Users_Login_Widget
		// ------                     -----
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