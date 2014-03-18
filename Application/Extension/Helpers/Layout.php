<?php
/**
 * View helper for retrieving layout object
 *
 * @subpackage Helper
 */
class Extension_Helpers_Layout extends Zend_View_Helper_Abstract
{
    /** @var Zend_Layout */
    protected $_layout;

    /**
     * Get layout object
     *
     * @return Zend_Layout
     */
    public function getLayout()
    {
        if (null === $this->_layout) {
            $this->_layout = Candoo_App_Resource::get('template');
        }

        return $this->_layout;
    }

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
}
