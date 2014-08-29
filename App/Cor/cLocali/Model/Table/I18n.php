<?php
class cLocali_Model_Table_I18n extends Candoo_Db_Table_Abstract
{
    protected $_name = 'i18n';
    
    /* protected $_referenceMap = array(
    	'BaseTable' => array(
    		'columns' => 'foreign_key',
    		'refTableClass' => 'Candoo_Db_Table_Translatable',
    		'refColumns'	=> 'test_id',
    		'onDelete' 	    => self::CASCADE,
    		'onUpdate'		=> self::RESTRICT,
    	)
    ); */
}
