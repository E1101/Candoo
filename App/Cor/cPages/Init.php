<?php
class cPages_Init extends Candoo_App_Initalize_Abstract
{
    public function _initSiteStructure()
    {
        // get sitemap file
        $host = Candoo_Site::getSite();
        $confPath = Candoo_Module::getConfigDirPath('cPages');
        $file = $confPath.DS.'sitemap.'.$host.'.php';

        if (file_exists($file)) {
            $pages = include_once $file;
        }
                
        if (!is_array($pages)) {
            throw new Zend_Exception('Error in Sitemap file.');
        }
        
        // register routes to router ``````````````````````````````````````````````````````````````````````````|
        $routes = $this->_registerRoutes($pages);
        
        // add routes to router
        $front   = Candoo_App_Resource::get('frontController');
        $request = Candoo_App_Resource::get('request');
        
        if (! $request ->isOnAdminArea()) {
            $config = new Zend_Config($routes);
            $front->getRouter()->addConfig($config);
        }
        // `````````````````````````````````````````````````````````````````````````````````````````````````````
        
        // register sitemap as a resource to use accross site
        Candoo_App_Resource::set('sitemap', new Zend_Navigation($pages) );
    }
    
    protected function _registerRoutes(array $pages) 
    {
        $routes = array();
        foreach ($pages as $page) 
        {    
            if (isset($page['pages'])) {
            	$routes = $this->_registerRoutes($page['pages']); 
            }
            
        	if ( isset($page['route']) ) {
        		$route = $page['route'];
        		// set M/C/A
        		$routes[$route]['defaults']['module'] = $page['module'];
        		$routes[$route]['defaults']['controller'] = $page['controller'];
        		$routes[$route]['defaults']['action'] = $page['action'];
        		// set default params
        		if (isset($page['params'])) {
        			$routes[$route]['defaults'] = array_merge($routes[$route]['defaults'],$page['params']);
        		}
        		// set options of route
        		$routes[$route] = array_merge($routes[$route],$page['router']);
        	}
        }

        return $routes;
    }
    
}