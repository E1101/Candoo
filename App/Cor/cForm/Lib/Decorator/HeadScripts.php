<?php
class cForm_Lib_Decorator_HeadScripts extends Zend_Form_Decorator_Abstract
{
	protected $_placement = 'PREPEND';
	
	/**
	 * Stylesheets 
	 * 
	 */
	protected $_stylesheet = array();
	
	/**
	 * Script
	 *
	 */
	protected $_script = array();
	
	/**
	 * Script
	 *
	 */
	protected $_inlinescript = array();
	
    public function render($content)
    {
        if (null === ($element = $this->getElement())) {
            return $content;
        }
        if (!$view = $element->getView()) {
            return $content;
        }
        
        // attach head scripts to view `````````````````````````````````````````````````````| 
        $scripts = $this->getScripts();
        foreach ($scripts as $sc) {
            $mode = Zend_View_Helper_HeadScript::FILE;
            
            if (! Candoo_Uri::check($sc)) {
                $mode = Zend_View_Helper_HeadScript::SCRIPT;
            }
            
           $view->headScript($mode,$sc);
        }
        // `````````````````````````````````````````````````````````````````````````````````
        
        // attach head links to view `````````````````````````````````````````````````````|
        $scripts = $this->getStylesheets();
        foreach ($scripts as $sc) {
        	if (! Candoo_Uri::check($sc)) {
        		//$view->headLink()->appendStylesheet($sc);
        	} else {
        	    $view->headLink()->appendStylesheet($sc);
        	}        	
        }
        // `````````````````````````````````````````````````````````````````````````````````

        $inlinescripts = $this->getInlineScripts();
        if (!empty($inlinescripts)) {
            $inlinescripts = '<script type="text/javascript">'.implode('\r\n', $inlinescripts).'</script>';
            $content = $inlinescripts.$content;
        }
        
        return $content;
                
        /* $placement = $this->getPlacement();
        $separator = $this->getSeparator();
        
        switch ($placement) {
        	case 'APPEND':
        		return $content . $separator . $label;
        	case 'PREPEND':
        	default:
        		return $label . $separator . $content;
        } */ 
    }
    
    
    
    /**
     * Get scripts
     *
     */
    public function getScripts()
    {
    	if (empty($this->_script)) {
    		if (null !== ($scripts = $this->getOption('Scripts'))) {
    			$this->setScripts($scripts);
    			$this->removeOption('Scripts');
    		}
    	}
    
    	return $this->_script;
    }
    
    public function setScripts($script)
    {
        if (!is_array($script)) {
            $this->_script[] = $script;
        } else {
            $this->_script = $script;
        }
        
    	return $this;
    }
    
    /**
     * Script haee ast ke baayad daghighan ghabl az form va be soorat e inline baashad
     * (be head attach nemishavad va yaaa dar entehaaye body nemiaaiad)
     */
    public function getInlineScripts()
    {
        if (empty($this->_inlinescript)) {
        	if (null !== ($scripts = $this->getOption('inlineScripts'))) {
        		$this->setInlineScripts($scripts);
        		$this->removeOption('inlineScripts');
        	}
        }
        
        return $this->_inlinescript;
    }
    
    public function setInlineScripts($script)
    {
    	if (!is_array($script)) {
    		$this->_inlinescript[] = $script;
    	} else {
    		$this->_inlinescript = $script;
    	}
    
    	return $this;
    }
    
    
    /**
     * Get stylesheets
     *
     */
    public function getStylesheets()
    {
    	if (empty($this->_stylesheet)) {
    		if (null !== ($styles = $this->getOption('Stylesheet'))) {
    			$this->setStylesheets($styles);
    			$this->removeOption('Stylesheet');
    		}
    	}
    
    	return $this->_stylesheet;
    }
    
    public function setStylesheets($style)
    {
    	$this->_stylesheet = $style;
    }
}
?>