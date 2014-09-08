<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('textarea');

class JFormFieldTwoJTextArea extends JFormFieldTextArea{
	protected $type = 'TwoJTextArea';
	public $hide = 0;
	
	protected function getInput(){
		if( isset($this->element['default']) ){
			$this->value = str_replace( '|2JNS|', "\n", $this->value );
		}
		$this->hide = isset($this->element['hide']) ? $this->element['hide'] : 0;
		$addtext = isset($this->element['addtext']) ? '<div class="twojtoolbox_form_addtext">'.$this->element['addtext']."</div>" : '';
		return parent::getInput().$addtext;
	}
}
