<?php
/**
 * Plugin i ke dar har darkhaast rout baraaie ejraaie yek module
 * file e bootstrap e aan raaa ejraa mikonad
 *
 */
class Candoo_App_Bootstraper_Plugin extends Zend_Controller_Plugin_Abstract
{
    public function routeShutdown($request)
    {
        $bootstrapClass = $request->getModuleName().'_Bootstrap';
        if (class_exists($bootstrapClass)) {
            $class = new $bootstrapClass($request);
        }
    } 
}
