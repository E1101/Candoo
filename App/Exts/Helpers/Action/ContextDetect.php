<?php
class Helpers_Action_ContextDetect extends Zend_Controller_Action_Helper_Abstract
{
    public function preDispatch()
    {   
    	/* $request = $this->getRequest();
    	
    	$contextSwitch = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch');
    	// layout baayad az resource e Candoo_App morede dastresi gharaar girad
    	// contextSwitch az Zend_Layout estefaade mikonad
    	// i have to manualy disable layout 
    	$contextSwitch ->setAutoDisableLayout(false);
    	
    	if ($request->isXmlHttpRequest()) {
    		$request->setParam('format','ajax');
    	}
    	
    	if (in_array($request->getParam('format'),array('ajax','json')) ) {
    	    Candoo_App_Resource::get('template')->disableLayout();
    	    
    	}
    	    	
    	$contextSwitch ->setActionController($this->getActionController());
    
    	if (! $contextSwitch->hasContext('ajax')) {
    		$contextSwitch  ->addContext('ajax', array('suffix' => 'ajax'));
    	}
    
    	$contextSwitch ->addActionContext(
    		$request->getActionName(), 
    		array('xml', 'json','ajax')
    	)
    	->initContext(); */
    }
}

