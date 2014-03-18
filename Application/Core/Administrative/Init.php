<?php
class Administrative_Init extends Candoo_App_Initalize_Abstract
{
    public function _initUrl()
    { 
    	// avalin stack marboot be subsite ast
        $this->init('Multisite');
        
        $front   = Candoo_App_Resource::get('frontController');
        $request = Candoo_App_Resource::get('request');
        
        if ( $request ->isOnAdminArea() ) {
        	$pathInfo = ltrim($request->getPathInfo(),'/');
        	$pathInfo = explode('/', $pathInfo);
        
        	array_shift($pathInfo);
        	$pathInfo = implode('/',$pathInfo);
        	$request ->setPathInfo('/'.$pathInfo);
        }
    }
    
    public function _initControllers()
    {
        $front   = Candoo_App_Resource::get('frontController');
        $request = Candoo_App_Resource::get('request');
        
        if ( $request ->isOnAdminArea() ) {
            // set controller directory to admin folder inside modules for separating admin code
            $front->setModuleControllerDirectoryName('admin'.DS.'controllers');
             
            /**
             * TODO: controller directory pish farz dar soorati ke address yaaft nashod
             * ehtemaalan baayad be dashboard e admin eshaare konad
             */
            // agar dar hengaame dispatch yek address be C/A naresad, controller default raa ejraa mikonad
            $front->setParam('useDefaultControllerAlways',true);
             
            $dispatcher = $front->getDispatcher();
            $dispatcher->setDefaultModule('Administrative');
        }
    }
    
    public function _initRouter()
    {
        $front   = Candoo_App_Resource::get('frontController');
        $request = Candoo_App_Resource::get('request');
        
        if ( $request ->isOnAdminArea() ) {
        	$front->getRouter()
        		  ->removeDefaultRoutes()
        		  // pass request and dispatcher object to route
        		  ->addRoute('admin',new Administrative_Lib_Route(array(),$front->getDispatcher(),$request));
        }
    }
}