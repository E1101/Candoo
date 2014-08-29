<?php
class Candoo_Uri
{
    /**
     * Yek web address e relative raaa be address e absolute tabdil mikonad
     * exp. /candoo/templates/ -> http://www.domain.com/candoo/templates/
     * 
     * @param string $address
     */
    public static function UrlRelToAbs($address)
    {
        if (Zend_Uri::check($address)) {
            return $address;
        }
        
        $request = Candoo_App_Resource::get('request');
        $host = $request->getHttpHost();
        $schm = $request->getScheme();
        
        return $schm.'://'.$host.$address;
    }
    
    public static function build (array $segments)
    {
        $url = '';
     
        foreach ($segments as $segment) {
            $segment = (string) $segment;
            $segment = trim($segment,'/');
			if ($segment != '') {
			    $url.= '/';
			    $url.=$segment;
			}
        }
        
        return $url;
    }
    
    public static function check($uri)
    {
        if (Zend_Uri::check($uri)) {
            return true;
        }
        
        // emkaan daarad ke address e relative baashad
        $uri = 'http://domain.com/'.ltrim($uri,'/');
        return Zend_Uri::check($uri);
    }
}
