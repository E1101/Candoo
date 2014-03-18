<?php
class Core_Init extends Candoo_App_Initalize_Abstract
{
    public function _initView()
    {
        $view = Candoo_App_Resource::get('view');
        
        /**
         * Add view helpers ```````````````````````````````````````````````````````````````````````````````````|
         */  
        $view->addHelperPath(APP_DIR_EXTENSION .DS. 'Helpers' , 'Extension_Helpers_');
        
         // Helper haaie Module haaie core ghaabeliat e dastresi daashte baashand
        $coreModules = Candoo_Extension_Module::getCoreModules();
        foreach ($coreModules as $module) {
        	$view->addHelperPath(APP_DIR_CORE .DS. $module .DS. 'Helpers' , $module.'_Helpers_');
        }
        /** ````````````````````````````````````````````````````````````````````````````````````````````````` */

        $config = Candoo_Config::getInstance();
        
        $view->headTitle ($config->web->title) ->setSeparator(' :: ');
        
        $view->headMeta() ->appendName ('GENERATOR','Candoo '.Candoo_Version::VERSION);
        
        $view->headMeta()->appendName('keywords',    $config->web->meta->keyword);
        $view->headMeta()->appendName('description', $config->web->meta->description);
        
        $view->headMeta() ->appendName ('copyright 2011','Payam Naderi. Programmer and Web Developer , darksoul.design@yahoo.com');
    }    
}