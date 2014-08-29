<?php
class cTemplate_Plugins_Render extends Zend_Controller_Plugin_Abstract
{
    protected $_layoutActionHelper = null;

    /**
     * @var Zend_Layout
     */
    protected $_layout;

    /**
     * Constructor
     *
     * @param  Zend_Layout $layout
     * @return void
     */
    public function __construct(Zend_Layout $layout = null)
    {
        if (null !== $layout) {
            $this->setLayout($layout);
        }
    }
    
    /**
     * postDispatch() -- render layout
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // agar dar khaast e ajax daashtim template gharaar nist render shavad
        if (Candoo_App_Resource::get('request')->isXmlHttpRequest()) {
        	return;
        }
        
        $layout = $this->getLayout();
        $helper = $this->getLayoutActionHelper();
        
        // Return early if layout has been disabled
        if (!$layout->isEnabled()) {
        	return;
        }
                
        // Return early if forward detected
        if (!$request->isDispatched()
            || $this->getResponse()->isRedirect()
            || ($layout->getMvcSuccessfulActionOnly()
                && (!empty($helper) && !$helper->isActionControllerSuccessful())))
        {
            return;
        }
                        
        /** Rendering layout ----------------------------------------------------------------------------| */
        $response   = $this->getResponse();
        // khoroojie controller/action ejraaa shode 
        // array['default'] => 'content'
        $content    = $response->getBody(true);
        
        // assign C/A output to layout->content_key
        $contentKey = $layout->getContentKey();
        if (isset($content['default'])) {
            $content[$contentKey] = $content['default'];
        }
        if ('default' != $contentKey) {
            unset($content['default']);
        }

        // rendering whole page (layout + C/A output)
        $layout->assign($content);
        
        $fullContent = null;
        $fullContent = $layout->render();
                
        /* do automatic head... attachement ``````````````` { */
        $jQuery = '';
        if (Candoo_JQuery::isEnabledView()) {
        	$jQuery = $layout->getView()->jQuery()."\r\n";
        }
        
        $fullContent = str_replace('<head>', 
        	"<head>\r\n"
        	.$layout->getView()->headTitle()."\r\n"
        	.$layout->getView()->headMeta()."\r\n"
        	.$layout->getView()->headStyle()."\r\n"
        	.$layout->getView()->headLink()."\r\n"
        	.$jQuery
        , $fullContent);
        
        $fullContent = str_replace('</body>',
        	 $layout->getView()->headScript()."\r\n"
        	."</body>\r\n"	
        , $fullContent);
        /* ```````````````````````````````````````````````` } */
        
        
        $response->setBody($fullContent);
        
        return;
        
       /* $obStartLevel = ob_get_level(); 
       try {
            $fullContent = $layout->render();
            $response->setBody($fullContent);
        } catch (Exception $e) {
            while (ob_get_level() > $obStartLevel) {
                $fullContent .= ob_get_clean();
        	}
            $request->setParam('layoutFullContent', $fullContent);
            $request->setParam('layoutContent', $layout->content);
            $response->setBody(null);
            throw $e;
        }  */
    }

    /**
     * Retrieve layout object
     *
     * @return Zend_Layout
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Set layout object
     *
     * @param  Zend_Layout $layout
     * @return Zend_Layout_Controller_Plugin_Layout
     */
    public function setLayout(Zend_Layout $layout)
    {
        $this->_layout = $layout;
        return $this;
    }
    

    /**
     * Set layout action helper
     *
     * @param  Zend_Layout_Controller_Action_Helper_Layout $layoutActionHelper
     * @return Zend_Layout_Controller_Plugin_Layout
     */
    public function setLayoutActionHelper(Zend_Layout_Controller_Action_Helper_Layout $layoutActionHelper)
    {
        $this->_layoutActionHelper = $layoutActionHelper;
        return $this;
    }

    /**
     * Retrieve layout action helper
     *
     * @return Zend_Layout_Controller_Action_Helper_Layout
     */
    public function getLayoutActionHelper()
    {
        return $this->_layoutActionHelper;
    }
}
