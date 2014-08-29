<?php
/**
 * Script ro be onvaane yek action helper gharaar daadam,
 * chon hengaami ke dispatcher be class e Controller miresad
 * tavasote helperBroker helper haaie mojood e aaan raa init mikonad
 * viewRenderer hengaame init shodan be view scriptPath e controller
 * raa dar ebtedaa ezaafe mikonad ke baa`es mishavad aval samte default
 * baraaie render ersaal shavad na script haaye samte template
 *   
 * @author root
 */
class cTemplate_Helpers_Action_Scripts extends Zend_Controller_Action_Helper_Abstract
{
    public function preDispatch()
    {
        $request = Candoo_App_Resource::get('request');
        // template script haaye samte admin baraaie template haa dobaare neveshte nemishavad
        if ($request->isOnAdminArea()) {
        	return;
        }
        
        $view    = Candoo_App_Resource::get('view');
        $layout  = Candoo_App_Resource::get('template');
    
    	/** Afzoodan e script haaie tarafe template dar soorat e inke mojood boodand */
    	$viewRenderer = Candoo_App_Resource::get('viewRenderer');
    	$scriptFile = $layout->getDir().DS.'views'.DS.'scripts'.DS.$request->getModuleName().DS.$request->getControllerName().DS.$request->getActionName().'.'.$viewRenderer->getViewSuffix();
    	if ( file_exists($scriptFile) )
    	{
    		$view->addScriptPath($layout->getDir().DS.'views'.DS.'scripts'.DS.$request->getModuleName());
    		 
    		/** reset viewRenderer basePath baraaie inke az folder e default e view render nakonad
    		 va script e daroon e template raa render konad
    		 */
    		 // depricated logical error
    		//$viewRenderer->setViewBasePathSpec(':controller'.DS.':action.:suffix');
    	}
    }
}
