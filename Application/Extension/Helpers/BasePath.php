<?php

/**
 * Helper for retrieving the BasePath
 *
 */
class Zend_View_Helper_BasePath extends Zend_View_Helper_Abstract
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

    /**
     * Get BaseUrl
     *
     * @return string
     */
    public function getBasePath()
    {
        if ($this->_baseUrl === null) {
            $basePath = Candoo_App_Resource::get('request')->getBasePath();

            // Remove scriptname, eg. index.php from baseUrl
            $basePath = $this->_removeScriptName($basePath);

            $this->setBasePath($basePath);
        }

        return $this->_baseUrl;
    }

    /**
     * Remove Script filename from baseurl
     *
     * @param  string $url
     * @return string
     */
    protected function _removeScriptName($url)
    {
        if (!isset($_SERVER['SCRIPT_NAME'])) {
            // We can't do much now can we? (Well, we could parse out by ".")
            return $url;
        }

        if (($pos = strripos($url, basename($_SERVER['SCRIPT_NAME']))) !== false) {
            $url = substr($url, 0, $pos);
        }

        return $url;
    }
}
