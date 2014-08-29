<?php
class Addons_Html_Addon extends Candoo_Addon_Abstract
{
	protected function displayAction() 
	{
		$content = $this->getParam('content', null);
		if ($content) {
			//$this->getResponse()->setBody($content);
			$this->setNoRender(true);
			return $content;
		}
		
		$file 	 = $this->getParam('file', null);
		
		// Http URL `````````````````````````````````````
		if (Zend_Uri_Http::check($file)) 
		{
			/**
			 * TODO : Assigning variables
			 */
			$output = file_get_contents($file);			
			$this->setNoRender(true);
		} 
		// Server local file ````````````````````````````````
		else 
		{
			$vieSuffix    = Candoo_App_Resource::get('viewRenderer')->getViewSuffix();
			$ext = pathinfo($file,PATHINFO_EXTENSION);
			// File e view template system ast va baayad render shavad 
			if ($ext == $vieSuffix) 
			{
				 // get current scriptPath
				 $scrPaths = $this->getView()->getScriptPaths();
				
				 // render content of file 
				 $this->getView()->assign($this->getParams());
				 
				 $path = pathinfo($file,PATHINFO_DIRNAME);
				 $this->getView()->addScriptPath($path);
				 $output = $this->getView()->render(basename($file));
				 
				 // restore script paths
				 $this->_view->setScriptPath($scrPaths);
			}
			// File e local ke mohtaviaat e aan be khorooji miravad
			else 
			{
				/**
				 * TODO : in ghesmat mitavanad file haa raa bar assaase extension
				 * va viewer e marboot be aan namaaiesh dahad
				 */
				
				if ($ext == 'php') {
					/**
			 		* Assigning variables for php files
			 		*/
					$vars = $this->_request->getParam('assign');
					extract($vars);
				
					ob_start();
					include $file;
					$output = ob_get_contents();
					ob_end_clean();
				} else {
					$output = file_get_contents($file);
				}
				
			}
			
		}
				
		//$this->getResponse()->setBody($output);	 
		
		return $output;
	}
	
}
