<?php
class cForm_Helpers_View_JQuery_JQValidation extends Zend_View_Helper_HtmlElement
{
    public function JQValidation()
    {
        $jQuery = Candoo_App_Resource::get('jQuery');
        $jQuery->enable();
        
        $jQuery->addJavascriptFile(Candoo_Assets::getURL('candoo/js/jquery.validate').'/jquery.validate.min.js');
    }
}
