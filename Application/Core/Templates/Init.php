<?php
class Templates_Init extends Candoo_App_Initalize_Abstract
{
    public function _initTemplate()
    {
        /* plugin e Zend_Layout_Controller_Plugin_Layout ke tavasote Template_Lib_Template::_initPlugin 
         * register mishavad baayd ghabl az plugin e errorHandler baashad pas aval message init shavad
         * 
         */  
        $this->init('Message');

        Templates_Lib_Template::getInstance()->setTemplates(array(
        	'frontend' => $this->getConfig()->getParam('frontend'),
        	'backend'  => $this->getConfig()->getParam('backend'),
        ))->start();
        
        // afzoodan e helper haa va detect kardan e script e samte template
        Candoo_App::getInstance()->registerPlugin(new Templates_Plugins_Scripts());
    }
}