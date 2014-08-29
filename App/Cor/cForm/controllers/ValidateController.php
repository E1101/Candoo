<?php
class cForm_ValidateController extends Zend_Controller_Action
{
	public function indexAction()
	{
	    Candoo_Addon_Registry::unregister('candoo', 'Addons_Candoo_DebugInfo_Addon');
	    
	    $this->_helper->viewRenderer->setNoRender();
	    $this->_helper->getHelper('layout')->disableLayout();
	    
	    $request = $this->_request;
	   	    
	    $messages = array();
	    /**
	     * TODO naame form mitavaanad dar address gharaar girad va be injaaa ersaal shavad
	     * /candoo/cForm/validate/[moduleTest_Forms_User_Login]
	     * 
	     * such as:
	     * in decorator
	     * $formClass = $form ->getClass() $view->url('here',array('formClass'=>$formClass));
	     */
	    $form = new moduleTest_Forms_User_Login();
	    if (! $form->isValid($request->getPost()))
	    {
	        $messages = $form->getMessages();
	    }
	    
	    $this->_response
	    	 ->setHeader('Content-type', 'application/json')
	    	 ->appendBody(Zend_Json::encode($messages)); 
	}
}