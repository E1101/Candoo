<?php
class Candoo_App_Config_Setter
{
	/**
	 * az set + kelid haaye option (setMethod) method haaie class ra ejraa mikonad
	 * va value har kelid be onvaane value be aan mifrestad
	 * @param array $options
	 */
	final public function __construct(array $options = null)
	{
		$this->start($options);
	}
	
	final public function start($options)
	{
	    $this->init();
	    
		if (is_array($options)) {
			foreach ($options as $option=>$value ) {
				$method = 'set'.ucfirst($option);
				if (method_exists($this, $method)) {
					$this->$method($value);
					unset($options[$option]);
				}
			}
		}
	}
	
	// ---------------------------------------------------------------------------------------------------------------
	
	protected function init()
	{
		
	}
			
    /**
     * set php.ini setting
     *
     * @param array $settings array('phpini_key' => 'value') 
     */
    public function setPhpini($settings)
    {
    	Candoo_App::setPhpsettings($settings);
    }
    	
}
