<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('text');
class JFormFieldTwoJText extends JFormFieldText{
	protected $type = 'TwoJText';
	public $hide = 0;
	
	protected function getInput(){
		$this->hide = isset($this->element['hide']) ? $this->element['hide'] : 0;
		$addtext = '';
		if( $this->element['class'] && $this->element['class']=='twoj_color' && !isset($this->element['hide_transperent'])  ){
			$addtext = " <input type='submit' name='transperent_color[]' value='".JText::_('COM_TWOJTOOLBOX_SET_TRANSPERENT', 1)."' class='button' onClick='ems_set_transp(\"".$this->name."\"); return false' />";	
		}
		$addtext .= isset($this->element['addtext']) ? '<div class="twojtoolbox_form_addtext">'.$this->element['addtext']."</div>" : '';
		return parent::getInput().$addtext;
	}
}
