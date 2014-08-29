<?php
class moduleTest_Model_Table_Dms extends Candoo_Db_Table_Dms
{ 
    protected $_behaviors = array (
    	'translate' => array(
    		'setPrefixNamespace' => 'cLocali_Model_Table_Behavior',    	
    		'translationFields'  => array('page_title','content')
    	)
    );
    
    
}