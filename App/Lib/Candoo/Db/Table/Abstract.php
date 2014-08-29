<?php
class Candoo_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    protected static $_prefix;
    
    protected $_behaviors = array ();
    
    /**
     * 0 not started transaction yet
     * 1 transaction level 1
     * .
     * .
     * n transaction level n
     * 
     * note: with each rollBack() decress this and finaly rollBack DB on 0
     * 
     * @var int
     */
    protected static $_transaction = 0;
    
    // Query methods ```````````````````````````````````````````````````````````````````````
    
    public function insert(array $data) 
    {
        $this->beginTransaction();
        
        try 
        {
            $oldData = $data;
            
        	$this->_beforeInsert(&$data);
        	$this->_behaviorExec('_beforeInsert', &$data);
        	
    		$pk = parent::insert($data);

    		$this->_afterInsert($pk, $data, $oldData);
    		$this->_behaviorExec('_afterInsert', $pk, $data, $oldData);

    		$this->commit();
        } 
        catch (Exception $e) 
        {
            $this->rollBack();
            throw $e;
        }
    	
    	return $pk;
    }
    
    public function update(array $data, $where)
    {
        $this->beginTransaction();
        
        try 
        {
            $oldData = $data;
            
        	$this->_beforeUpdate(&$data,&$where);
        	$this->_behaviorExec('_beforeUpdate', &$data,&$where);
        	
        	// ba`zi mavaaghe data e update shode marboot be jadvale asli nist 
        	// va dar _afterUpdate e`maal mishavad
        	if (! empty($data) ) {
        	    $affected = parent::update($data, $where);
        	}
        	
        	$this->_afterUpdate(&$affected, $data, $oldData, $where);
        	$this->_behaviorExec('_afterUpdate', &$affected, $data, $oldData, $where);
        	
        	$this->commit();
        }
        catch (Exception $e)
        {
        	$this->rollBack();
        	throw $e;
        }
        
        return $affected;
    }
    
    public function delete($where)
    {
        $this->beginTransaction();
        
        try 
        {
        	$this->_beforeDelete(&$where);
        	$this->_behaviorExec('_beforeDelete', &$where);
        
        	$affect = parent::delete($where);
        
        	$this->_afterDelete(&$affect,$where);
        	$this->_behaviorExec('_afterDelete', &$affect,$where);
        	
        	$this->commit();
        }
        catch (Exception $e)
        {
        	$this->rollBack();
        	throw $e;
        }
        
        return $return;
    }
    
    protected function _fetch(Zend_Db_Table_Select $select)
    {
        $this->_beforeFetch(&$select);
        $this->_behaviorExec('_beforeFetch', &$select);
        
    	$data = parent::_fetch($select);
    	
    	$this->_afterFetch(&$data, $select);
    	$this->_behaviorExec('_afterFetch', &$data, $select);
    	
    	$this->_beforeFetchReturn(&$data);
    	$this->_behaviorExec('_beforeFetchReturn', &$data);
    	
    	return $data;
    }
    
    protected function _beforeInsert(array $data) { }
    
    /**
     * In method pas az ejraaie INSERT sedaa zade mishavad
     * 
     * @param int $pk Primary key row insert shode
     * @param array $insertedData Daadeh haaee ke dar row neveshteh shode ast
     * @param array $oldData Daade haaee ke tavasote insert($data) dar ebtedaa ersaal shode bood
     */
    protected function _afterInsert($pk,array $insertedData,array $oldData) { }
    
    protected function _beforeUpdate(array $data, $where) { }
    
    protected function _afterUpdate($affected, $updatedData, $oldData, $where) { }
    
    protected function _beforeDelete($where) { }
    
    protected function _afterDelete($affected, $where) { }
    
    protected function _beforeFetch(Zend_Db_Table_Select $select) { }
    
    protected function _afterFetch(array $data, Zend_Db_Table_Select $select) { }
    
    protected function _beforeFetchReturn(array $data) { }
    
    /**
     * Bar rooie har event as method haaye baalaa behavior haaye 
     * register shode raa ejraa mikonad
     * 
     */
    protected function _behaviorExec()
    {
        // return a copy of arguments that don`t have refrences
        //$args = func_get_args();
        
        // get arguments with refrence ````````````````````````|
        $stack = debug_backtrace();
        
        $args = array();
        if (isset($stack[0]["args"]))
        	for($i=0; $i < count($stack[0]["args"]); $i++)
        	$args[$i] = & $stack[0]["args"][$i];
        // ````````````````````````````````````````````````````
        
        $method = $args[0];
        unset($args[0]);
        
        $behaviors = $this->getBehaviors();
        foreach ($behaviors as $behavior) {
            $behObject = $this->getBehavior($behavior);
            call_user_func_array(array($behObject,$method), $args);
        }
    }
    
    
    /**
     * Daadeh haaie ersaali raa dar moghaabele cols haaye in table az db moghaaiese 
     * mikonad va faghat aaan haai raa ke jozve field haaye table ast raa minevisad 
     * 
     * @param array $data | dadeh haaye baaghi maande raa bar migardaanad
     */
    public function insertImmediately(array $data) 
    {
        $remaining = array();
        
        // seprate data to insert compared with table cols
        $colsData = $this->_getColsRelData($data);
          
        return $this->insert($colsData);
    }
    
    /**
     * Daadeh haaie ersaali raa dar moghaabele cols haaye in table az db moghaaiese
     * mikonad va faghat aaan haai raa ke jozve field haaye table ast raa minevisad
     *
     * @param array $data | dadeh haaye baaghi maande raa bar migardaanad
     */
    public function updateImmediately(array $data, $where)
    {
    	$remaining = array();
    
    	// seprate data to insert compared with table cols
    	$colsData = $this->_getColsRelData($data);
    
    	return $this->update($colsData, $where);
    }
    
    
    public function __call($meth, $args)
    {
        // Return Behavior object : $this->translate[Behavior]()
    	if (substr($meth, -8) === 'Behavior') {
    	    $behav = substr($meth, 0, strlen($meth)-8);
    		return $this->getBehavior($behav);    
    	}
    	
    	return false;
    }
    
    /**
     * Return Behavior as object if registered.
     * 
     * dastyaabi be behavior haa onDemand ast,
     * hengaami ke getBehavior() call mishavad
     * class name e register shode tabdil be object
     * shode bood object bargasht mishavad, agar na tabdil be
     * object, zakhire, sepas bargasht mishavad 
     * 
     * @param Candoo_Db_Table_Behavior_Abstract $behavior
     * @return Candoo_Db_Table_Behavior_Abstract
     */
    public function getBehavior($behavior)
    {
        if (!$this->hasBehavior($behavior)) {
            throw new Zend_Exception('Behavior '.$behavior.' not registered.');
        }
        
        // register behavior as object
        if (! $this->_hasBehaviorObject($behavior) ) {
            // new behaviorClass()
            if (isset($this->_behaviors[$behavior]['setPrefixNamespace'])) {
                $prefix = $this->_behaviors[$behavior]['setPrefixNamespace'];
                // don`t use as a behavior constructor options
                unset($this->_behaviors[$behavior]['setPrefixNamespace']);
            } else {
                $prefix = 'Candoo_Db_Table_Behavior'; 
            }
           
            $class  = $prefix.'_'.$behavior;
            if (! class_exists($class,true) ) {
            	// try to change behavior to get right class
            	$class = $prefix.'_'.ucfirst(strtolower($behavior));
            }
                        
            if (! class_exists($class,true) ) {
            	throw new Zend_Exception('Behavior Class not found for "'.$behavior.'"');
            }
            
            // register object 
            $options = (is_array($this->_behaviors[$behavior])) 
            		 ? $this->_behaviors[$behavior]
            		 : array();
            
            $options['table'] = $this;
            
            $this->_behaviors['__'.$behavior] = new $class($options);
        }
        
        return $this->_behaviors['__'.$behavior];
    }
    
    /**
     * Yek array shaamel e naam e behavior haaie register shode bar migardaanad
     * 
     * @return array
     */
    public function getBehaviors()
    {
        $behaviors = array_keys($this->_behaviors);
        foreach ($behaviors as $i=>$behavior) {
            if (substr($behavior, 0,2) === '__') {
                unset($behaviors[$i]);
            }
        }
        
        return $behaviors; 
    }
    
    /**
     * Test mikonad ke aayaa in behavior tabdil be object shode ast?!!
     * 
     * dar $_behaviors object e har behavior ham zakhire mishavad
     * be kelid e "__behaviorName" exp. __translate
     * 
     * @param string $behavior
     */
    protected function _hasBehaviorObject($behavior)
    {
        return isset($this->_behaviors['__'.$behavior]);
    }
    
    /**
     * Aayaa Behavior baa yek naam mojood ast?!!
     * 
     * @param string $behavior
     * @return bool 
     */
    public function hasBehavior($behavior)
    {
        return isset($this->_behaviors[$behavior]); 
    }
    
    
    /**
     * Yek assoc array raa migirad va aan ghesmat az array ra ke marboot
     * be cols haaye daroon e table hast raa bar migardaanad
     * 
     * @param array $data
     */
    protected function _getColsRelData(array $data)
    {
        $cols = $this->info(Zend_Db_Table::COLS);
        
        return array_intersect_key($data, array_flip($cols));
    }
    
    /**
     * Retrieve table original(default or base) columns, instead of nodes
     *
     * Morede estefaade dar rowClass
     *
     * @return array
     */
    public function getCols()
    {
    	return parent::_getCols();
    }
    
    // Transaction Virtual controll ````````````````````````````````````````````````````````
    
    /*
     * Chon in class az transaction estefade mikonad tamaamie class haaye extend shode az in
     * class ham be naachaar transaction daarand.
     * 
     * agar transaction yekbaar fa`aal shode baashad pas az $this->_db->beginTransaction()
     * khataa ie inke ghablan start shode daarim.
     * 
     * pas be in soorat transaction haa ro hade`aghal dar table haa mitavaanim controll konim
     * 
     * Candoo_Dms_Page : <starting transaction>
     * 	|>	Candoo_Dms_Nodes : <starting transaction again>
     *      on error i dont rollBack this only throw exception
     *  Exception thrown, here rollBack DB
     */
    
    public function beginTransaction()
    {
    	if (self::$_transaction == 0) {
    		$this->_db->beginTransaction();
    	}
    
    	self::$_transaction++;
    
    	return $this;
    }
    
    public function rollBack()
    {
    	self::$_transaction--;
    
    	if (self::$_transaction == 0) {
    		$this->_db->rollBack();
    	}
    
    	return $this;
    }
    
    public function commit()
    {
    	self::$_transaction--;
    
    	if (self::$_transaction == 0) {
    		$this->_db->commit();
    	}
    
    	return $this;
    }
    
    
    // Table Class Setup ```````````````````````````````````````````````````````````````````
    
    /**
     * Initialize table and schema names.
     *
     * If the table name is not set in the class definition,
     * use the class name itself as the table name.
     *
     * A schema name provided with the table name (e.g., "schema.table") overrides
     * any existing value for $this->_schema.
     *
     * @return void
     */
    protected function _setupTableName()
    {
    	if (! $this->_name) {
    		$this->_name = get_class($this);
    	}
    	
    	// add prefix for [prefix_]tables
    	$this->_name = self::getPrefix().$this->_name; 
    	
    	if (strpos($this->_name, '.')) {
    		list($this->_schema, $this->_name) = explode('.', $this->_name);
    	}
    }
    
    /**
     * Initialize database adapter.
     *
     * @return void
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupDatabaseAdapter()
    {
    	if (! $this->_db) {
    		$this->_db = Candoo_App_Resource::get('db',false);
    		if (!$this->_db) {
    		    $this->_db = self::getDefaultAdapter();
    		    
    		    if (!$this->_db instanceof Zend_Db_Adapter_Abstract) {
    		    	require_once 'Zend/Db/Table/Exception.php';
    		    	throw new Zend_Db_Table_Exception('No adapter found for ' . get_class($this));
    		    }
    		}
    	}
    }

    /**
     * tamaami e site haa va subsite haa baraaie db khod az yek prefix estefaade mikonand
     * module haa hengaaam e nasb shodan va ijaaad e db table haaa az in prefix estefaade
     * mikonand
     * 
     * in prefix bar asaas e naame site generate mishavad
     * 
     */
    public static function getPrefix()
    {
        if (! self::$_prefix) {
            $host = Candoo_Site::getSite();
            $host = str_replace('www.','',strtolower($host));
             
            $prsHost = explode('.',$host);
            $prsHost = implode('_',$prsHost);
            
            $_prefix = str_replace(array('a','e','i','o','u','y'),'',$prsHost).'_';
            
            self::setPrefix($_prefix);
        }
        
    	return self::$_prefix;
    }
    
    public static function setPrefix($prefix)
    {
        self::$_prefix = $prefix;
    }
}
