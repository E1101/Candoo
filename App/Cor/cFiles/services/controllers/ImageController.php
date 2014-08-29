<?php
class cFiles_ImageController extends Zend_Controller_Action
{
    /**
     * Class e image editor
     * 
     * @var cFiles_Lib_Imagedit
     */
    protected $_class; 
    
    /**
     * Format e tasvire khorooji
     * agar entekhaab nashavad hamaan format e tasvir e voroodist
     * 
     * @var string
     */
    protected $_format;
    
	public function indexAction()
	{
	    Candoo_App_Resource::get('viewRenderer')->setNoRender();
	    
	    $params = $this ->_request ->getVars();
	    
	    /**
	     * Ghabl az hame image raa set mikonim chon baaghi e parameter haa be aan ehtiaaj daarand
	     */
	    if (! isset($params['f'])) {
	        throw new Exception('File not defined.');
	    }
	    
	    $f = $params['f'];
	    
	    // set image generator class
	    $this->_class = $this->view->imagedit($f)->setUseApi(false)->setCreateTag(false);
	    
	    /**
	     * @ use Cache
	     * Bar Asaase naame file va parameter haaaie voroodi baayad az chache estefaade konim
	     * 
	     */ 
	     $output = false;
	     if (Candoo_Cache::isEnabled()) {
	         // cache unique id for image
	         $params_temp = $params;
	         ksort($params_temp);
	         $id = md5(serialize($params_temp));
	         unset($params_temp);
	         
	         $cache = new Candoo_Cache_Core();
	         $output = $cache->load($id);
	     }
	        
	     // agar dar chache mojood nabood yaa caching enable nabood
	     if ( $output === false) 
	     {
	         unset($params['f']);
	         foreach ($params as $met => $arg) {	                
	         	$met = 'set'.ucfirst(strtolower($met));
	            	 
	           	if (method_exists($this, $met)) {
	           		if (strpos($arg, ',') !== false) {
	           			$arg = explode(',', $arg);
	           			// mohtaviate string e true va false raa be meghdaar boolean tabdil mikonad
	           			array_walk($arg, function (&$val,$key) use (&$arg) {
	           				if (strtolower($val) == 'true' || strtolower($val) == 'false') {
	           					$arg[$key] = (strtolower($val) == 'true');
	           				}
	           			});
	           		} else {
	           			$arg = array($arg);
	           		}
	           		 
	           		call_user_func_array(array($this,$met), $arg);
	           	}
	     	}
	            
	        $output = $this->_class->__toString();
	            
	        if (Candoo_Cache::isEnabled()) {
	             $cache->save($output,$id,array('imagedit'));
	        }    
	    }
	    	    
	    $this->getResponse()
	    	 ->setHeader('Content-type', 'image'.$this->_class->getDefaultFormat())
	    	 //->setHeader('Content-Disposition', basename($f))
	    	 ->setBody($output);
	}
		
	protected function setRotate( $angle , $crop = false )
	{
	    $this->_class->rotate($angle,$crop);
	}
	
	protected function setGrayscale()
	{
	    $this->_class->grayscale();
	}
	
	protected function setBrightness( $brightness )
	{
	    $this->_class->brightness($brightness);
	}
	
	protected function setContrast( $contrast )
	{
	    $this->_class->contrast($contrast);
	}
	
	protected function setCrop( $x , $y , $width , $height )
	{
	    $this->_class->crop($x, $y, $width, $height);
	}
	
	protected function setCropborder( $left , $top , $right , $bottom )
	{
	    $this->_class->cropBorder($left, $top, $right, $bottom);
	}
	
	protected function setCropface( $preserveRatio = false )
	{
	    $this->_class->cropFace($preserveRatio);
	}
	
	protected function setHeight( $height , $preserveRatio = false )
	{
	    $this->_class->setHeight($height,$preserveRatio);
	}
	
	protected function setWidth( $width , $preserveRatio = false )
	{
	    $this->_class->setWidth($width, $preserveRatio);
	}
	
	protected function setFormat($format)
	{
	    $this->_class->setDefaultFormat($format);
	}
}