<?php
class moduleTest_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        // you can set template from cTemplate Config
        // 		template.front = "websplash"
        //		template.admin = "simpleAdmin"
        // or   $this->view->layout()->setTemplate('simpleAdmin');
        
        // set layout of page inside a template folder with [name].phtml
        //$this->view->layout()->setLayout('fullwidth');
    }
    
    public function subAction()
    {
        Zend_Debug::dump('You are in sub page');
        
        Zend_Debug::dump(
        		$this->_request->getVars()
        );
    }
    
    public function testAction()
    {
        Zend_Debug::dump('You are in test page');
        
        Zend_Debug::dump(
        		$this->_request->getVars()
        );
    }
    
	public function indexAction()
	{	    
	    // addons `````````````````````````````````````````````````````````| 
	 	//Candoo_Addon_Registry::exec('target', 'display','Here is test');
	 
	 	// formsaaaz ``````````````````````````````````````````````````````|
	 	$form = new moduleTest_Forms_User_Login();
	 	$form->enableJquery(false);
	 	
	 	if ($this->_request->isPost()) {
	 	    if ($form->isValid($this->_request->getPost())) {
	 	    	
	 	    }
	 	}
	 	$this->view->form = $form;
	 	// ````````````````````````````````````````````````````````````````
	 	
	 	$table  = new moduleTest_Model_Translatable();
	 	
	 	/* $table->translateBehavior()->setLocale('fa');
	 	$data = array(
	 			'test_title' 	 => 'Persian Title',
	 			'test_isactive'	 => 0
	 	);
	 	$table ->update($data,'test_id = 2'); */
	 	
	 	// add data and Translation locale to translatable data`````````````````````````|
	 	/* 
	 	$data = array(
	 			'test_title' 	 => 'This is my Candoo page title',
	 			'test_isactive'	 => 1,
	 			'@locale'        => array(
	 				'fa' => array(
	 					'test_title' 	 => 'عنوان به فارسی',
	 				)
	 			)
	 	);
	 	$table ->insert($data); 
	 	*/
	 	// ``````````````````````````````````````````````````````````````````````````````
	 	
	 	// inserting data is possible only for current locale of table `````````````````|
	 	/* $data = array(
	 			'test_title' 	 => 'This is my Candoo page title',
	 			'test_isactive'	 => 1,
	 	);
	 	$emptyRow = $table->createRow($data)->save(); */
	 	// `````````````````````````````````````````````````````````````````````````````

	 	// ==============================================================================
	 	
	 	//$table = new Candoo_Db_Table_Dms();
	 	$table = new moduleTest_Model_Table_Dms();
	 	$table->translateBehavior()->setLocale('fa');
	 	
	 	// delete a page and his nodes with cascade deletet `````````````````````````````|
	 	//$table->delete('page_id = 37');
	 	
	 	$row   = $table->fetchRow('page_id = 1');
	 	//$row->setReadOnly(false);
	 	//$row->delete();
	 	// ```````````````````````````````````````````````````````````````````````````````
	 	
	 	// find/select a page ```````````````````````````````````````````````````````````|
	 	//$rows  = $table->find(1); // find page by id
	
	 	//$where = $table->select()->where('page_id = ?',1);
	 	//$rows  = $table->fetchAll($where); // find page by condition
	 	//$row   = $rows->current();
	 	
	 	//$row   = $table->fetchRow($where); // fetch a row by condition
	 	//$row->setReadOnly(false);
	 	//Zend_Debug::dump($row->getNodes());
	 	
	 	// edit a field content
	 	//$row->content = 'This content changed by me';
	 	// add new field 
	 	//$row->newfield = 'This is new field';
	 	//$row->save();
	 	
	 	// ```````````````````````````````````````````````````````````````````````````````
	 	
	 	// find a page nodes ````````````````````````````````````````````````````````````|
	 	/* $rows = $table->find(4); // find page by primary key
	 	if (count($rows) > 0) {
	 	    $row  = $rows->current();
	 	    $rowNodes = $row->findDependentRowset('Candoo_Db_Table_Dms_Node','Pages');
	 	    $rowNodes = $rowNodes->toArray();
	 	    
	 	    Zend_Debug::dump($rowNodes);
	 	} */
	 	// ```````````````````````````````````````````````````````````````````````````````
	 	
	 	// Insert a page + nodes ````````````````````````````````````````````````````````|
	 	$data = array(
	 		'page_title' 	 => 'This is my Candoo page title',
	 		// namespace automatically added
	 		//'page_namespace' => 'candoo_pages',
	 		
	 		'content'		 => 'This is content of this page as a node',
	 		'image'			 => 'http://localhost/img/image_no.jpg',
	 		'grade'			 => 'A',
	 		'is_active'		 => 0,
	 	);
	 	//$table ->insert($data);
	 	// ```````````````````````````````````````````````````````````````````````````````
	 	
	 	// create new row and insert to DB  `````````````````````````````````````````````|
	 	//$emptyRow = $table->createRow($data)->save();
	 	// ```````````````````````````````````````````````````````````````````````````````
	 	
	 	// Update a page + nodes ````````````````````````````````````````````````````````|
	 	$data = array(
	 			'page_title' 	 => 'UPDATED:This is my Candoo page title',
	 			'page_namespace' => 'UPDATED:candoo_pages',
	 			//'page_parent' 	 => 2,
	 	
	 			'content'		 => '<p>UPDATED:This is content of this page as a node</p>',
	 			'image'			 => 'UPDATED:http://localhost/img/image_no.jpg',
	 			'grade'			 => 'UPDATED:A',
	 			'is_active'		 => 1,
	 	);
	 	//$table ->update($data,'page_id = 6');
	 	// ```````````````````````````````````````````````````````````````````````````````
	 	
	}
}