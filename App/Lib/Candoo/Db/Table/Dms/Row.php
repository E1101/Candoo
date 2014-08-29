<?php
class Candoo_Db_Table_Dms_Row extends Zend_Db_Table_Row_Abstract
{
    /**
     * Return nodes of a row 
     * 
     * @return Candoo_Dataset_Entity
     */
    public function getNodes()
    {
        $data = $this->toArray();
    	$cols = $this->getTable()->getCols();
    	
    	$nodes = array_diff_key($data,array_flip($cols));
    	
    	return new Candoo_Dataset_Entity($nodes);
    }
    
    /**
     * Return nodes name as an array
     *
     * @return array | naame node haaa [0] => "content"
     */
    public function getNodesName()
    {
    	return array_keys($this->getNodes()->toArray());
    }
    
    /**
     * baraaie field haaye ke mojood nist exception nadaarim
     * 
     * @see Zend_Db_Table_Row_Abstract::__get()
     */
    public function __get($columnName)
    {
    	$columnName = $this->_transformColumn($columnName);
    	    	
    	if (!array_key_exists($columnName, $this->_data)) {
    		return null;
    	}
    	
    	return $this->_data[$columnName];
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Row_Abstract::__set()
     */
    public function __set($columnName, $value)
    {
    	$columnName = $this->_transformColumn($columnName);
    	$this->_data[$columnName] = $value;
    	$this->_modifiedFields[$columnName] = true;
    }
    
    /**
     * (non-PHPdoc)
     * @see Zend_Db_Table_Row_Abstract::setFromArray()
     */
    public function setFromArray(array $data)
    {
    	// baraaie inke tamaami e data haa be onvaan e field e row dar nazar gerefte mishavad
    	// be onvaane node haaye table asli ghaabele zakhire ast
    	//$data = array_intersect_key($data, $this->_data);
    
    	foreach ($data as $columnName => $value) {
    		$this->__set($columnName, $value);
    	}
    
    	return $this;
    }
    
    /**
     * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
     */
    protected function _doUpdate()
    {
    	/**
    	 * A read-only row cannot be saved.
    	 */
    	if ($this->_readOnly === true) {
    		require_once 'Zend/Db/Table/Row/Exception.php';
    		throw new Zend_Db_Table_Row_Exception('This row has been marked read-only');
    	}
    
    	/**
    	 * Get expressions for a WHERE clause
    	 * based on the primary key value(s).
    	 */
    	$where = $this->_getWhereQuery(false);
    
    	/**
    	 * Run pre-UPDATE logic
    	 */
    	$this->_update();
    
    	/**
    	 * Compare the data to the modified fields array to discover
    	 * which columns have been changed.
    	 */
    	//$diffData = array_intersect_key($this->_data, $this->_modifiedFields);
    	
    	/**
    	 * CHANGED BY MYSELF ______________________________________________________________________
    	 */
    	// send all data in update, becuase of all nodes deleted and inserting again
    	$diffData = $this->_data;
    	/**
    	 * ---------------------------------------------------------------------- CHANGED BY MYSELF
    	 */
    	
    	/**
    	 * Were any of the changed columns part of the primary key?
    	 */
    	$pkDiffData = array_intersect_key($diffData, array_flip((array)$this->_primary));
    
    	/**
    	 * Execute cascading updates against dependent tables.
    	 * Do this only if primary key value(s) were changed.
    	 */
    	if (count($pkDiffData) > 0) {
    		$depTables = $this->_getTable()->getDependentTables();
    		if (!empty($depTables)) {
    			$pkNew = $this->_getPrimaryKey(true);
    			$pkOld = $this->_getPrimaryKey(false);
    			foreach ($depTables as $tableClass) {
    				$t = $this->_getTableFromString($tableClass);
    				$t->_cascadeUpdate($this->getTableClass(), $pkOld, $pkNew);
    			}
    		}
    	}
    
    	/**
    	 * Execute the UPDATE (this may throw an exception)
    	 * Do this only if data values were changed.
    	 * Use the $diffData variable, so the UPDATE statement
    	 * includes SET terms only for data values that changed.
    	 */
    	if (count($diffData) > 0) {
    		$this->_getTable()->update($diffData, $where);
    	}
    
    	/**
    	 * Run post-UPDATE logic.  Do this before the _refresh()
    	 * so the _postUpdate() function can tell the difference
    	 * between changed data and clean (pre-changed) data.
    	 */
    	$this->_postUpdate();
    
    	/**
    	 * Refresh the data just in case triggers in the RDBMS changed
    	 * any columns.  Also this resets the _cleanData.
    	 */
    	$this->_refresh();
    
    	/**
    	 * Return the primary key value(s) as an array
    	 * if the key is compound or a scalar if the key
    	 * is a scalar.
    	 */
    	$primaryKey = $this->_getPrimaryKey(true);
    	if (count($primaryKey) == 1) {
    		return current($primaryKey);
    	}
    
    	return $primaryKey;
    }
}
