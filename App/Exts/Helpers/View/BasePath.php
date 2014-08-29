<?php

/**
 * Helper for retrieving the BasePath
 *
 */
class Helpers_View_BasePath extends Zend_View_Helper_Abstract
{
    /**
     * BaseUrl
     *
     * @var string
     */
    protected $_baseUrl;

    /**
     * Returns site's base url, or file with base url prepended
     *
     * $file is appended to the base url for simplicity
     *
     * @param  string|null $file
     * @return string
     */
    public function basePath($file = null)
    {
        // Get baseUrl
        $baseUrl = $this->getBasePath();

        // Remove trailing slashes
        if (null !== $file) {
            $file = '/' . ltrim($file, '/\\');
        }

        return $baseUrl . $file;
    }

    /**
     * Get BaseUrl
     *
     * @return string
     */
    public function getBasePath()
    {
        if ($this->_baseUrl === null) {
            $basePath = Candoo_App::getBasePath();
            
            $this->setBasePath($basePath);
        }

        return $this->_baseUrl;
    }
    
    /**
     * Set BaseUrl
     *
     * @param  string $base
     * @return Zend_View_Helper_BaseUrl
     */
    public function setBasePath($base)
    {
    	$this->_baseUrl = rtrim($base, '/\\');
    	return $this;
    }
    
}
