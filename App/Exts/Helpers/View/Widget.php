<?php
class Helpers_View_Widget extends Zend_View_Helper_Abstract
{
	/**
	 * Store widget class
	 * 
	 * @var object Widget class
	 */
	protected  $_class;
	
	public function widget($name,$module=null)
	{
		$name = ucfirst(strtolower($name));
		// modules widgets
		if ($module) {
			$class = $module.'_Widgets_'.$name.'_Widget';
		} 
		// stand alone widgets
		else {
			$class = 'Widgets_'.$name.'_Widget';
		}
		
		if (! class_exists($class,true)) {
			trigger_error('Widget '.$class.' not found',E_USER_WARNING);
		} else {
			$this->_class = new $class();
		}
		
		if ($this->_class) {
		    return $this->_class;
		}
		
		// baraaie jolo giri az khataaie fatal error dar hengaami ke class mojood nist
		return $this;
	}
	
	// baraaie jolo giri az khataaie fatal error dar hengaami ke class mojood nist
	public function __call($name,$args)
	{
	    
	}
}
