<?php
class Extension_Helpers_Widget extends Zend_View_Helper_Abstract
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
		
		return $this;
	}
	
	public function __call($name,$args)
	{
		if (!$this->_class) {
			return null;
		}
		
		$args = (isset($args[0])) ? $args[0] : array();
		return $this->_class->$name($args);
	}
}
