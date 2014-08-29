<?php
/**
 * Tinymce Html Editor form element
 *
 */
class cForm_Lib_Element_Tinymce extends Zend_Form_Element_Xhtml
{
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'formTextarea';
    
    public function init()
    {
        $this->setAttrib('id', 'tinymce_'.uniqid());
    }
    
    public function render($view = null)
    {
        // hengaame render script haaie in form raa niz attach mikonad
        $this->addDecorator('HeadScripts',array(
        	//'Stylesheet' => '* {font-size:12px; }',
        	'Scripts'     => array(
        		Candoo_Assets::getURL('candoo/js').'/tinymce/tiny_mce.js',
        		"tinyMCE.init({
        			theme : 'advanced',
        			plugins : 'autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
        			theme_advanced_buttons1 : 'newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
        			theme_advanced_buttons2 : 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor,|,preview',
        			theme_advanced_buttons3 : 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen',
        			theme_advanced_buttons4 : 'insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage',
        			theme_advanced_toolbar_location : 'top',
        			theme_advanced_toolbar_align : 'left',
        			theme_advanced_statusbar_location : 'bottom',				
					width : '590',
					elements : '".$this->getName()."',
        	
        			theme_advanced_toolbar_location: 'top',
        			theme_advanced_toolbar_align: 'left',
        			mode: 'exact',
        			content_css: '".$this->view->APP_BASEPATH."themes/tehran-fut/merged.css',
        			relative_urls : false,
        			remove_script_host : true,
        			document_base_url : '".$this->view->APP_BASEPATH."',
        			convert_urls : true,
        		});	
        		",
        	),    
        ));
        
        return parent::render($view);
    }
}
