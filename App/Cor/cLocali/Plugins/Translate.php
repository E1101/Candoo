<?php
class cLocali_Plugins_Translate extends Zend_Controller_Plugin_Abstract
{    
    public function routeShutdown($request)
    {
        /**
         * File haaye translation e in module raa baraaie estefaade dar translator
         * moa`refi mikonad
         *
         */
        $module = $request->getModuleName();
        Candoo_App_Resource::get('translator')->addTranslation($module);
    }
}
