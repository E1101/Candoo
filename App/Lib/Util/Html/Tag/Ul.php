<?php
class Util_Html_Tag_Ul extends Util_Html_Decorator
{
	protected $_tag  = 'ul';
	
	public function _import($content)
	{
		if (!is_array($content)) {
			throw new Exception('content must be an array');
		}
				
		foreach ($content as $node) {
			if (is_string($node)) {
				array_push( $this->_nested, new Util_Html_Tag_Li($node) );
			} elseif ($this->_isPrintable($node)) {
				array_push($this->_nested, $node);
			} else {
				throw new Exception('content must be a string or __toString able');
			}
		}
	}
}