<?php
class moduleTest_IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{	    
	    Zend_Debug::dump($this->view->url('moduleTest>index>index'));
	    
	    //Zend_Debug::dump($this->_request->getParams());
	    
	    // this is a way to get backend or other areas object
	    //echo 'from: '.__CLASS__.'->'.__FUNCTION__.'<br/>';
	    //echo 'You use <b>'.$this->view->layout()->getArea('backend')->getName().'</b> as Admin template.';
	    
		//new moduleTest_Models_Test();
	}
}