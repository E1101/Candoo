<?php
class moduleTest_Widgets_Test_Widget extends Candoo_Extension_Widget_Abstract
{
	protected function displayAction() 
	{
		$this->setNoRender();

		return '<div style="width:285px;height:150px;border: 1px solid #111">
				<b>'.$this->_view->translate('this is a ranslated message').'</b> from moduleTest <b>TestWidget</b>
				<hr/>
				</div>';
	}
}
