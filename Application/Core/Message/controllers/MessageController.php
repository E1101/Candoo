<?php
class Message_MessageController extends Zend_Controller_Action 
{
	public function errorAction()
	{	    
		$request    = $this->getRequest();
		$error 	    = $request->getParam('error_handler');

		 //Agar khataee Rokh nadaade bood :
		if(!$error)	{ return; }
		
		$class 	    = get_class($error->exception);
		
		/**
		 * Log Errors
		 */
		/*$config 	= Flex_Config::getConfig();
        if (isset($config->install->version)) 
        {
			$conn = Flex_Db_Connection::factory()->getConnection();
			$logDao = Flex_Model_Dao_Factory::getInstance()->setModule('core')->getLogDao($conn);
			$logDao->add(new Core_Models_Log(array(
	    		'created_date' 	=> date('Y-m-d H:i:s'),
				'uri'			=> $request->getRequestUri(),
	    		'module'        => ($request->module == null) ? $request->getModuleName() : $request->module,
	    		'controller'    => ($request->controller == null) ? $request->getControllerName() : $request->controller,
	    		'action' 	    => ($request->action == null) ? $request->getActionName() : $request->action,
	    		'class' 	    => $class,
				'file'			=> $error->exception->getFile(),
				'line'			=> $error->exception->getLine(),
	    		'message' 	    => $error->exception->getMessage(),
				'trace'			=> $error->exception->getTraceAsString(),
	        )));
        }*/
        		
		$params = $request->getParams();
		          unset ($params['error_handler']);
 				  $params['request_uri'] = $request->getRequestUri();
        
		switch ($class) 
		{
			case 'Zend_Controller_Router_Exception':
				$this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
				$message = $this->view->translate('Page not found');
				break;
			case 'Zend_Loader_PluginLoader_Exception':
				$message = $this->view->translate('Plugin or View Helper was not found in the registry');
				break;
			/*case 'Zend_Mail_Protocol_Exception':
				$message = $this->view->translate('Could not open socket to send mail, please check ')
				           .'<a href="">'
				           .$this->view->translate('Mail configuration')
				           .'</a>';
				break;*/
			default:
				$message = $error->exception->getMessage();
				break;
		}	
				
		$this->view->assign('error', $error);	
		$this->view->assign('params', $params);	
		$this->view->assign('message', $message);
		$this->view->assign('exception',$error->exception);
		
		/* $config 	= Flex_Config::getConfig();
		$debug 	= (isset($config->web->debug) && 'true' == $config->web->debug) ? true : false;
		$this->view->assign('debug', $debug); */
		
	}
	
	/**
	 * Show offline message
	 */
	/* public function offlineAction() 
	{
		$config  = Flex_Config::getConfig();
		
		$message = $config ->web->offline->message;
		
		$this->view->assign('message', $message);
	} */
}
