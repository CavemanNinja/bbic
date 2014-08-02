<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2013 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.17 $
**/

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('joomla.utilities.arrayhelper');

class JFormFieldIconSelect extends JFormFieldTwoJText{
	public $type = 'IconSelect';
	public $hide = 0;
	public $hideColor = 0;
	public $targetID = '';
	
	
	protected function getInput(){
		$this->hide = isset($this->element['hide']) ? $this->element['hide'] : 0;
		$this->hideColor = isset($this->element['hideColor']) ? $this->element['hideColor'] : 0;
		$this->targetID = isset($this->element['targetID']) ? $this->element['targetID'] : '';
		
		if( $json = (string)$this->element['json'] ){
			if(TJTB_JVERSION==3) $this->class=' twojtoolbox_fieldrefresh';
			else  $this->element['class']=' twojtoolbox_fieldrefresh';
		}
		
		$ret_html = '';
		
		if(!$this->targetID){
			$ret_html .= '<div id="'.$this->id.'_view" class="twojtoolbox_editIconSelectViewBlock"></div>';
			$ret_html .= '<div class="twoj_hiddenblock">'.parent::getInput()."</div>";
			$ret_html .= isset($this->element['addtext']) ? '<div class="twojtoolbox_form_addtext" style="margin-right: 10px;">'.$this->element['addtext']." </div>" : '';
			$ret_html .=  ' <button id="'.$this->id.'_editIconSelectButton" class="twojtoolbox_editIconSelectButton">'.JText::_('COM_TWOJTOOLBOX_ITEM_ICONSELECTBUTTON').'</button>';
			JFactory::getDocument()->addScriptDeclaration(' emsajax(document).ready( function(){ twojtoolbox_editIconSelectRefresh("'.$this->id.'", 2); twojtoolbox_editIconSelectEvent("'.$this->id.'", "'.$this->form->getValue('type').'", "page1", {hideColor:'.$this->hideColor.'}); }); ');
			if( $json ){ 
				JFactory::getDocument()->addScriptDeclaration(' emsajax(document).ready( function(){  emsajax("#'.$this->id.'").change( function(){ ems_twojtoolbox_onchange( this, '.$json.'); }); }); '); 
			}
		} else {
			JFactory::getDocument()->addScriptDeclaration('emsajax(document).ready( function(){ emsajax(\'<button id="'.$this->targetID.'_editIconSelectButton" class="twojtoolbox_editIconSelectButton">'.JText::_('COM_TWOJTOOLBOX_ITEM_ICONSELECTBUTTON').'</button>"\').insertAfter("#'.$this->targetID.'");'
			.' twojtoolbox_editIconSelectEvent("'.$this->targetID.'", "'.$this->form->getValue('type').'", "page1", {hideColor:'.$this->hideColor.', insert: 1});  }); ');
		} 
		return ($this->targetID ) ? '' : $ret_html;
	}
	
	protected function getLabel(){
		return ($this->targetID ) ? '' : parent::getLabel();
	}
}