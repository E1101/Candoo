<?php
abstract class Util_Html_Decorator
{
    /**
     * HTML tag to use
     * @var string
     */
    protected $_tag;
        
    /**
     * Decorator attribs
     * @var array
     */
    protected $_options = array();
    		
	protected $_nested = array();       // content i ke daroon e in widget gharaar migirad, mitavaanad matn va yaa dataSource baashad
	
	public function __construct($content=null,$options=null)
	{		
		if ($content != null) {
			$this->nest($content);
		}
		
		if ($options != null) {
			$this->setOptions($options);
		}
	}
			
	public function render() 
	{
		$tag = $this->getTag();
		$attribs = $this->getOptions();
		
		$content='';
		foreach ($this->getNested() as $node) {
			$content .= $node;
		}
		
		// agar generate kardan e <tag></tag> disable bood
		/**
		 * @see self::disableTag();
		 */
		if ($this->getOption('disableTag')) {
			return $content;
		}
		
        return $this->_getOpenTag($tag, $attribs)
             . $content
        	 . $this->_getCloseTag($tag);
        	 
	}
	public function __toString() { return $this->render(); }
	
	/**
	 * Yek content daroon e widget gharaar midahad
	 * @param string|Candoo_Widget $content
	 * @throws Exception
	 */
	public function nest($content)
	{
		$this->_import($content);
		
		return $this;
	}
	
	/**
	 * Content i ke baayad namaaiesh daade shavad raa import mikonad.
	 * >> Hadaf e nahaaii in method in ast ke objecti daashte baashim
	 *    ke betavaanad __toString shavad.
	 *    khoob ast ke az widget haaye koochak tar e nest shode estefaade kard
	 *    yaaa nahaaiat yek string
	 * 
	 * exp. baraaie ul li, yek array migirad vaa aan raa be widget haaye
	 * ul->nest(li) tabdil mikonad.
	 * 
	 * @param unknown_type $content
	 * @throws Exception
	 */
	public function _import($content)
	{
		if (is_string($content)) {
		} elseif($this->_isPrintable($content) ) {
		} else {
			throw new Exception('content array must contain a string or __toString able object');
		}
		
		array_push($this->_nested, $content);
	}
	
	/**
	 * Mohtaviaate daroon e widget raa bar migardaanad
	 * 
	 */
	public function getNested()
	{
		return $this->_nested;
	}	

    /**
     * Set tag to use
     *
     * @param  string $tag
     * @return Zend_Form_Decorator_HtmlTag
     */
    public function setTag($tag)
    {
        $this->_tag = $tag;
        return $this;
    }

    /**
     * Get tag
     *
     * If no tag is registered, either via setTag() or as an option, uses 'div'.
     *
     * @return string
     */
    public function getTag()
    {
        if (null === $this->_tag) {
        	$this->setTag('div');
        }

        return $this->normalizeTag($this->_tag);
    }
    
    public function disableTag()
    {
    	$this->setOption('disableTag', true);
    	return $this;
    }
    
    public function enableTag()
    {
    	$this->setOption('disableTag', false);
    	return $this;
    }
    
    /**
     * Set attributes
     *
     * @param  array $options
     * @return Zend_Form_Decorator_Abstract
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
        return $this;
    }
    
    /**
     * Retrieve options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }
    
    /**
     * Get option
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption($key)
    {
        $key = (string) $key;
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }

        return null;
    }
    
    /**
     * Set option
     *
     * @param  string $key
     * @param  mixed $value
     * @return Zend_Form_Decorator_Abstract
     */
    public function setOption($key, $value)
    {
        $this->_options[(string) $key] = $value;
        return $this;
    } 
  
	   
    /**
     * Normalize tag all lowercase.
     *
     * @param  string $tag
     * @return string
     */
    protected function normalizeTag($tag)
    {
        return strtolower($tag);
    }   
    
    /**
     * Convert options to tag attributes
     *
     * @return string
     */
    protected function _htmlAttribs(array $attribs)
    {
    	// deprecate unwanted options to use in htmTag attrib
    	// disable tag option
    	unset($attribs['disableTag']);
    	
        $xhtml = '';
        foreach ((array) $attribs as $key => $val) {
            $key = htmlspecialchars($key, ENT_COMPAT,'UTF-8');
            if (is_array($val)) {
                   $val = implode(' ', $val);
            }
            
            $val    = htmlspecialchars($val, ENT_COMPAT,'UTF-8');
            $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }
    
    /**
     * Get the formatted open tag
     *
     * @param  string $tag
     * @param  array $attribs
     * @return string
     */
    protected function _getOpenTag($tag, array $attribs = null)
    {
        $html = '<' . $tag;
        if (null !== $attribs) {
            $html .= $this->_htmlAttribs($attribs);
        }
        $html .= '>';
        return $html;
    }

    /**
     * Get formatted closing tag
     *
     * @param  string $tag
     * @return string
     */
    protected function _getCloseTag($tag)
    {
        return '</' . $tag . '>';
    }
    
    /**
     * Tashkhis midahad ke aayaa $obj ghaabeliat e tabdil be matn 
     * __toString raa daarad ?
     * 
     * @param mixed $obj
     */
    protected function _isPrintable($obj) 
    {
    	return is_object($obj) && method_exists($obj, '__toString');
    }
    
    	
}