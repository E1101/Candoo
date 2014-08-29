<?php
class cForm_Lib_Validator_Confirm extends Zend_Validate_Abstract
{
	const NOT_MATCH = 'notMatch';
	
	/**
	 * Element i ke confirm baayad baa aan moghaaiese shavad
	 * 
	 */
	protected $_confirmto;
	
	protected $_messageTemplates = array(
		self::NOT_MATCH => 'Confirmation does not match',
	);
	
	public function __construct($confirmElement = 'confirm')
	{
	    $this->setConfirmTo($confirmElement);
	}
	
	public function isValid($value,$context = null) 
	{
	    $value = (string) $value;
	    $this->_setValue($value);
	    
	    if (is_array($context)) {
	        $confirmTo = $this->getConfirmTo();
	    	if (isset($context[$confirmTo]) && ($value == $context[$confirmTo]) ) {
	    	    return true;
	    	}	    
	    } elseif (is_string($context) && ($value == $context)) {
	        return true;
	    }
	    
	    $this->_error(self::NOT_MATCH);
	    
	    return false;
	}
	
	public function setConfirmTo($element)
	{
	    $this->_confirmto = $element;
	}
	
	public function getConfirmTo()
	{
	    return $this->_confirmto;
	}
}
