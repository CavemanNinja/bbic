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

class JFormFieldPadding extends JFormFieldText{
	protected $type = 'padding';
	protected $value_left;
	protected $value_right;
	protected $value_top;
	protected $value_bottom;
	
	
	protected function getInput(){
		$this->value_left 	= $this->form->getValue($this->element['name'].'_left', 	'params', 	$this->element['default_left']);
		$this->value_right 	= $this->form->getValue($this->element['name'].'_right', 'params', 	$this->element['default_right']);
		$this->value_top 	= $this->form->getValue($this->element['name'].'_top', 	'params', 	$this->element['default_top']);
		$this->value_bottom =$this->form->getValue($this->element['name'].'_bottom','params', 	$this->element['default_bottom']);
		
		return '<table cellspacing="0" cellpadding="2" border="0"  align="left"><tr>'
			.'<td align="center" colspan="3">&nbsp;&nbsp;&nbsp;<input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_top]" style="float:none;"	value="'.$this->value_top.'" size="2" maxlength="3"> px</td>'
			.'</tr><tr>'
			.'<td><input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_left]" style="float:none;" value="'.$this->value_left.'" 	size="2" maxlength="3"> px</td>'
			.'<td><img src="'.JURI::root().'components/com_twojtoolbox/css/img/pen.png" alt="" border="0"></td>'
			.'<td><input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_right]" style="float:none;"	value="'.$this->value_right.'" 	size="2" maxlength="3"> px</td>'
			.'</tr><tr>'
			.'<td align="center" colspan="3">&nbsp;&nbsp;&nbsp;<input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_bottom]" style="float:none;" value="'.$this->value_bottom.'" size="2" maxlength="3"> px</td>'
			.'</tr></table>';
	}
}
