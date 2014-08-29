<?php
class moduleTest_Model_Table_Test extends Candoo_Db_Table_Abstract
{
    protected $_name = 'test';
    
    protected $_behaviors = array (
    	'translate' => array(
    		// agar in behavior jaaie digari be joz Candoo_Lib_Db_Table_Behavior ast
    		'setPrefixNamespace' => 'cLocali_Model_Table_Behavior',
    	
    		// parameter haaye morede estefaade ie in behavior ke be construct ersaal mishavad
    		//'locale' => 'en', // set class locale, if not get current locale from locale resource
    		'translationFields' => 'test_title', // field haaee az in table ke baayad translate shavand
    		//'translationTable' => 'cLocali_Model_Table_I18n', // agar mikhaahid az table db digari baraaie translate estefaade konid
    	)
    );
    
    
}