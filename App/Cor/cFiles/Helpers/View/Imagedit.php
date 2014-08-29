<?php
class cFiles_Helpers_View_Imagedit extends Zend_View_Helper_HtmlElement
{
    /**
     * Class e saazandeie tasvir
     * 
     * @var cFiles_Lib_Imagedit
     */
    protected $_class;
    
    /**
     * @var string Base URL for images
     */
    protected $_useApi;
    
    /**
     * hengaame estefaade az api har dastoor be soorate
     * ?rotate=15,true&... be url ezaafe mishavad in motaghaier haa
     * injaa zakhire mishavand 
     * 
     * @var array
     */
    protected $_apiParams = array();
    
    /**
     * Hengaame __construct option haa yaa setter method haaye in class hastand
     * va yaa dar gheire in soorat be onvaane html attr. haaie tag zakhire mishavand
     * <img alt='' ......
     * 
     */
    protected $_htmlAttrs = array();
    
    /**
     * Address e ax e load shode
     * 
     * @var strig
     */
    protected $_imageUrl;
 
    /**
     * @var string Default image format to use
     */
    protected $_format = 'jpg';
    
    /**
     * @var bool Whether or not to create an image tag
     */
    protected $_createTag = true;

    /**
     * Generate a link or image tag 
     *
     * @param mixed $image
     * @param array $options
     * @return void
     */
    public function imagedit($image = null, $options = array())
    {
        $this->_reset();
        
        /**
         * set image for class
         */
        // class e image mitavaanad file va http address raa bepazirad
        // agar image hich kodaam nabood pas farz mikonim ke yek address e relative e web ast /candoo/...
        if (!Zend_Uri::check($image) && !is_file($image)) {
            $image = Candoo_Uri::UrlRelToAbs($image);
        }
        
        /**
         * set default render method, data | api from cFiles config
         */
        $useApi = 'api';
        $config = Candoo_Module::getConfig('cFiles');
        if ($config) {
            $useApi = ($config->imagedit) ? ($config->imagedit->src) ? strtolower($config->imagedit->src) : 'api' : 'api';
        }
        $this->setUseApi($useApi == 'api');
           
        $this->setImage($image); 
        
        /**
         * setDefault format by filetype
         */
        $extension = strtolower(pathinfo($image,PATHINFO_EXTENSION));
        $this->setDefaultFormat($extension);
        
        $this->setOptions($options);
        
        return $this;
    }
    
    /**
     * reset parameters on each helper call
     */
    protected function _reset()
    {
        $this->_apiParams = array();
        $this->_htmlAttrs = array();
        $this->_createTag = true;
    }
    
    /**
     * be khaatere in ke setImage dar cunstructor gharaar daaarad
     * va inke method useApi ba`d faraakhaani mishavad az tarighe in
     * method hengaame ta`ghire ax address e image raa zakhire mikonim
     * 
     * @param string $image
     */
    public function setImage($image)
    {
        $this->getClass()->setImage($image);
        $this->_imageUrl = $image;
    }
    
    public function getOrginImageUrl()
    {
        return $this->_imageUrl;
    }
    
    protected function setOptions($options)
    {
        foreach ($options as $key=>$val) {
            $method_name = 'set'.ucfirst(strtolower($key));
            if (method_exists($this, $method_name) && $method_name != 'setOptions') {
                call_user_func_array(array($this,$method_name), $val);
            } 
            // store options as html attributes on tag <img alt='' ....
            else {
            	$this->_storeForHtmlAttr($key,$val);
            }
        }
    }
    
    public function __call($meth, $args)
    {
        $class = $this->getClass();
        if ( in_array($meth, array('hasFace','getWidth','getHeight')) ) {
            return call_user_func_array(array($class,$meth), $args);
        }
        
        if ( method_exists($class, $meth) ) {
            // agar dar haalat e api bood method va param ro zakhire mikonad ke be url ezaafe shavad
            if ($this->useApi()) {
                $this->_storeForApi($meth,$args);
            } else {
                call_user_func_array(array($class,$meth), $args);
            }	
        }
        
        return $this;
    }
    
    public function __toString()
    {
        /**
         * Agar creatTag = false baashad mohtaviaate image raa bar migardaanad
         */
        if (! $this->createTag()) {
            $format = $this->getDefaultFormat();
            $method = 'get'.strtoupper($format);
            
            return $this->getClass()->$method();
        }

        $options['src']    = $this->getSrc();
        $options          += $this->_htmlAttrs;
        
        // dar haalati ke az api estefaade mikonim ta`ghiraat dar ax ba`d az ejraa moshakhas mishavad
        if (! $this->useApi()) {
            // insert image width and height as attributes of tag
            $options['width']  = $this->getWidth();
            $options['height'] = $this->getHeight();
        }
          
        $tag = '<img' . $this->_htmlAttribs($options) . $this->getClosingBracket();
        return $tag;
    }
    
    /**
     * link to generated image
     */
    public function getSrc()
    {
        $format = $this->getDefaultFormat();
        $method = 'get'.strtoupper($format);
        
        /**
         * Agar baraaie namaaiesh tasvir az api estefaade mikard
         */
        if ($this->useApi()) {
        	$src = $this->_createApiSrc();
        } else {
        	$src = 'data:image/'.$format.';base64,'.base64_encode($this->getClass()->$method());
        }
        
        return $src;
    }
    
    // Options parameter ````````````````````````````````````````````````````````````````````````````|
    public function setFormat($val)
    {
        $this->setDefaultFormat($val);
        
        return $this;
    }
    
    public function setCreateTag($val = true)
    {
        $this->_createTag = $val;
        
        return $this;
    }
    
    public function setUseApi($val = true)
    {
        $this->_useApi = $val;
        
        return $this;
    }
    // ```````````````````````````````````````````````````````````````````````````````````````````````
    
    public function useApi()
    {
        return $this->_useApi;
    }
    
	/**
     * Should the helper create an image tag?
     *
     * @return bool
     */
    public function createTag()
    {
        return $this->_createTag;
    }
    
    public function getDefaultFormat()
    {
    	return $this->_format;
    }
    
    /**
     * Set default image format
     *
     * If set, this will set the default format to use on all images.
     *
     * @param  null|string $format
     * @return Zend_View_Helper_TinySrc
     * @throws Zend_View_Exception
     */
    public function setDefaultFormat($format = null)
    {
    	if (null === $format) {
    		return $this;
    	}
    
    	$format = strtolower($format);
    	if (!in_array($format, array('png', 'jpg'))) {
    		throw new Zend_View_Exception('Invalid format; must be one of "jpg" or "png"');
    	}
    	$this->_format = $format;
    	return $this;
    }
   
    
    /**
     * Dar hengaami ke az haalat e api estefaade mikonaim dastoorat ejraaa nemishavand
     * va faghat zakhire mishavand ke ba`d be url ezaafe shavad
     * 
     * @param string $meth | method e sedaa zade shode
     * @param array $args  | argument haaie method
     */
    protected function _storeForApi($meth,$args)
    {
        $this->_apiParams[$meth] = $args;
    }
    
    /**
     * Hengaame __construct option haa yaa setter method haaye in class hastand
     * va yaa dar gheire in soorat be onvaane html attr. haaie tag zakhire mishavand
     * <img alt='' ......
     * 
     * @param string $key
     * @param string $val
     */
    protected function _storeForHtmlAttr($key,$val)
    {
        $this->_htmlAttrs[$key] = $val;
    }
    
    /**
     * method haaie call shode ro az _apiParams mikhaanad va url raa baraaie namaaiesh ax dorost mikonad
     * 
     */
    protected function _createApiSrc()
    {
        $params['f'] = $this->getOrginImageUrl();
        $params['format'] = $this->getDefaultFormat();
        
    	foreach ($this->_apiParams as $meth => $args) {
    	    switch ($meth) {
    	        // naaame method e class  |   aanchiz ke api dar url mishnaasad
    	    	case 'cropBorder' 		  : $meth = 'cropborder'; break;	
    	    	case 'cropFace'   		  : $meth = 'Cropface';   break;
    	    	case 'setHeight'   		  : $meth = 'Height';	  break;
    	    	case 'setWidth'   		  : $meth = 'Width';	  break;
    	    	//case 'setDefaultFormat'   : $meth = 'Format';	  break;
    	    }
    	    
    	    array_walk($args, function(&$val,$key) use (&$args)  {
    	    	if (is_bool($val)) {
    	    	    $args[$key] = ($val) ? 'true' : 'false';
    	    	}
    	    });
   
    	    $params[$meth] = implode(',', $args); 
    	}
    	
    	return Candoo_App::getBasePath().'/srv/cFiles/Image?'.urldecode(http_build_query($params/* ,null,'&' */));
    }
    
    
    protected function getClass()
    {
        if ($this->_class == null) {
            $this->_class = new cFiles_Lib_Imagedit();
        }
        
        return $this->_class;
    }
    
    
    
    
    /**
     * Validate a dimension
     *
     * Dimensions may be integers, optionally preceded by '-' or 'x'.
     *
     * @param  string $dim
     * @return bool
     */
    protected function _validateDimension($dim)
    {
        if (!is_scalar($dim) || is_bool($dim)) {
            return false;
        }
        return preg_match('/^(-|x)?\d+$/', (string) $dim);
    }

    /**
     * Determine whether to use default dimensions, or those passed in options.
     *
     * @param  array $options
     * @return string
     */
    protected function _mergeDimensions(array $options)
    {
        if (!$this->_validateDimension($options['width'])) {
            return $this->_dimensions;
        }
        $dimensions = '/' . $options['width'];
        if (!$this->_validateDimension($options['height'])) {
            return $dimensions;
        }
        $dimensions .= '/' . $options['height'];
        return $dimensions;
    }
}
