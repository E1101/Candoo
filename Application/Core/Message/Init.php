<?php
class Message_Init extends Candoo_App_Initalize_Abstract
{
    public function _initErrorHandler()
    {        
    	// register to run last
    	Candoo_App::getInstance()->registerPlugin(
    			new Zend_Controller_Plugin_ErrorHandler(array(
    					'module' 	 => 'Message',
    					'controller' => 'message',
    					'action'     => 'error',
    			)),1000
    	);
    }
}