<?php
class Helpers_Action_Csrf extends Zend_Controller_Action_Helper_Abstract
{
	protected $_salt = 'salt';
	
	protected $_name = 'csrf_control_element';
	
	protected $_timeout = 300;
	
	protected $_session = null;
	
	protected $_csrfEnable = false;
	
	protected $_token;

	
	public function init()
	{
		/**
		 * Agar "Error" rokh daade bood az ejraaie edaameie script khod daari mikonad
		 */
		$request      = $this->getRequest();
		$errorHandler = $request->getParam('error_handler'); 
		if ($errorHandler && $errorHandler->exception) 
		{
			return;
		}
		
		$router = $this->getFrontController()->getRouter();
		$route  = $router->getCurrentRoute();
		
		if ($route instanceof Zend_Controller_Router_Route_Chain) 
		{
			return;	
		}
		
		/**
		 * Yek array bar migardaanad shaamel :
		 * 'module'
		 * 'controller'
		 * 'action'
		 * agar ehtiaaj daashte baashim ke parameter "csrf" ham set konim
		 * dar router ezaafe mishavad.
		 * 
		 * dar barnaameie maa in tanzim dar file .ini router haa
		 * be in sourat baraaie safahaati ke ehtiaaj be csrf daarad set 
		 * mishavad :
		 * routes.[page].defaults.enable = "true"
		 * 
		 * @var unknown_type
		 */
		$defaults = $route->getDefaults();
		// agar parameter haaie "config csrf" be safhe ersaal shode bood		
		if (isset($defaults['csrf']) && (string)$defaults['csrf'] == 'enable' ) 
		{
			$this->_csrfEnable = true;
		}
	}
	
	public function preDispatch()
	{
	    Zend_Debug::dump(__CLASS__.':'.__FUNCTION__);
		if ($this->_csrfEnable) 
		{
			$session = $this->_getSession();
	        $session->setExpirationSeconds($this->_timeout);
	        
	        $this->_token   = $session->token;
	        $session->token = $this->_generateToken();
	        
	        /**
	         * agar az form haa chizi be safhe ersaal shode bood
	         * @var unknown_type
	         */
			$request = $this->getRequest();
			$isValid = null;
			if ( $request->isPost() || ($request->isGet() && !empty($_GET)) ) 
			{
				/**
				 * search "field token" dar $_POST va $_GET va khaandane meghdaare aan
				 */
				$values = ($request->isPost())
				        ?  $request->getPost()
				        :  $request->getQuery();
				
				if ( key_exists($this->getTokenName(),$values) )
				{
					$token = $values[$this->getTokenName()];
					$isValid = $this->isValidToken($token);
				}
				
			}
			
			if ($isValid === false) 
			{
				throw new Zend_Exception('Token does not match');
			}
		}
	}
	
	public function postDispatch()
	{
	    Zend_Debug::dump(__CLASS__.':'.__FUNCTION__);
		if ($this->_csrfEnable) 
		{
			$element = sprintf('<input type="hidden" name="%s" value="%s" />',
				$this->_name,
				$this->getToken()
			);
			$this->getActionController()->view->assign('tokenElement', $element);
		}
	}
	
	public function getTokenName() 
	{
		return $this->_name;	
	}
		
	public function isValidToken($token)
	{
		if (null == $token || '' == $token) 
		{
			return false;
		}
		return ($token == $this->_token);
	}
	
	public function getToken()
	{
		$session = $this->_getSession();
		if (!isset($session->token)) {
			/**
			 * We need to regenerate token
			 */
	        $session->token = $this->_generateToken();
		}
		return $session->token;
	}	
	
	private function _getSession()
	{
		if ($this->_session == null) 
		{
			$this->_session = new Zend_Session_Namespace($this->_getSessionName());
		}
		return $this->_session;
	}
	
	private function _getSessionName() 
	{
		return __CLASS__ . $this->_salt . $this->_name;
	}
	
	/**
	 * @return string
	 */
	private function _generateToken()
	{
		$token = md5(
            mt_rand(1, 1000000)
            .  $this->_salt
            .  $this->_name
            .  mt_rand(1, 1000000)
        );
        return $token;
	}
}

