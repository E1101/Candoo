<?php
class Templates_Plugins_Scripts extends Zend_Controller_Plugin_Abstract
{    
    public function preDispatch(Candoo_Request $request)
    {
        // template script haaye samte admin baraaie template haa rewrite nemishavad
        if (Candoo_App_Resource::get('request')->isOnAdminArea()) {
            return;
        }
        
        $view    = Candoo_App_Resource::get('view');
        $layout  = Candoo_App_Resource::get('template');
        
       /** Ezaafe kardane helper haaaye morede estefade dar template */
        $view->addHelperPath(
        	$layout->getDir().DS.'views'.DS.'helpers',
        	$layout->getName().'_Helpers_'
        );

        
        /** Afzoodan e script haaie tarafe template dar soorat e inke mojood boodand */
        $viewRenderer = Candoo_App_Resource::get('viewRenderer');
        $scriptFile = $layout->getDir().DS.'views'.DS.'scripts'.DS.$request->getModuleName().DS.$request->getControllerName().DS.$request->getActionName().'.'.$viewRenderer->getViewSuffix();
        if (file_exists($scriptFile))
        {
        	$view->setScriptPath($layout->getDir().DS.'views'.DS.'scripts'.DS.$request->getModuleName());
		
        	/** reset viewRenderer basePath baraaie inke az folder e default e view render nakonad 
           	va script e daroon e template raa render konad
        	*/
        	$viewRenderer->setViewBasePathSpec(':controller'.DS.':action.:suffix');
        }
    }
}
