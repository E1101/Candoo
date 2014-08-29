<?php
class Candoo_Request_Http extends Candoo_Request_Http_Abstract
{
    /**
     * Base URL of request
     * ghabl az pathinfo gharaar migirad
     * [/appDir/subSite/fa]/article/list
     *
     * @var string
     */
    protected $_baseUrl = null;
    
    /**
     * dar dovomin stack az uri ghesmat e service haa ro daaarim ke naaame aaan injaa negah daashte mishavad
     * /[subsite][/service]/site
     * /........./admin|srv/
     *
     * @see self::isOnAdminArea | self::isOnServiceArea
     * @var string
     */
    protected $_service;
    
    
    /**
     * Set the REQUEST_URI on which the instance operates
     *
     * If no request URI is passed, uses the value in $_SERVER['REQUEST_URI'],
     * $_SERVER['HTTP_X_REWRITE_URL'], or $_SERVER['ORIG_PATH_INFO'] + $_SERVER['QUERY_STRING'].
     *
     * @param string $requestUri
     * @return Zend_Controller_Request_Http
     */
    public function setRequestUri($requestUri = null)
    {
    	if ($requestUri === null) {
    		if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
    			$requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
    		} elseif (
    				// IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
    				isset($_SERVER['IIS_WasUrlRewritten'])
    				&& $_SERVER['IIS_WasUrlRewritten'] == '1'
    				&& isset($_SERVER['UNENCODED_URL'])
    				&& $_SERVER['UNENCODED_URL'] != ''
    		) {
    			$requestUri = $_SERVER['UNENCODED_URL'];
    		} elseif (isset($_SERVER['REQUEST_URI'])) {
    			$requestUri = $_SERVER['REQUEST_URI'];
    			// Http proxy reqs setup request uri with scheme and host [and port] + the url path, only use url path
    			$schemeAndHttpHost = $this->getScheme() . '://' . $this->getHttpHost();
    			if (strpos($requestUri, $schemeAndHttpHost) === 0) {
    				$requestUri = substr($requestUri, strlen($schemeAndHttpHost));
    			}
    
    			// agar mod_rewrite fa`aal bood script name raa az request uri hazf mikonad `````````|
    			if ($this->isAvailableRewriteMode()) {
    				// always index.php
    				$scriptName = basename(Candoo_App::getScriptUri());
    				$basePath   = Candoo_App::getBasePath();
    					
    				// az basePath be ba`d /candoo/[index.php]/route
    				// 				  yaaa /candoo/route
    				$reqWithoutBase = ltrim(substr($requestUri, strlen($basePath)),'/');
    				$reqWithoutBase = explode('/', $reqWithoutBase);
    
    				// agar [index.php] dar address mojood bood, hazf mishavad
    				if ($reqWithoutBase[0] == $scriptName) {
    					unset($reqWithoutBase[0]);
    				}
    					
    				$requestUri = $basePath.'/'.implode('/', $reqWithoutBase);
    			}
    			// ```````````````````````````````````````````````````````````````````````````````````
    
    		} elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
    			$requestUri = $_SERVER['ORIG_PATH_INFO'];
    			if (!empty($_SERVER['QUERY_STRING'])) {
    				$requestUri .= '?' . $_SERVER['QUERY_STRING'];
    			}
    		} else {
    			return $this;
    		}
    	} elseif (!is_string($requestUri)) {
    		return $this;
    	} else {
    		// Set GET items, if available
    		if (false !== ($pos = strpos($requestUri, '?'))) {
    			// Get key => value pairs and set $_GET
    			$query = substr($requestUri, $pos + 1);
    			parse_str($query, $vars);
    			$this->setQuery($vars);
    		}
    	}
    	
    	// convert /candoo/fa/%D8%A7%D8%AE%D8%A8%D8%A7%D8%B1/science/html5-20120805.html to /cando/fa/اخبار
    	$requestUri = urldecode($requestUri);
    	
    	$this->_requestUri = $requestUri;
    	return $this;
    }
    
    /**
     * Set the base URL of the request; i.e., the segment leading to the script name
     *
     * E.g.:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Do not use the full URI when providing the base. The following are
     * examples of what not to use:
     * - http://example.com/admin (should be just /admin)
     * - http://example.com/subdir/index.php (should be just /subdir/index.php)
     *
     * If no $baseUrl is provided, attempts to determine the base URL from the
     * environment, using SCRIPT_FILENAME, SCRIPT_NAME, PHP_SELF, and
     * ORIG_SCRIPT_NAME in its determination.
     *
     * @param mixed $baseUrl
     * @return Zend_Controller_Request_Http
     */
    public function setBaseUrl($baseUrl = null)
    {
    	if ((null !== $baseUrl) && !is_string($baseUrl)) {
    		return $this;
    	}
    
    	if ($baseUrl === null) {
    
    		$baseUrl = Candoo_App::getScriptUri();
    
    		// Does the baseUrl have anything in common with the request_uri?
    		$requestUri = $this->getRequestUri();
    
    		/**
    		 * Hengaami ke requestUri be daroon e hamin folder e script eshaare mikard
    		 * az saakhtaare http://domain/[subsite]/[service]/[locale]
    		 * peiravi mikonad
    		 */
    		if (0 === strpos($requestUri, dirname($baseUrl))) {
    			// agar mod_rewrite fa`aal bood script name raa hazf mikonad
    			if ($this->isAvailableRewriteMode()) {
    				$baseUrl = dirname($baseUrl);
    			}
    
    			// ```````````````````````````````````````````````````````````````````````````````````|
    			// be donbaale subsite dar uri migardad va agar mojood bood
    			// aan raa be baseUri ezaafe mikonad
    
    			// az basePath be ba`d /candoo/[index.php]/route
    			// 				  yaaa /candoo/route
    			$ptInf = ltrim(substr($requestUri, strlen($baseUrl)),'/');
    			$ptInf = explode('/', $ptInf);
    
    			// first stack of uri
    			$uriFS = array_shift($ptInf);
    			if ( $uriFS == Candoo_Site::getSite() ) {
    				$baseUrl .= '/'.$uriFS;
    			} else {
    				array_unshift($ptInf, $uriFS);
    			}
    			// ```````````````````````````````````````````````````````````````````````````````````
    
    			// ```````````````````````````````````````````````````````````````````````````````````|
    			// /candoo/admin || /candoo/srv
    			$uriFS = array_shift($ptInf);
    			if ( $uriFS == $this->getAdminUriName() || $uriFS == $this->getServicesUriName() ) {
    				$this->_service = ($uriFS == $this->getAdminUriName())
    				? $this->getAdminUriName()
    				: $this->getServicesUriName();
    				$baseUrl .= '/'.$uriFS;
    			} else {
    				array_unshift($ptInf, $uriFS);
    			}
    			// ```````````````````````````````````````````````````````````````````````````````````
    
    			// directory portion of $baseUrl matches
    			$this->_baseUrl = rtrim($baseUrl, '/');
    			return $this;
    		}
    
    	}
    
    	$this->_baseUrl = rtrim($baseUrl, '/');
    	return $this;
    }
    
    public function isOnAdminArea()
    {
    	if ($this->_baseUrl == null) {
    		$this->getBaseUrl();
    	}
    
    	return ($this->_service == $this->getAdminUriName());
    }
    
    public function getAdminUriName()
    {
    	$backend = Candoo_App::getConfig()->request->uri->backend;
    
    	return ($backend) ? $backend : 'admin';
    }
    
    public function isOnServiceArea()
    {
    	if ($this->_baseUrl == null) {
    		$this->getBaseUrl();
    	}
    
    	return ($this->_service == $this->getServicesUriName());
    }
    public function getServicesUriName()
    {
    	return 'srv';
    }
    
}
