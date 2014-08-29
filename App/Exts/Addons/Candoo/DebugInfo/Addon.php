<?php
/*
 * ---------------------------------------------------
* @author              abernardi77 at gmail dot com
* @version             0.3.3
* ---------------------------------------------------
*
* Copyright (c) 2012 andrea bernardi - abernardi77@gmail.com
* tested with PHP 5.3.8 (cli) (built: Dec  5 2011 21:24:09) on Mac osX 10.6.8
*
* Permission is hereby granted, free of charge,
* to any person obtaining a copy of this software and associated
* documentation files (the "Software"),
* to deal in the Software without restriction,
* including without limitation the rights to use, copy, modify, merge,
* publish, distribute, sublicense, and/or sell copies of the Software,
* and to permit persons to whom the Software is furnished to do so,
* subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included
* in all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
* INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
* DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
* ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
* OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class Addons_Candoo_DebugInfo_Addon extends Candoo_Addon_Abstract
{
    protected static $_stime;
    
    protected static $_deltas = array();
    
	public function onAppRunAction() 
	{
	    $this->setNoRender();
	    
	    $this->stop(__FUNCTION__);
	    $this->resetTime();
	    
	}
	
	public function onScriptShutdownAction()
	{
	    $this->setNoRender();
	    
	    // Running Proccess time > 0.061204195022583ms
	    
	    $this->stop(__FUNCTION__);

	    $this->_view->assign('deltas',self::$_deltas);
	    
	    // ```````````````````````````````````````````````````````````````````
	    // query profiler
	    if (Candoo_App_Resource::isRegistered('db')) {
	        $profiler = Candoo_App_Resource::get('db')->getProfiler();
	        
	        $profiles = $profiler ->getQueryProfiles();
	        
	        $this->_view->assign('profiles',$profiles);
	    }
	    
	    // manualy render output on screen
	    echo $this->_view->render($this->getViewScript('onScriptShutdown'));
	}
	
	
	
	protected function resetTime() 
	{
		self::$_stime = $this->getMicrotime();
	}
	
	protected function stop($msg="") 
	{
		$this->addDelta($msg, $this->deltaTime(self::$_stime));
	}
	
	private function addDelta($msg="", $deltaT) 
	{
		$mem = $this->getRamUsage( memory_get_usage(true) );
		self::$_deltas[] = array(
				"EVENT" => $msg,
				"DELTA" => "" . $deltaT . " s",
				"RAM_USAGE" => $mem);
	}
	
	// following method found somewhere into php online manual
	private function getRamUsage($size) 
	{
		$unit = array('b','kb','mb','gb','tb','pb');
		return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
	}
	
	private function deltaTime($stTime) 
	{
		return $this->getMicrotime() - $stTime;
	}
	
	private function getMicrotime()
	{
		return microtime(true);
	}
	
}
