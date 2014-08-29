<?php
class cLocali_Model_Table_Behavior_Translate extends Candoo_Db_Table_Behavior_Abstract
{
    /**
     * Locale fe`li baraaie estefaade hengaami ke
     * class dar haalate translation ast
     * 
     * @var string
     */
    protected $_locale;
    
    /**
     * Field haai az in table ke baayad localize shavand
     * 
     * @var array | string
     */
    protected $_translationFields;
    
    /**
     * Class e table-i- ke translation e field haa ro negah midaarad
     * 
     * @var Candoo_Db_Table_Abstract
     */
    protected $_translationTable = 'cLocali_Model_Table_I18n';
    
    
    // Class options method *************************************************************************
    
    /**
     * Agar table dar haalate translation baashad ehtiaaj daarim ke
     * locale fe`li raa daashte baashim
     *
     * @param string | Zend_Locale $locale
     * @throws Zend_Exception
     */
    public function setLocale($locale)
    {
    	if ($locale instanceof Zend_Locale) {
    		$locale = $locale->toString();
    	}
    
    	if (! is_string($locale) || ! Zend_Locale::isLocale($locale)) {
    		throw new Zend_Exception('Invalid locale provided.');
    	}
    
    	$this->_locale = $locale;
    
    	return $this;
    }
    
    /**
     * Get Current class locale
     *
     * if not set try to get from locale resource object
     *
     * @return string
     */
    public function getLocale()
    {
    	if (! $this->_locale) {
    		// try to get locale from locale object
    		if ($locale = Candoo_App_Resource::get('locale',false)) {
    			$this->setLocale($locale->toString());
    		}
    	}
    
    	return $this->_locale;
    }
    
    /**
     * Object e Class e Table-i- ke translation haaa raa negah midaarad
     *
     * @param string | Zend_Db_Table_Abstract $class
     */
    public function setTranslationTable($class)
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
    
    	$this->_translationTable = $class;
    
    	return $this;
    }
    
    /**
     * Object e Class e Table-i- ke translation haaa raa negah midaarad
     * bar migardaanad
     *
     * @throws Zend_Exception
     * @return Ambigous <Zend_Db_Table_Abstract>
     */
    public function getTranslationTable()
    {
    	$class = $this->_translationTable;
    
    	if (! class_exists($class,true)) {
    		throw new Zend_Exception('Table Class '.$this->_translationTable.' not found.');
    	}
    
    	$class = new $class();
    	if (!$class instanceof Zend_Db_Table_Abstract) {
    		throw new Zend_Exception('Table Class must instance of Zend_Db_Table_Abstract');
    	}
    
    	return $class;
    }
    
    /**
     * Field haaee az in table ke baayad translate shavand
     * raa dar ghaalebe array bar migardaanad
     *
     * @return Ambigous <array, multitype:>
     */
    public function getTranslationFields()
    {
    	$fields = $this->_translationFields;
    
    	if (is_string($fields)) {
    		$fields = (array) $fields;
    	} elseif ($fields == null) {
    		$fields = array();
    	}
    
    	return $fields;
    }
    
    /**
     * Field haaee ro ke baayad translate shavand be class moa`refi mikonad
     *
     * @param string | array $fields
     * @throws Zend_Exception
     */
    public function setTranslationFields($fields)
    {
    	if (is_string($fields) ) {
    		$fields = (array) $fields;
    	} elseif (! is_array($fields)) {
    		throw new Zend_Exception('Fields must be array or string');
    	}
    
    	$this->_translationFields = $fields;
    
    	return $this;
    }
    
    /**
     * Add Translation Field(s) to current field(s)
     *
     * @param string | array $field
     */
    public function addTranslationField($field)
    {
    	$currFields = $this->getTranslationFields();
    
    	$this->setTranslationFields($field);
    	$extraField = $this->getTranslationFields();
    
    	$this->setTranslationFields(array_merge($currFields,$extraField));
    
    	return $this;
    }
    
    // StandAlone Query based methods ***************************************************************
    
    /**
     * Add translation data for a row with specific primary key
     *
     * @param int   $pk   | primary key of row
     * @param array $data | data baayad haavie tamaami e translation field haa baashad,
     * 						dar gheire in soorat dar select yaaft nemishavad
     */
    public function addTranslation($pk, $locale, array $data)
    {
    	$trFields = $this->_getTranslatableData($data);
    
    	// test mikonim ke tamaami e translation data haazer baashand
    	if (count($trFields) != count($this->getTranslationFields())) {
    		throw new Zend_Exception('All translation fields must be found in data');
    	}
    
    	// agar data i ke marboot be translation field haa nabood
    	if ( ($nonTrans = array_diff_key($data, $trFields)) != array() )  {
    		throw new Zend_Exception('Field(s) "'.implode(', ', $nonTrans).'" not defined as translation field.');
    	}
    
    	foreach ($trFields as $field => $value) {
    		// write it
    		$trData = array(
    				'model' 	  => get_class($this->getTable()),
    				'foreign_key' => $pk,
    				'field' 	  => $field,
    				'locale' 	  => $locale,
    				'content'	  => $value
    		);
    		$transTable = $this->getTranslationTable();
    		$transTable ->createRow($trData)->save();
    	}
    }
    
    // Query correction *****************************************************************************
    
    /**
     * You can pass translation fields as:
     * '@locale' => array(
     		'fa' => array(
     			'test_title' => 'عنوان به فارسی',
     		)
     	)
     */
    public function _beforeInsert($data)
    {
        // fe`alan @locale az data kenaar gozaashte mishavad
        if (isset($data['@locale'])) {
        	if (! is_array($data['@locale']) ) {
        		throw new Zend_Exception('Invalid locale translation provided. must be an array');
        	}
        
        	unset($data['@locale']);
        }
    }
    
    public function _afterInsert($pk, $insertedData, $oldData)
    {
        $data = $oldData;
        
        $localeFields = array();
        // search for @locale translation fields
        if (isset($data['@locale'])) {
        	if (! is_array($data['@locale']) ) {
        		throw new Zend_Exception('Invalid locale translation provided. must be an array');
        	}
        
        	$localeFields = $data['@locale'];
        	unset($data['@locale']);
        }
        
        // add current locale to translation field
        $localeFields = array_merge($localeFields,
        	array($this->getLocale() => $this->_getTranslatableData($data))
        );
        
        foreach ($localeFields as $locale => $fields) {
        	$this->addTranslation($pk, $locale, $fields);
        }
        
        return $pk;
    }
    
    /**
     * Afzoodan e meghdaar e field haa az table e translation ba meghdaar e asli e field haaa
     *  
     */
    public function _beforeFetch($select)
    {
        // get select Table, primary key and name
        $info       = $select->getTable()->info();
        $tableName  = $info[Zend_Db_Table::NAME];
        $tableClass = get_class($select->getTable());
        $tablePrim  = current($info[Zend_Db_Table::PRIMARY]);
        
        $locale     = $this->getLocale();
        
        /* agar filed i add nashode bood pas manzoor * ast
         in ghesmat dar select::assemple anjaam mishavad
        vali chon maa tavasote joinInner field haaee ro
        be select ezaafe khaahim kard dar natije dar
        assemble wildcard ro ezaafe nakhaahad kard */
        $fields  = $select->getPart(Zend_Db_Table_Select::COLUMNS);
        // If no fields are specified we assume all fields from primary table
        if (!count($fields)) {
        	$select->from($tableName);
        }
        
        // table betavaanad baa table digar join shavad
        $select->setIntegrityCheck(false);
        
        foreach ($this->getTranslationFields() as $tf) {
        	$name = 'I18n__'.$tf;
        	// get name of translation table
        	$joinTable = $this->getTranslationTable()->info(Zend_Db_Table::NAME);
        
        	$select->joinInner(
        			array( $name => $joinTable),//join table name
        			"`$name`.`foreign_key` = `$tableName`.`$tablePrim`
        			AND `$name`.`model`   = '$tableClass'
        			AND `$name`.`field`   = '$tf'
        			AND `$name`.`locale`  = '$locale'
        			",//conditions
        			//array($tf => $name.'.content') // field e content e in query raa baa naame field e asli jaaigozin mikonad
        			array($tableName.$name => $name.'.content')
        	);
        }
        
    }
    
    public function _beforeFetchReturn($data)
    {
        $tableName       = $this->getTable()->info(Zend_Db_Table::NAME);
           
        foreach ($data as $i=>$row) {
            foreach ($row as $field => $val) {
                $name = $tableName.'I18n__';
                // agar field e translate bood baa meghdaar e field e asli jaaigozin mishavad
                if (strpos($field, $name) !== false) {
                	$originField = substr($field, strlen($name), (strlen($field)-strlen($name)));
                	$data[$i][$originField] = $val;
                	unset($data[$i][$field]);
                }    
            }
        }
    }
    
    
    /**
     * Aval Faghat field haaee ke jozve translationField haa nist dar jadval e 
     * asli update mishavad.
     * 
     * translation fiield haa dar jadval e digari zakhire mishavand
     * 
     */
    public function _beforeUpdate($data, $where)
    {
        $data = array_diff_key($data, array_flip($this->getTranslationFields()));
    }
    
    public function _afterUpdate($affected, $updatedData, $oldData, $where)
    {
        $data = array_diff_key($oldData, $updatedData);
        if (empty($data)) {
            return $affected;
        }
        
        $rows = $this->getTable()->fetchAll($where);
        // { save translationFields content to his table ``````````````````````````````````````````````|
        foreach ($rows as $row) {
        	$tTable = $this->getTranslationTable();
        	// get query data
        	$locale     = $this->getLocale();
        	$foreignKey = current($this->getTable()->info(Zend_Db_Table::PRIMARY));//get primary key
        	$foreignKey = $row->$foreignKey;
        	$model      = get_class($this->getTable());
        
        	foreach ($data as $field => $val) {
        		$whr    = "
        		`locale` = '$locale'
        		AND `foreign_key` = $foreignKey
        		AND `field` = '$field'
        		AND `model` = '$model'
        		";
        		$tTable ->update(
        			array(
        				'content' => $val
        			),
        			$whr
        		);
        	}
        }
        
        return ($affected) ? $affected : $rows->count();
    }
    
    /**
     * Delete data form translation table
     *  
     */
    public function _beforeDelete($where)
    {
        $prKey = current($this->getTable()->info(Zend_Db_Table::PRIMARY));
        
        // be khaatere in line haa nemitavaan _afterDelete baashad
        // chon be table e asli ehtiaaj daarim
        $rows = $this->getTable()->fetchAll($where);
        $ids  = array();
        foreach ($rows as $row) {
        	$ids[] = $row->$prKey;
        }
        
        $tableClass = get_class($this->getTable());
        $wr = $this->getTable()->getAdapter()->quoteInto('`foreign_key` IN(?)', $ids);
        $wr .= " AND `model` = '$tableClass'";
        
        $transTable = $this->getTranslationTable();
        $transTable->delete($wr);
    }
    
    
    /**
     * Yek array be onvaane voroodi migirad va test mikonad ke aayaa haavie e translatable
     * field haa mishavad ?!!
     *
     * @param array $data
     */
    protected function _hasTranslatabeData(array $data)
    {
    	foreach ($data as $field => $val) {
    		if ( in_array($field, $this->getTranslationFields()) ) {
    			return true;
    		}
    	}
    
    	return false;
    }
    
    /**
     * Az array aanhaaee raa ke marboot be translatable fields hast raaa
     * bar migardaanad
     *
     * @param array $data
     */
    protected function _getTranslatableData(array $data)
    {
    	return array_intersect_key($data, array_flip($this->getTranslationFields()));
    }
    
}
