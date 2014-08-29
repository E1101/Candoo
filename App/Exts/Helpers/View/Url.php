<?php
/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 */
class Helpers_View_Url extends Zend_View_Helper_Abstract
{
    /**
     * Generates an url given the name of a route.
     * O) agar $route_name vaared shode bood $route_scheme mitavaanad module_controller_action baashad
     *   
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
        
        /**
         * Dar hengaami ke dar admin hastim farz bar in ast ke route e morede estefaade admin ast 
         */
        if ($route_name == null && $request->isOnAdminArea()) {
            $route_name = 'admin';
        }
        
        /**
         * $this->url('module_controller_action',array('page'=>1),'admin');
         */
        if ($route_name !== null) {
            // module_controller_action ro be in soorat az rooie route_scheme mikhaanad
            if (is_string($route_scheme)) {
                
            	// get module_controller_action
            	// @ because momkene ba`zi az onsor haaie m/c/a masalan c/a yaa /a mojood nabaashad
            	@list($m,$c,$a) = explode('_', $route_scheme);
            
            	// set router dispatcher M/C/A key
            	$route_scheme = array();
            	if (!empty($m)) {
            		$route_scheme[$request->getModuleKey()] = $m;
            	}
            	if (!empty($c)) {
            		$route_scheme[$request->getControllerKey()] = $c;
            	}
            	if (!empty($a)) {
            		$route_scheme[$request->getActionKey()] = $a;
            	}
            } else {
                $route_scheme = (array) $route_scheme;
            }
            
            if (!is_array($route_scheme)) {
            	throw new Exception('Invalid Route Scheme entered in url helper');
            }
        }
        /**
         * Agar $route_name vaared nashode bood $route_scheme naaame yek route ast
         * 
         * $this->url('home',array('param'=>'value'));
         */
        else 
        {
            $route_name = $route_scheme;
            // emkaan daarad ke route shaamele horoof e unicode baashad, exp. /آرشیو
            $encode = false;
            
            $route_scheme = array();
        }
                
        // baraaie ersaal be $router->assemble
        $urlOptions += $route_scheme+$params;
                
        /* Language be onvaane baseUrl be route ezaafe mishavad pas hengaame link 
         * be safahaate ham zabaan ehtiaaj nist tekraaar shavad
         * 
         * // Ezaafe kardane language be url
        $currLocale = (string) Candoo_App_Resource::get('locale');
        $defLocale  = Candoo_Module::getConfig('cLocali')->locale->default;
        
        // add locale to route only if not default
        $localePart = null;
        if ($currLocale != $defLocale) {
            $currLocale = '';
        } */
               
        $router = Candoo_App_Resource::get('frontController')->getRouter();
        return $router->assemble($urlOptions, $route_name, $reset, $encode);
    }
}
