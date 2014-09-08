<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('twojtext');

class JFormFieldBorder extends JFormFieldTwoJText{
	protected $type = 'border';
	protected $value_color;
	protected $value_w;
	protected $value_s;
	protected $shadown_border;
	
	protected function getInput(){
		
		$this->shadown_border = isset($this->element['shadown_border']) ? $this->element['shadown_border'] : 0;
		
		$this->value_color = $this->form->getValue($this->element['name'].'_color', 'params', $this->element['default_color']);
		$this->value_w = $this->form->getValue($this->element['name'].'_w', 'params', $this->element['default_w']);
		if(!$this->shadown_border) $this->value_s = $this->form->getValue($this->element['name'].'_s', 'params', $this->element['default_s']);
		
		$html = '';
		
		$html .= '<input type="text" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_w]" value="'.$this->value_w.'" class="inputbox" style="float: left;" size="6" /> ';
		
		if(!$this->shadown_border){
			$border_options = array();
			$border_options[] = JHtml::_('select.option', 'none', 	'none',   'value', 'text');
			$border_options[] = JHtml::_('select.option', 'dotted', 'dotted', 'value', 'text');
			$border_options[] = JHtml::_('select.option', 'dashed', 'dashed', 'value', 'text');
			$border_options[] = JHtml::_('select.option', 'solid', 	'solid',  'value', 'text');
			$border_options[] = JHtml::_('select.option', 'double', 'double', 'value', 'text');
			$border_options[] = JHtml::_('select.option', 'groove', 'groove', 'value', 'text');
			$border_options[] = JHtml::_('select.option', 'ridge', 	'ridge',  'value', 'text');
			$border_options[] = JHtml::_('select.option', 'inset', 	'inset',  'value', 'text');
			$border_options[] = JHtml::_('select.option', 'outset', 'outset', 'value', 'text');
			
			$html .= JHtml::_('select.genericlist', $border_options, $this->formControl.'['.$this->group.']['.$this->element['name'].'_s]', '', 'value', 'text', $this->value_s, $this->id.'_s');
		}
		
		$html .= '<input type="text" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_color]" value="'.$this->value_color.'"'
				.' class="twoj_color"'
				.' size="5" style="width: 60px;" maxlength="7"'
				."> <input type='submit' name='transperent_color[]' value='".JText::_('COM_TWOJTOOLBOX_SET_TRANSPERENT', 1)."' class='button' onClick='ems_set_transp(\"".$this->formControl."[".$this->group."][".$this->element['name']."_color]\"); return false' />";
		return $html;
	}
}
