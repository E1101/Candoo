<?php
class Pages_Init extends Candoo_App_Initalize_Abstract
{
    public function _initRoutes()
    {        
        $front   = Candoo_App_Resource::get('frontController');
        $request = Candoo_App_Resource::get('request');
        
        $routes  = $this->getConfig()->toZendConfig();
        
        $front->getRouter()
        	  ->removeDefaultRoutes()
        	  ->addConfig($routes,'routes')
        ;
    }
}