<?php
class cTemplate_Init extends Candoo_App_Initalize_Abstract
{
    public function _initTemplate()
    {
        $config   = $this->getConfig();
        
        // agar template i ta`rif nashode bood ``````````````````````````````````````````````````|
        $area     = (Candoo_App_Resource::get('request')->isOnAdminArea()) ? 'admin' : 'front';
        if (!$config->template && !$config->template->$area) {
        	return ;
        }
        
        /* plugin e Zend_Layout_Controller_Plugin_Layout ke tavasote Template_Lib_Template::_initPlugin 
         * register mishavad baayd ghabl az plugin e errorHandler baashad pas aval message init shavad
         * 
         */  
        $this->init('cMessage');
                
        /* new Zend_Http_UserAgent(array(
        	'wurflapi' => array(
        		'wurfl_api_version' => 1.1,
        		'wurfl_lib_dir'     => APP_DIR_LIBRARIES . "/wurfl-php-1.4.1/WURFL/",
        		'wurfl_config_file' => APP_DIR_LIBRARIES . "/wurfl-php-1.4.1/config.php"
        		),
        	)
        ); */
        
        
        $template = $config ->template->$area;
        // define template for output and starting using mvc on second parameter
        new cTemplate_Lib_Template($template,true); 
    }
}