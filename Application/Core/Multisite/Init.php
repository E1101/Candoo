<?php
class Multisite_Init extends Candoo_App_Initalize_Abstract
{
    public function _initUrl()
    {
        $request = Candoo_App_Resource::get('request');
        
        /* be donbaale subsite dar uri migardad va agar mojood bood
         * aan raa be baseUri ezaafe mikonad
        * */
        $pathInfo = $request->getPathInfo();
        $ptInf = explode('/', ltrim($pathInfo,'/')); //exploded array of pathinfo
        
        // first stack of uri
        $uriFS = array_shift($ptInf);
        if ( strtolower($uriFS) == Multisite_Lib_Sites::getSite() ) {
            $bs = $request->getBaseUrl();
            $request->setBaseUrl($bs.'/'.$uriFS);
            
        	// set pathinfo after subsite section
        	$pathInfo = '/'.implode('/', $ptInf);
        	$request->setPathInfo($pathInfo);
        }
    }
}