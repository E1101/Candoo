<?php
class cForm_Init extends Candoo_App_Initalize_Abstract
{
    public function _initHelpers()
    {
        if (!Candoo_JQuery::isEnabledView()) {
            return;
        }
        
        // agar jQuery fa`aal bood helper haaye jQuery e cForm raa add mikonad
        $view = Candoo_App_Resource::get('view');
    	if (false === $view->getPluginLoader('helper')->getPaths('cForm_Helpers_View_JQuery_')) {
            $view->addHelperPath(APP_DIR_CORE.DS.'cForm'.DS.'Helpers'.DS.'View'.DS.'JQuery', 'cForm_Helpers_View_JQuery_');
        }
    }
}