<?php
class websplash_Helpers_ThemeServer extends Zend_View_Helper_Abstract
{
	private $prop = null;
		
	public function themeServer()
	{	
		$this->prop['serverName'] = $this->server_version();
		$this->prop['clientIp']   = $_SERVER['REMOTE_ADDR'];
		$this->prop['phpVer']     = phpversion();

		return $this;
	}

	public function __get($name)
	{
		if (isset($this->prop[$name]))
		{
			return $this->prop[$name];
		}
	    
		return null;
	}
	
	
	// Get apache version
	public function server_version()
	{
		if (function_exists('apache_get_version'))
		{
			if (preg_match('|Apache\/(\d+)\.(\d+)\.(\d+)|', apache_get_version(), $version))
			{
				return 'Apache '.$version[1].'.'.$version[2].'.'.$version[3];
			}
		}
		elseif (isset($_SERVER['SERVER_SOFTWARE']))
		{
			if (preg_match('|Apache\/(\d+)\.(\d+)\.(\d+)|', $_SERVER['SERVER_SOFTWARE'], $version))
			{
				return 'Apache '.$version[1].'.'.$version[2].'.'.$version[3];
			} 
		}
				
		return $_SERVER['SERVER_SOFTWARE'];
	}
}
