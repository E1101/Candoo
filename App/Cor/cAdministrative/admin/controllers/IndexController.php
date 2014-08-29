<?php
/**
 * Administrative is default module on admin area
 * and we do`nt need module prefix on each controller
 * ------V-------
 * Administrative_IndexController
 * 
 */
class IndexController extends Zend_Controller_Action
{	
    public function init()
    {
        $this->view->layout()->setLayout('_layout');
        Zend_Debug::dump($this->_request->getParams());
    }
    
	public function indexAction()
	{
	    
	}
	
}