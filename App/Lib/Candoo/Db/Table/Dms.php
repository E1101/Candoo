<?php
class Candoo_Db_Table_Dms extends Candoo_Db_Table_Abstract
{
    const NAMESPAC   = 'namespace';
    
    protected $_name = 'candoo_dms_pages';
    
    /**
     * Moshkhas konandeie daste bandi e page haa ham mitavaanad baashad
     * masalan news | category::1 
     * 
     * be in soorat mitavaanim dar search page haaaie ham gorooh raa load konim
     */
    protected $_namespace;
        
    /**
     * Class e table-i- ke node haaye field haa ro negah midaarad
     *
     * @var Candoo_Db_Table_Abstract
     */
    protected $_nodesTable = 'Candoo_Db_Table_Dms_Node';
    
    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Abstract::setOptions()
     */
    public function setOptions(Array $options)
    {
        foreach ($options as $key => $value) {
        	switch ($key) {
        		case self::NAMESPAC:
                    $this->setNamespace($value);
                    break;
        	}
        }

        parent::setOptions($options);
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Abstract::_setup()
     */
    protected function _setup()
    {
        parent::setRowClass('Candoo_Db_Table_Dms_Row');
        
    	$this->_setupDatabaseAdapter();
    	$this->_setupTableName();
    	
    	// name space be shekli be onvaane daste bandi e page haa niz be kaar miravad
    	// ke baraaie har class e table unique dar nazar gerefte mishavad
    	// hengaame select in namespace be onvaane shart e where ezaafe mishavad
    	$this->_setupNamespace();
    }
    
    /**
     * You can overwrite this for your own table
     */
    protected function _setupNamespace()
    {
        $namespace = new Zend_Filter_Word_SeparatorToCamelCase('_');
        $namespace = $namespace->filter(get_class($this));
        
        $this->setNamespace($namespace);
    }
    
    /**
     * Object e Class e Table-i- ke node haaa raa negah midaarad
     *
     * @param string | Zend_Db_Table_Abstract $class
     */
    public function setNodesTableClass($class)
    {
    	if (is_object($class))
    	{
    		if (!$class instanceof Zend_Db_Table_Abstract) {
    			throw new Zend_Exception('Table Class must instance of Zend_Db_Table_Abstract');
    		}
    
    		$class = get_class($class);
    	}
    	elseif(!is_string($class)) {
    		throw new Zend_Exception('Invalid table class provided. must be string or object instance of Zend_Db_Table_Abstract');
    	}
    
    	$this->_nodesTable = $class;
    
    	return $this;
    }
    
    /**
     * Object e Class e Table-i- ke node haaa raa negah midaarad,
     * bar migardaanad
     *
     * @throws Zend_Exception
     * @return Ambigous <Zend_Db_Table_Abstract>
     */
    public function getNodesTableClass()
    {
    	$class = $this->_nodesTable;
    
    	if (! class_exists($class,true)) {
    		throw new Zend_Exception('Table Class '.$this->_nodesTable.' not found.');
    	}
    
    	$class = new $class();
    	if (!$class instanceof Zend_Db_Table_Abstract) {
    		throw new Zend_Exception('Table Class must instance of Zend_Db_Table_Abstract');
    	}
    
    	return $class;
    }
    
    /**
     * Baraaie table e asli e dms faghat data haaye marboot
     * be cols haaye table raa joda mikonad va baraaie insert
     * mifrestad
     */
	protected function _beforeInsert($data)
	{
	    if (!isset($data['page_namespace'])) {
	    	$data['page_namespace'] = $this->getNamespace();
	    }
	    
	    $data = $this->_getColsRelData($data);
	}
	
	/**
	 * Ba`d az Insert kardane Data dar table asli baaghi maande
	 * haa ro node haaye dms farz mikonim va aan haa ro dar jadval
	 * e node haa minevisim
	 */
	protected function _afterInsert($pk, $insertedData, $oldData)
	{
	    $nodesTable = $this->getNodesTableClass();
	    
	    $nodes = array_diff_key($oldData, $insertedData);
	    foreach ($nodes as $caption => $content) {
	    	$newRow = $nodesTable ->createRow();
	    	$newRow ->foreign_key = $pk;
	    	$newRow ->caption = $caption;
	    	$newRow ->content = $content;
	    	$newRow->save();
	    }
	}
	
	/**
	 * Collect base table fields for update
	 */
	protected function _beforeUpdate($data, $where)
	{
	    $data = $this->_getColsRelData($data);
	}
	
	/**
	 * Update Nodes for a row
	 */
	protected function _afterUpdate($affected, $updatedData, $oldData, $where)
	{
	    $data = array_diff_key($oldData, $updatedData);	    
	    if (empty($data)) {
	    	return;
	    }
	    
	    $pkField = current($this->info(Zend_Db_Table::PRIMARY));
	    
	    $nodesTable = $this->getNodesTableClass();
	    
	    $rows = $this->fetchAll($where);
	    	    
	    $i = 0;
	    foreach ($rows as $row) {
	    	// increase affected row
	    	$i++;

	    	// delete previous node data
	    	$where = $this->getAdapter()->quoteInto('foreign_key = ?', $row->$pkField);
	    	$nodesTable->delete($where);
	    
	    	// insert new node data
	    	foreach ($data as $caption => $content) {
	    		$newRow = $nodesTable ->createRow();
	    		$newRow ->foreign_key = $row->$pkField;
	    		$newRow ->caption = $caption;
	    		$newRow ->content = $content;
	    		$newRow->save();
	    	}
	    }
	    
	    if ($affected == null) {
	        $affected = $i;
	    }
	}
	
	/**
	 * Delete nodes of pages form nodes table
	 */
	protected function _beforeDelete($where)
	{
	    $pkField = current($this->info(Zend_Db_Table::PRIMARY));
	    
	    $rows = $this->fetchAll($where);
	    $ids  = array();
	    foreach ($rows as $row) {
	    	$ids[] = $row->$pkField;
	    }
	    
	    $wr = $this->getAdapter()->quoteInto('foreign_key IN(?)', $ids);
	    
	    $dependTable = $this->getNodesTableClass();
	    $dependTable->delete($wr);
	}
	
	protected function _afterFetch($data, $select)
	{
	    $pkField = current($this->info(Zend_Db_Table::PRIMARY));
	     
	    foreach ($data as $i => $arr)
	    {
	    	/*
	    	 * array(4) {
	    	[0] => array(4) {
	    		["node_id"] => string(2) "40"
	    		["page_id"] => string(1) "4"
	    		["caption"] => string(7) "content"
	    		["content"] => string(45) "<p>This is content of this page as a node</p>"
	    	}
	    	[1] => array(4) {
	    		["node_id"] => string(2) "41"
	    	...
	    	*/
	    	$nodesTable = $this->getNodesTableClass();
	    	$nodes 		= $nodesTable->fetchAll(
	    					$nodesTable->select()
	    						->where('foreign_key = ?',$arr[$pkField])
	    	)->toArray();
	    
	    	/* Tabdil as form e bala be in shekl
	    	 * array(4) {
	    	["content"] => string(45) "<p>This is content of this page as a node</p>"
	    	["image"] => string(33) "http://localhost/img/image_no.jpg"
	    	["grade"] => string(1) "A"
	    	["is_active"] => string(1) "0"
	    	}
	    	*/
	    	$adData = array();
	    	foreach ($nodes as $node) {
	    		$adData[$node['caption']] = $node['content'];
	    	}
	    		
	    	$data[$i] = array_merge($data[$i],$adData);
	    }
	}
        
    public function setNamespace($namespace)
    {
    	$this->_namespace = $namespace;
    
    	return $this;
    }
    
    public function getNamespace()
    {
    	return $this->_namespace;
    }
    
}
