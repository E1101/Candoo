<?php
class Candoo_Addon_Registry
{	
	/**
	 * @var array
	 */
	private static $_targets;
	
	private function __construct() 
	{
		self::$_targets = array();
	}
	
	/**
	 * @param array $array
	 * ["target"] => array(1) {
    	 ["event"] => array(2) {
      		[0] => string(28) "Addons_AddonGroup_Test_Addon"
      		[1] => string(31) "Addons_AddonGroup_Boarder_Addon"
    	}
    	
	 */
	public static function registerAddons(array $array)
	{
	    foreach ($array as $target => $hooks) {
	    	if (is_array($hooks)) {
	        	foreach ($hooks as $hook) {
	            	self::register($target, $hook);
	        	}
	    	}
	    }
	    
	    return ;
	} 
	
	/**
	 * Register an addon to target
	 * 
	 */
	public static function register($target, $hook, $args = array(), $priority = null) 
	{
	    if (!is_array($args)) {
	        $args = array();
	    }
	    
	    $hook = (is_string($hook)) ? $hook : get_class($hook);
	    
		$priority = (is_int($priority)) ? $priority : (isset(self::$_targets[$target]) ? Candoo_Dataset_Array::lastKey(self::$_targets[$target])+1 : 0);
		
		/**
		 * Class e tekraari dar target be onvaane hook ezaafe nashavad
		 * 
		 */
		self::unregister($target,$hook,$args);
		
		/**
		 * Test mikonim ke aayaa $priority vojood daarad ?? 
		 * agar bood baayad baghie anaasor e array ba`d az aan shift daade shavand
		 */
		if (isset(self::$_targets[$target][$priority])) 
		{
			$list = self::$_targets[$target];
			
			$right = array_slice(self::$_targets[$target],$priority);
			$left  = array_slice($list,$priority,count($list) - count($right));
			
			array_unshift($right,array('class'=>$hook, 'args'=>$args));
			$result = array_merge($left, $right);
			
			self::$_targets[$target] = $result;
		}
		else
		{
			self::$_targets[$target][$priority] = array('class' =>$hook, 'args' =>$args);
		}
						
		return true;
	}
	
	/**
	 * Unregister a hook
	 * 
	 * @param $hook string|Candoo_Addon_Abstract
	 */
	public static function unregister($target, $hook, $args = array()) 
	{
	    if (!is_array($args)) {
	    	$args = array();
	    }
	    	    	     
	    if (isset(self::$_targets[$target])) {
	    	$curHook = (is_string($hook)) ? $hook : get_class($hook);	
	    	foreach (self::$_targets[$target] as $p => $h) {
	    		if ($h['class'] == $curHook && self::_argsToKey($args) == self::_argsToKey($h['args'])) {
	    			unset(self::$_targets[$target][$p]);
	    			ksort(self::$_targets[$target]);
	    			return true;
	    		}
	    	}
	    }
	    
	    return false;
	}
	
	
	public static function isRegistered($target, $hook, $args = array()) 
	{
	    if (!is_array($args)) {
	    	$args = array();
	    }
	    
		if (isset(self::$_targets[$target])) {
	    	$curHook = (is_string($hook)) ? $hook : get_class($hook);
	    	foreach (self::$_targets[$target] as $p => $h) {
	    		if ($h['name'] == $curHook && self::_argsToKey($h['args'] == self::_argsToKey($args))) {
	    			return true;
	    		}
	    	}
	    }
	    
	    return false;
	}
	
	protected static function _argsToKey(array $args)
	{
	    return md5(serialize($args));
	}
		
	/**
	 * Execute a target on spec. event
	 * 
	 * pas as target va event mitavaanad parameter haaye marboot be eventAction e addOn ersaal shavad
	 * exp. exec('candoo','appendBody','<this is the text>')
	 */
	public static function exec($target, $event)
	{
	    // az offset 2 be ba`d marboot be arguman haaie eventAction ast
	    // exec($target, $event, $arg1, $arg2)
	    $args  = array_slice(func_get_args(),2);
		if (!isset(self::$_targets[$target]) || empty(self::$_targets[$target])) {
		    return;
		}
			    
	    $hooks = &self::$_targets[$target];
	    
		// * bar asaase piority hook ha raa sort mikonad
		ksort($hooks);
				
		foreach ( $hooks as $index => $hook) 
		{
		    $class = $hook['class'];
		    $class = new $class($hook['args']);
		   	
		    // Action postfix automaticaly add after method when __call trigger on Addon class
		    $eventMethod = $event.'Action';
		    if (method_exists($class,$eventMethod)) {
		        // meghdaar e bargashti e hook e ghabli ro be hook e ba`di mifrestad
		        if ( isset($value) ) {
		            $preVal = $value;
		            // meghdaar e baazgashtie ghabli raa be onvaan e parameter hook e ba`di ersaal mikonad
		            // farz bar in ast ke avali argument baraaie in kaar dar nazar gerefte shode
		            $args[0] = $preVal;
		        }
		        		        
		    	$value = call_user_func_array(array($class,$event), $args);
		    	 
		    	// agar in method meghdaari bargasht nadaad ya`ni faghat action boode
		    	// va meghdaar e ghabli jaaaigozin mishavad
		    	if (!$value && isset($preVal)) {
		    	    $value = $preVal;
		    	// agar hanooz meghdaar e ghabli baraaie $preVal naboode
		    	// va $value ham ke meghdaar nadaarad pas unset mishavad
		    	} elseif (!$value) {
		    	    unset ($value);
		    	}
		    }
		    
		    unset($class);
		}
		
		
		
		return (isset($value)) ? $value : null;
	}
	
	
	/**
	 * Execute a target on spec. event
	 *
	 * pas as target va event mitavaanad parameter haaye marboot be eventAction e addOn ersaal shavad
	 * exp. exec('candoo','appendBody','<this is the text>')
	 */
	public static function execChain($target, $event)
	{
		// az offset 2 be ba`d marboot be arguman haaie eventAction ast
		// exec($target, $event, $arg1, $arg2)
		$args  = array_slice(func_get_args(),2);
		if (!isset(self::$_targets[$target]) || empty(self::$_targets[$target])) {
			return;
		}
		 
		$hooks = &self::$_targets[$target];
		 
		// * bar asaase piority hook ha raa sort mikonad
		ksort($hooks);
		foreach ( $hooks as $index => $hook)
		{
			$class = $hook['class'];
			$class = new $class($hook['args']);
	
			$value = '';
			// Action postfix automaticaly add after method when __call trigger on Addon class
			$eventMethod = $event.'Action';
			if (method_exists($class,$eventMethod)) 
			{
				$value .= call_user_func_array(array($class,$event), $args);
			}
	
			unset($class);
		}
	
		return $value;
	}
	
	public static function getHooks()
	{
	    return self::$_targets;
	}

}//end Class

