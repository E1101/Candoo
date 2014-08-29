<?php
class cForm_Lib_Decorator_FormElements extends Zend_Form_Decorator_Abstract
{
    /**
     * can use this keys as options:
     * boolean 'useTemplateDecorators' | aaayaa hengaame render e element haa az decorator e template estefaade konad
     * string  'viewDecorator'         | yek masir ke mahalle negah daari e decorator view haaye form ast mesle folder e template 
     * 
     * @param array $options
     */
    public function __construct($options = null)
    {
    	parent::__construct($options);    
    }  
    
    /**
     * Render form elements
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $form    = $this->getElement();
        if ((!$form instanceof Zend_Form) && (!$form instanceof Zend_Form_DisplayGroup)) {
            return $content;
        }
        
        $belongsTo      = ($form instanceof Zend_Form) ? $form->getElementsBelongTo() : null;
        $elementContent = '';
        $displayGroups  = ($form instanceof Zend_Form) ? $form->getDisplayGroups() : array();
        $separator      = $this->getSeparator();
        $translator     = $form->getTranslator();
        $items          = array();
        $view           = $form->getView();
        
        foreach ($form as $item) {
            $item->setView($view)
                 ->setTranslator($translator);
            if ($item instanceof Zend_Form_Element) {
                foreach ($displayGroups as $group) {
                    $elementName = $item->getName();
                    $element     = $group->getElement($elementName);
                    if ($element) {
                        // Element belongs to display group; only render in that
                        // context.
                        continue 2;
                    }
                }
                $item->setBelongsTo($belongsTo);
            } elseif (!empty($belongsTo) && ($item instanceof Zend_Form)) {
                if ($item->isArray()) {
                    $name = $this->mergeBelongsTo($belongsTo, $item->getElementsBelongTo());
                    $item->setElementsBelongTo($name, true);
                } else {
                    $item->setElementsBelongTo($belongsTo, true);
                }
            } elseif (!empty($belongsTo) && ($item instanceof Zend_Form_DisplayGroup)) {
                foreach ($item as $element) {
                    $element->setBelongsTo($belongsTo);
                }
            }
            
            // use template decorator for decorate element ```````````````````````````````````````````| 
            $template = Candoo_App_Resource::get('template',false);
            if ($template && $this->getOption('useTemplateDecorators')) 
            {
            	$pathTheme  = $template->getDir() .DS. 'views' .DS. 'forms';
            	$this->setViewDecorator($pathTheme);
            }
            
            // agar decorator haa mojood baaashad dar folder e viewDecorator 
            // decorator e item raa be viewScript ta`ghir midahad 
            $this->_defineDecorator($item);
            
            $items[] = $item->render();
            
            if (($item instanceof Zend_Form_Element_File)
                || (($item instanceof Zend_Form)
                    && (Zend_Form::ENCTYPE_MULTIPART == $item->getEnctype()))
                || (($item instanceof Zend_Form_DisplayGroup)
                    && (Zend_Form::ENCTYPE_MULTIPART == $item->getAttrib('enctype')))
            ) {
                if ($form instanceof Zend_Form) {
                    $form->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
                } elseif ($form instanceof Zend_Form_DisplayGroup) {
                    $form->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
                }
            }
        }
        $elementContent = implode($separator, $items);

        switch ($this->getPlacement()) {
            case self::PREPEND:
                return $elementContent . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $elementContent;
        }
    }
    
    /**
     * Merges given two belongsTo (array notation) strings
     *
     * @param  string $baseBelongsTo
     * @param  string $belongsTo
     * @return string
     */
    protected function mergeBelongsTo($baseBelongsTo, $belongsTo)
    {
    	$endOfArrayName = strpos($belongsTo, '[');
    
    	if ($endOfArrayName === false) {
    		return $baseBelongsTo . '[' . $belongsTo . ']';
    	}
    
    	$arrayName = substr($belongsTo, 0, $endOfArrayName);
    
    	return $baseBelongsTo . '[' . $arrayName . ']' . substr($belongsTo, $endOfArrayName);
    }
    
    /**
     * Yek folder raaa baraaie estefaadeie form haa hengaame render baraaie decorate 
     * moa`refi mikonad
     * 
     * dar in folder decorate element haa be in soorat gharaar daarad:
     * [elementType].phtml
     * password.phtml
     * 
     * @param string $path | path to decorator folder
     */
    public function setViewDecorator($path)
    {
        $path = rtrim($path,DS);
        
        $this->setOption('viewDecorator', $path);
        
        return $this;
    }
    
    protected function _defineDecorator(Zend_Form_Element $element)
    {
        // agar haavie meghdaari baashad path marboot be viewDecorator haast
        $path       = $this->getOption('viewDecorator');
        if (!$path) {
            return ;
        }

        $view    = $this->getElement()->getView();
        
        // masalan baraaie _Form_Element_[Tinymce] <- be donbaale in migardad
        $scriptFile = $this->getScriptByElementType($element,$path);
        if ($scriptFile) {         
            $view->addScriptPath($path);
            $element ->setDecorators(array(
            		array('ViewScript', array(
            				'viewScript' => $scriptFile,
            		))
            ));
        } 
        // dar soorati ke yek form element az form element haaie digari tolid shavad
        // exp. tinymce az text agar file i be naame form element e asli (tinymce) nadaashtim
        // az helper e aaan estefaade mikonad
        elseif($scriptFile = $this->getScriptByHelper($element,$path)) {
            $view->addScriptPath($path);
            $element ->setDecorators(array(
            		array('ViewScript', array(
            				'viewScript' => $scriptFile,
            		))
            ));
        }
        // be donbaale decorator e default ke baraaie generate kardane aghlab e element 
        // haa estefaade mishavad migardad
        else {
            $scriptFile = '_default'.$viewSuffix;
            if (file_exists($path.DS.$scriptFile)) {
            	$view->addScriptPath($path);
            	$element ->setDecorators(array(
            			array('ViewScript', array(
            					'viewScript' => $scriptFile,
            			))
            	));
        	}
        }
    }
    
    /**
     * Dar folder e viewDecorator haa dar soorati ke file baraaie in element vojood daashte baashad
     * aaan raa bar migardaanad
     */
    protected function getScriptByElementType($element,$path)
    {
        if (! $element instanceof Zend_Form_Element) {
            return false;
        }
        
        $viewSuffix = '.'.Candoo_App_Resource::get('viewRenderer')->getViewSuffix();
        
        $type = end(explode('_',$element ->getType()));
        $file = strtolower($type).$viewSuffix;
        if (file_exists($path.DS.$file)) {
            return $file;
        }
        
        return false;
    }
    
    /**
     * Mavaaghe`ee pish miaaiad ke yek form element masalan tinymce baaa ta`ghir bar rooie ye
     * form element digar be vojood miaaaiad, dar in soorat ehiaaj daarim ke viewDecorator e
     * helper in type az form raa render konim
     * 
     */
    protected function getScriptByHelper($element,$path)
    {
    	if (! $element instanceof Zend_Form_Element) {
    		return false;
    	}
    
    	$viewSuffix = '.'.Candoo_App_Resource::get('viewRenderer')->getViewSuffix();
    
    	$type = str_replace('form', '', $element ->helper);
    	$file = strtolower($type).$viewSuffix;
    	if (file_exists($path.DS.$file)) {
    		return $file;
    	}
    
    	return false;
    }
}
