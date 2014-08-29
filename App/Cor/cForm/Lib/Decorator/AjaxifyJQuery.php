<?php
class cForm_Lib_Decorator_AjaxifyJQuery extends Zend_Form_Decorator_Abstract
{
    public function __construct($options = null)
    {
        if (! Candoo_JQuery::isEnabledView()) {
            throw new Zend_Exception('jQuery view not enabled and I need it.');
        }
        
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
        $form = $this->getElement();
        $view = $form->getView();
        
        $jQuery = Candoo_App_Resource::get('view')->jQuery();
        $jQuery->enable();
        
        $jh = ZendX_JQuery_View_Helper_JQuery::getJQueryHandler();
        
        $form_id = $form->getAttrib('id');
        $jQuery->addOnload("
        	$jh('form#{$form_id} input').blur(function () {
        		var formElement = $jh(this);
        		doValidation(formElement);
        	});
        	
        	// run validation on each form element on submit
        	$jh('form#{$form_id}').submit(function () {
        		result = true;
        		 
				$jh('form#{$form_id} input').each(function () {
					//TODO baayad result haa ba ham and shavand ke dar natije ejaazeie ersaal e form raa midahad
					result = doValidation($(this));
				});
				
				return result;
        	});	
        ");
                
        $url = $view->url('cForm_validate_index');
        $jQuery->addJavascript("
        	function doValidation(formElement)
			{
        		var result;
        
				var url = '".$url."';
				
        		// tell browser that is json object
				var data = {};
				$jh('form#{$form_id} input').each(function () {
					data[$(this).attr('name')] = $(this).val();
				});
	
				$jh.post(url,data,function(resp){
					form_id = formElement.attr('id');					
					// faghat error message haaye marboot be form e element e blur shode namaaiesh shavad
					errorMessage = resp[form_id];
					
					// TODO result be onvaane yek motaghaiere global nist va set kardanesh dar injaa biroon az 
					//      in function natije`i nadaarad 
					if (typeof errorMessage !== 'undefined') {
						result = false;
					} else {
						result = true;
    				}
					
					putErrorHtml(errorMessage);			
					
				},'json');
				
				return result;
			}
        
        	function putErrorHtml(formErrors)
        	{
        		var o = '<div id=\"errors\" class=\"errors\">';
        		for (errorKey in formErrors) {
        			o += '<p style=\"color:red;\">'+formErrors[errorKey]+'</p>';
        		}
        		o += '</div>';
        
        		$jh('form#{$form_id} input#'+form_id).parent().find('.errors').remove();
        		$jh('form#{$form_id} input#'+form_id).parent().append(o);
        	}
        ");     
        
        return $content;  
    }
}
