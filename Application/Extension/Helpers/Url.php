<?php
/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 */
class Extension_Helpers_Url extends Zend_View_Helper_Abstract
{
    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function url($route_scheme,$params = array(), $route_name = null, $reset = true, $encode = true)
    {
        /*
         * be soorat e array('module'=>'moduleName','controlller'=>'controllerName' ....
         */
        $urlOptions = array();

        $request = Candoo_App_Resource::get('request');
        
        if ($request->isOnAdminArea()) 
        {
            if (is_string($route_scheme)) {
            	// get module>controller>action
            	list($m,$c,$a) = explode('>', $route_scheme);
            
            	// set router dispatcher M/C/A key
            	$route_scheme = array();
            	$request = Candoo_App_Resource::get('request');
            	if (!empty($m)) {
            		$route_scheme[$request->getModuleKey()] = $m;
            	}
            	if (!empty($c)) {
            		$route_scheme[$request->getControllerKey()] = $c;
            	}
            	if (!empty($a)) {
            		$route_scheme[$request->getActionKey()] = $a;
            	}
            }
            
            $urlOptions += $route_scheme+$params;     
        } 
        else 
        {
            $route_name = $route_scheme;
            $urlOptions = $params;
            // emkaan daarad ke route shaamele horoof e unicode baashad, exp. /آرشیو
            $encode = false;
        }
        
        // Ezaafe kardane language be url
        $currLocale = (string) Candoo_App_Resource::get('locale');
        $defLocale  = Candoo_Extension_Module::getConfig('Localization')->locale->default;
        
        // add locale to route only if not default
        $localePart = null;
        if ($currLocale != $defLocale) {
            $currLocale = '';
        }
                
        $router = Zend_Controller_Front::getInstance()->getRouter();
        return $router->assemble($urlOptions, $route_name, $reset, $encode);
    }
}
