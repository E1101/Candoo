<?php
class cForm_Lib_Form extends Zend_Form
{
    protected $_enabledJQuery;
    
    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('cForm_Lib_Element_', self::_getDir() .DS. 'Element', 'element');
        
        $this->addElementPrefixPath('cForm_Lib_Decorator_', self::_getDir() .DS. 'Decorator','decorator');
        $this->addElementPrefixPath('cForm_Lib_Validator_', self::_getDir() .DS. 'Validator','validate');
        
        // ezaafe kardane filter haaye candoo baraaaie estefaade dar formhaaa 
        $this->addElementPrefixPath('Candoo_Filter_', APP_DIR_LIBRARIES .DS. 'Filter','filter');
        // ezaafe kardane validator haaye candoo baraaaie estefaade dar formhaaa
        $this->addElementPrefixPath('Candoo_Validator_', APP_DIR_LIBRARIES .DS. 'Validator','validate');
     
        $translator = Candoo_App_Resource::get('translator',false);
        if ($translator) {
            /**
             * @note ehtiaaji be set kardane translator nist...
             * 		 translator az Registry gerefte mishavad
             */
        }
        
        // force each form to have a id
        // can be used on jquery validation and so ...
        $this->setAttrib('id', get_class($this).'_'.uniqid());
                
        parent::__construct($options);
    }
    
    /**
     * Load the default decorators
     * 
     * @note: dar hengaame render e form in decorator haa mojebe saakhte shodane khorooji ast
     *
     * @return Zend_Form
     */
    public function loadDefaultDecorators()
    {
    	if ($this->loadDefaultDecoratorsIsDisabled()) {
    		return $this;
    	}
    
    	$decorators = $this->getDecorators();
    	if (empty($decorators)) {
    	    /**
    	     * this will out put something like : 
    	     */
    	    /**
    	     * OUTPUT form elements added to form
    	     * 
    	     * <label for="username">password:</label>
			   <dd id="username-element">
					<input type="password" name="username" id="username" value="">
				</dd>
				...
			*/
    	    // baraaie inke hengaame render shodan form element haa az decorator scriptView samte template estefaade konam
    	    // yek extend az decorator e asli ijaad kardam
    		$this->addDecorator(new cForm_Lib_Decorator_FormElements(array('useTemplateDecorators' => true)))
    		//$this->addDecorator('FormElements')
    		/**
    		 * FormElements haa raa daroon tag e html gharaar midahad
    		 * 
    		 * <div class="zend_form">[formElements]</div>
    		 */
    		     //->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'zend_form'))
			/**
			 * Tag e Form raa ezaafe mikonad
			 * 
			 * <form enctype="application/x-www-form-urlencoded" action="" method="post">
			 * 		[htmlTag]
			 * 			[formElements]
			 * 		[/htmlTag]
			 * </form>
			 */    		     
    		     ->addDecorator('Form')
    		;
    		
    		/**
    		 * jQuery validation decorator
    		 */
    		if ($this->isEnabledJquery()) {
    			$this->enableJquery();
    		}
    	}
    	
    	return $this;
    }
    
    /**
     * Khaasiate jQuery raa hengaame render e form enable mikonad
     * yek seri decorator jQuery be form ezaafe mikonad
     * 
     * @param boolean $flag
     */
    public function enableJquery($flag = true)
    {
        $this->_enabledJQuery = $flag;
        
        // element Decorator e marboot be JQuery raa hazf mikonad
        if (!$flag) {
            // remove jQuery validator
            $this->removeDecorator('cForm_Lib_Decorator_AjaxifyJQuery');
        } else {
            if (! $this->getDecorator('cForm_Lib_Decorator_AjaxifyJQuery')) {
                $this->addDecorator(new cForm_Lib_Decorator_AjaxifyJQuery());
            }
        }
        
        return $this;
    }
    
    public function isEnabledJquery()
    {
        // agar vazi`te jquery hanooz be class moa`refi nashode bood aaan raa az candoo migirad
        if ($this->_enabledJQuery == null) {
            $this->enableJquery(Candoo_JQuery::isEnabledView());
        }
        
        return $this->_enabledJQuery;
    }
    
    protected static function _getDir()
    {
        return realpath(dirname(__FILE__));
    }
    
}
?>