<?php
/**
 * View helper for retrieving layout object
 *
 * @subpackage Helper
 */
class Helpers_View_Layout extends Zend_View_Helper_Abstract
{
    /** @var Zend_Layout */
    protected $_layout;

    /**
     * Return layout object
     *
     * Usage: $this->layout()->setLayout('alternate');
     *
     * @return Zend_Layout
     */
    public function layout()
    {
        return $this->getLayout();
    }
    
    /**
     * Get layout object
     *
     * @return Zend_Layout
     */
    public function getLayout()
    {
    	if (null === $this->_layout) {
    		$layout = Candoo_App_Resource::get('template',false);
    		if (!$layout) {
    		    $layout = Zend_Layout::getMvcInstance();
    		    if (!$layout) {
    		        // Implicitly creates layout object
    		        $layout = new Zend_Layout();
    		    }
    		}
    		
    		$this->_layout = $layout;
    	}
    
    	return $this->_layout;
    }
}
