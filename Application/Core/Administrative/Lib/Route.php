<?php
class Administrative_Lib_Route extends Zend_Controller_Router_Route_Abstract
{
    /**
     * URI delimiter
     */
    const URI_DELIMITER = '/';

    /**
     * Default values for the route (ie. module, controller, action, params)
     * @var array
     */
    protected $_defaults;

    protected $_values      = array();
    protected $_moduleValid = false;
    protected $_keysSet     = false;

    /**#@+
     * Array keys to use for module, controller, and action. Should be taken out of request.
     * @var string
     */
    protected $_moduleKey     = 'module';
    protected $_controllerKey = 'controller';
    protected $_actionKey     = 'action';
    /**#@-*/

    /**
     * @var Zend_Controller_Dispatcher_Interface
     */
    protected $_dispatcher;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    /**
     * I change version to 2, barraie inke dar Zend_Controller_Router_Rewrite
     * line 391:
            if (!method_exists($route, 'getVersion') || $route->getVersion() == 1) {
                $match = $request->getPathInfo();
            } else {
                $match = $request;
            }
       baraaie inke dar hengaame match pathinfo ro nemikhaaam
       chon parameter haa be in shekl ?m=modulename&c=controllername ersaal mishavad
     * @see Zend_Controller_Router_Route_Abstract::getVersion()
     */
    public function getVersion() {
        return 2;
    }

    /**
     * Instantiates route based on passed Zend_Config structure
     */
    public static function getInstance(Zend_Config $config)
    {
        $frontController = Candoo_App_Resource::get('frontController');

        $defs       = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        $dispatcher = $frontController->getDispatcher();
        $request    = $frontController->getRequest();

        return new self($defs, $dispatcher, $request);
    }

    /**
     * Constructor
     *
     * @param array $defaults Defaults for map variables with keys as variable names
     * @param Zend_Controller_Dispatcher_Interface $dispatcher Dispatcher object
     * @param Zend_Controller_Request_Abstract $request Request object
     */
    public function __construct(array $defaults = array(),
                Zend_Controller_Dispatcher_Interface $dispatcher = null,
                Zend_Controller_Request_Abstract $request = null)
    {
        $this->_defaults = $defaults;

        if (isset($request)) {
            $this->_request = $request;
        }

        if (isset($dispatcher)) {
            $this->_dispatcher = $dispatcher;
        }
    }

    /**
     * Set request keys based on values in request object
     *
     * @return void
     */
    protected function _setRequestKeys()
    {
        if (null !== $this->_request) {
            $this->_moduleKey     = $this->_request->getModuleKey();
            $this->_controllerKey = $this->_request->getControllerKey();
            $this->_actionKey     = $this->_request->getActionKey();
        }

        if (null !== $this->_dispatcher) {
            $this->_defaults += array(
                $this->_controllerKey => $this->_dispatcher->getDefaultControllerName(),
                $this->_actionKey     => $this->_dispatcher->getDefaultAction(),
                $this->_moduleKey     => $this->_dispatcher->getDefaultModule()
            );
        }

        $this->_keysSet = true;
    }

    /**
     * Matches a user submitted path. Assigns and returns an array of variables
     * on a successful match.
     *
     * If a request object is registered, it uses its setModuleName(),
     * setControllerName(), and setActionName() accessors to set those values.
     * Always returns the values as an array.
     *
     * @param string $path Path used to match against this routing map
     * @return array An array of assigned values or a false on a mismatch
     */
    public function match($request)
    {
        $this->_setRequestKeys();
        
        $values = array();
        $params = array();
             	
        $query  = trim($request->getPathinfo(),self::URI_DELIMITER);
        $query  = $this->_decodeQuery($query);
                
        $values = $request->parseQueryString($query);
        
        if ($this->_dispatcher && $this->_dispatcher->isValidModule($values[$this->_moduleKey])) {
        	$this->_moduleValid = true;
        }

        $this->_values = $values;
                
        /* module/controller/action e default ro dar soorati ke az path extract nashode boodand
         * az dispatcher gerefte va jaaii gozin mikonim
         */
        return $this->_values + $this->_defaults;
    }

    /**
     * Assembles user submitted parameters forming a URL path defined by this route
     *
     * @param array $data An array of variable and value pairs used as parameters
     * @param bool $reset Weither to reset the current params
     * @return string Route path with user submitted parameters
     */
    public function assemble($data = array(), $reset = false, $encode = true)
    {
        if (!$this->_keysSet) {
            $this->_setRequestKeys();
        }

        // merge user submitted params with current route params
        $params = (!$reset) ? $this->_values : array();
        
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $params[$key] = $value;
            } elseif (isset($params[$key])) {
                unset($params[$key]);
            }
        }
        // default shaamel e default module/controller/action az dispatcher ast
        $params += $this->_defaults;
                 
        /* if ($encode) {
            $routeKeys = array($this->_moduleKey,$this->_controllerKey,$this->_actionKey);
            foreach ($params as $key=>$val) {
                if (! in_array($key, $routeKeys)) {
                    $params[$key] = urlencode($val);
                }
            }
        } */

        $url = $this->_request->isOnAdminArea().self::URI_DELIMITER.$this->_encodeQuery(http_build_query($params));
           
        return $url;

    }
    
    protected function _decodeQuery($query)
    {
        return urldecode(base64_decode($query));
    }
    
    protected function _encodeQuery($query)
    {
        return base64_encode(urlencode($query));
    }

    /**
     * Return a single parameter of route's defaults
     *
     * @param string $name Array key of the parameter
     * @return string Previously set default
     */
    public function getDefault($name) {
        if (isset($this->_defaults[$name])) {
            return $this->_defaults[$name];
        }
    }

    /**
     * Return an array of defaults
     *
     * @return array Route defaults
     */
    public function getDefaults() {
        return $this->_defaults;
    }
    
}
