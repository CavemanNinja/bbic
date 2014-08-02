<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('twojtext');

class JFormFieldRadius extends JFormFieldTwoJText{

	protected $type = 'radius';
	
	protected $value_left;
	protected $value_right;
	protected $value_top;
	protected $value_bottom;
	
	protected function getInput(){
		$this->value_left =  $this->form->getValue($this->element['name'].'_left', 	'params', 	$this->element['default_left']);
		$this->value_right = $this->form->getValue($this->element['name'].'_right', 'params', 	$this->element['default_right']);
		$this->value_top =   $this->form->getValue($this->element['name'].'_top', 	'params', 	$this->element['default_top']);
		$this->value_bottom =$this->form->getValue($this->element['name'].'_bottom','params', 	$this->element['default_bottom']);
			
		return'<table cellspacing="0" cellpadding="2" border="0"  align="left"><tr><td style="width: 50px;"></td>'
			.'<td><input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_top]" 	value="'.$this->value_top.'" style="float:none; width: 20px;" size="2" maxlength="3"> px</td>'
									.'<td style="width: 100px;"></td>'
									.'<td><input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_right]" value="'.$this->value_right.'" style="float:none; width: 20px;" size="2" maxlength="3"> px</td>'
									.'</tr><tr><td></td><td align="left" colspan="3"><img src="'.JURI::root().'components/com_twojtoolbox/css/img/radius.png" alt="" border="0" style="padding-left: 10px;"></td>'
									.'</tr><tr><td></td>'
									.'<td><input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_left]" 	value="'.$this->value_left.'" style="float:none; width: 20px;" size="2" maxlength="3"> px</td>'
									.'<td></td>'
									.'<td><input type="text" class="inputbox" name="'.$this->formControl.'['.$this->group.']['.$this->element['name'].'_bottom]" value="'.$this->value_bottom.'" style="float:none; width: 20px;" size="2" maxlength="3"> px</td>'
									.'</tr></table>';
	}
}
