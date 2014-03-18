<?php
/**
 * TODO : class e template haa baayad az yek class e abstract Candoo_template
 * Extend shode baashand.
 * faghat nahveie dastresi be file haa va serv kardane aanhaa fargh mikonad
 * 
 * @author root
 *
 */
class Templates_Lib_Template
{
    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Candoo_App
     */
    protected static $_instance = null;
    
    /**
     * default, template pishfarze barnaame ast ke dar soorati ke templat i mojood nabood
     * az in template be onvaane pish farz estefaade mikonad
     *
     * @var array
     */
    protected $_templates = array();
    
    /**
     * Enforce singleton; disallow cloning
     *
     * @return void
     */
    private function __clone() { }
    
    public function __construct()
    {
        
    }
    
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function start()
    {
    	$this->enableMVC();
    
    	// store template resource to use from other classes
    	Candoo_App_Resource::set('template', self::getInstance());
    }
    
    /**
     * Template haaye marboot be har bakhsh site injaa ta`rif mishavad
     *
     * array(2) {
     ["frontend"] => string(11) "entrepeneur"
     ["backend"]  => string(11) "simpleAdmin"
     }
     * @param array $templates
     */
    public function setTemplates(array $templates)
    {
    	foreach ($templates as $area => $template) {
    		$this->setTemplate($template, $area);
    	}
    	 
    	return $this;
    }
    
    /**
     * template marboot be namaaiesh e site raa ta`rif mikonad
     *
     * @param string $area | can be a "frontend" or "backend"
     * @param string $name | template name
     */
    public function setTemplate($name, $area=null)
    {
    	$area = ($area == null) ? $this->getCurrentAreaname() : $area;	
    	$this->_templates[$area] = $name;
    	
    	return $this;
    }
       
    /**
     * Agar dar ghesmat e adminpanel baashim backend va dar ghere in soorat frontend 
     * raa bar migardaanad
     */
    public function getCurrentAreaname()
    {
        $req = Candoo_App_Resource::get('request');
        $area = ($req->isOnAdminArea() === false) ? 'frontend' : 'backend';
        
        return $area;
    }
    
    /**
     * Tavasote in method mitavan be object e template yek area digar dastresi
     * daasht, masalan hengaami ke dar admin panel hastim mitavaanim template
     * frontend i ke in darkhaast daarad raa bebinim yaaa be file haaie aan dastresi daashte
     * bashim 
     * 
     * @param string $name
     * @return Templates_Lib_Template_Object:
     */
    public function getArea($name)
    {
        return $this->_getAreaObject($name);
    }
    
    protected function _getAreaObject($area=null)
    {
        if ($area == null) {
            $area = $this->getCurrentAreaname();
        }
       
        if (!isset($this->_templates[$area])) {
            throw new Exception('template not defined for area '.$area);
        }
        
        if (!is_object($this->_templates[$area])) {
            $this->_templates[$area] = new Templates_Lib_Template_Object($this->_templates[$area]);
        }
        
        return $this->_templates[$area];
    }
    
    // `````````````````````````````````````````````````````````````````````````````````````````````````|
    // Proxy to Templates_Lib_Template_Object
    
    /**
     * Proxy through current area object 
     * 
     * @param string $method
     * @param array $args
     */
    public function __call($method,$args)
    {
        $obj = $this->_getAreaObject();
        
        if (empty($args)) {
            return $obj->$method();
        } else {
            return call_user_func_array(array($obj,$method), $args);
        }
    }
    
    public function __set($key,$value)
    {
        $this->_getAreaObject()->$key = $value;
    }
    
    public function __get($key)
    {
        return $this->_getAreaObject()->$key;
    }
    
    public function __isset($key)
    {
        $obj = $this->_getAreaObject();
        return isset($obj->$key);
    }
    
    public function __unset($key)
    {
        $obj = $this->_getAreaObject();
        unset($obj->$key);
    }
    
}
