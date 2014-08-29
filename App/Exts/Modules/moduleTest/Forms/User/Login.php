<?php
class moduleTest_Forms_User_Login extends cForm_Lib_Form
{
    public function init()
    {
    	$username = new Zend_Form_Element_Text('username');
    	$username->class = 'formtext';
    	$username->setLabel('Username');
    	$username->setRequired();
    
    	$password = new Zend_Form_Element_Password('password');
    	$password->class = 'formtext';
    	$password->setLabel('Password');
    	$password->addValidator(new cForm_Lib_Validator_Confirm('username'));
   	
    	//$htmlEditor = new cForm_Lib_Element_Tinymce('desc');
    	//$htmlEditor ->setAttrib('id','this_is_id');
 
    	$submit = new Zend_Form_Element_Submit('login');
    	$submit->class = 'formsubmit';
    	$submit->setValue('Login');
    
    	$this->addElements(array(
    			$username,
    			$password,
    			//$htmlEditor,
    			$submit
    	));
    }
}