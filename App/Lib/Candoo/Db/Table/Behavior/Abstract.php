<?php
abstract class Candoo_Db_Table_Behavior_Abstract
{
    /**
     * Table i ast ke in behavior raa sedaa zade ast
     * 
     * @var Zend_Db_Table_Abstract
     */
    protected $_table;
    
    public function __construct(array $options = array())
    {
        if (is_array($options) && !empty($options)) {
            $this->setOptions($options);
        }
        
        $this->init();
    }
    
    public function init() { }
    
    public function setOptions(array $options)
    {
        foreach ($options as $key => $val) {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($val);
            }
        }
        
        return $this;
    }
    
    final public function setTable(Zend_Db_Table_Abstract $table)
    {
       $this->_table = $table;
    }
    
    final public function getTable()
    {
        return $this->_table;
    }
    
    public function _beforeInsert(array $data) { }
    public function _afterInsert($pk,array $insertedData,array $oldData) { }
    
    public function _beforeUpdate(array $data, $where) { }
    public function _afterUpdate($affected, $updatedData, $oldData, $where) { }
    
    public function _beforeDelete($where) { }
    public function _afterDelete($affected, $where) { }
    
    public function _beforeFetch(Zend_Db_Table_Select $select) { }
    public function _afterFetch(array $data, Zend_Db_Table_Select $select) { }
    public function _beforeFetchReturn(array $data) { }
    
}
