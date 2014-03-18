<?php
class Candoo_Request extends Zend_Controller_Request_Http
{
	public function setBaseUrl($baseUrl=null)
	{	    
	    parent::setBaseUrl($baseUrl);
	    
	    /* agar rewrite_mode fa`aal nabood index.php ro be baseurl
		   ezaafe mikonad (faghat ehtemaalan baraaie homepage ettefaagh mioftad)
		*/
		if (!$this->isAvailableRewriteMode()) {
			$baseUrl = $this->getBaseUrl();
			$scriptName = basename($this->getServer('SCRIPT_FILENAME')); // always index.php
			
			// agar index.php ro nadaasht
			if(strpos(strtolower($baseUrl), $scriptName)===false) {
				// add index.php also on requestUri
				$reqUri = $this->getRequestUri();
				$reqUri = str_replace($baseUrl, '', $reqUri);
				
				$baseUrl = rtrim($baseUrl) . '/'.$scriptName.'/';
				parent::setBaseUrl($baseUrl);
				
				$this->setRequestUri(rtrim($baseUrl,'/').$reqUri);
			}
		}
	}
	
	public function isOnAdminArea()
	{
		$path = str_replace($this->getBaseUrl(), '', $this->getRequestUri());
		$path = explode('/',ltrim($path,'/'));
		
		// avalin stack az uri
		$frstStk = array_shift($path);
				
		// agar first stack dar ekhtiaar e host bood stack ba`d raa negaah mikonad
		if (Multisite_Lib_Sites::getSite() == $frstStk) {
		    $frstStk = array_shift($path);
		}
				
		$backend = Candoo_Config::getInstance()->request->uri->backend;
		if ($frstStk == $backend) {
		    return $backend;
		} else {
		    return false;
		}
	}
	
		
	public static function isAvailableRewriteMode()
	{
		if (function_exists('apache_get_modules')) {
  			$modules = apache_get_modules();
  			$mod_rewrite = in_array('mod_rewrite', $modules);
		} else {
  			$mod_rewrite =  (getenv('HTTP_MOD_REWRITE')=='On') ? true : false ;
		}
		
		return $mod_rewrite;
	}
	
	public static function parseQueryString($query) 
	{
    	$values = array();
    	
    	if (!empty($query)) {
    	    $pairs = explode("&", $query);
    	    foreach ($pairs as $pair) {
    	    	list($k, $v) = array_map("urldecode", explode("=", $pair));
    	    	$values[$k] = $v;
    	    }
    	}
   
        return $values;
    }
	
}
