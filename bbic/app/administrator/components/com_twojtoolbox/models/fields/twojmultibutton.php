<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('twojtextarea');

class JFormFieldTwoJMultiButton extends JFormFieldText{
	protected $type = 'TwoJMultiButton';
	public $hide = 0;
	
	protected $htmlButton = '';
	protected $htmlPanel = '';

	function getPanelField($fieldOption){
		if( property_exists($fieldOption, "textBefore"))  $this->htmlPanel .= '<span class="twoj_multibutton_panel_before">'.$fieldOption->textBefore.'</span>';
		if($fieldOption->type=='text'){
			$this->htmlPanel .= '<input name="'.$fieldOption->fieldname.'" class="twoj_multibutton_panel_element twoj_multibutton_panel_textbox'.(property_exists($fieldOption, "color")?' twoj_color':'').'" value="" '.( property_exists($fieldOption, "length")?' style="width: '.(int)$fieldOption->length.'px"':'').' type="text" />';
		}
		if($fieldOption->type=='select'){
			$selectOptions = array();
			if( count($fieldOption->values) ) foreach($fieldOption->values as $key => $value) $selectOptions[] =  JHtml::_('select.option', $key, 	$value,   'value', 'text');
			$this->htmlPanel .=  JHtml::_(
				'select.genericlist', 
				$selectOptions, 
				$fieldOption->fieldname, 
				'class="twoj_multibutton_panel_element twoj_multibutton_panel_selectbox"'.( property_exists($fieldOption, "length")?' style="width: '.(int)$fieldOption->length.'px"':''), 
				'value', 'text', ''
			);
		}
		if( property_exists($fieldOption, "textAfter") ) $this->htmlPanel .= '<span class="twoj_multibutton_panel_after">'.$fieldOption->textAfter.'</span>';
		if( !property_exists($fieldOption, "hideSplitter"))   $this->htmlPanel .= '<span class="twoj_multibutton_panel_splitter"> </span>';
	}
	
	function getPanel($buttonOption){
		$this->htmlPanel .= '<div ' 
			.'class="twoj_multibutton_panel" '
			.'id="twoj_multibutton_panel_id'.$buttonOption->idButton.'"  '
			.'data-idButton="'.$buttonOption->idButton.'" '
		.'>';
		if( count($buttonOption->fields) ){
			foreach($buttonOption->fields as $fieldname => $fieldOption){
				$fieldOption->fieldname = $fieldname;
				$this->getPanelField($fieldOption);
			}
		}
		$this->htmlPanel .= '</div>';
	}
	
	function getButton($buttonOption){
		$buttonText = $buttonTextOff = JText::_($buttonOption->text);
		if( isset($buttonOption->textDisable)) $buttonTextOff = JText::_($buttonOption->textDisable);
		$panelEnable = 0;
		if(isset($buttonOption->role) && $buttonOption->role == "showPanel" && $buttonOption->fields && count($buttonOption->fields)){
			$panelEnable = 1;
			$this->getPanel($buttonOption);
		}
		
		$this->htmlButton .= 
		'<input type="radio" '
			.'class="twoj_multibutton_button'.( isset($buttonOption->addClass) ? ' '.$buttonOption->addClass : '').'" '
			.'id="twoj_multibutton_button_id'.$this->id.'_'.$buttonOption->idButton.'" '
			.'name="twoj_multibutton_button_id'.$this->id.'"'
			.'data-panel="'.$panelEnable.'" '
			.'data-idmultibutton="'.$this->id.'" '
			.'data-idbutton="'.$buttonOption->idButton.'" '
			.'value="'.$buttonOption->idButton.'" '
		.'/><label  for="twoj_multibutton_button_id'.$this->id.'_'.$buttonOption->idButton.'">'.$buttonText.'</label>';
	}
	
	protected function getInput(){
		if( !isset($this->element['config']) ) return 'empty JSON Config';
		$config = json_decode(str_replace( "'", "\"", $this->element['config']));
		if( $config === NULL  )  return 'error in  JSON Config'; //json_last_error() != JSON_ERROR_NONE

		if( !count($config) ) return '';
		foreach($config as $idButton => $buttonOption){
			$buttonOption->idButton = $idButton;
			$this->getButton($buttonOption);
		}

	
		$this->setXmlVal('class', ' twojToolBoxSaveItem');
		$this->element['filter']='raw';
		
		return 
			'<div '
				.'id="twoj_multibutton_wrap_id'.$this->id.'" '
				.'class="twoj_multibutton_wrap" '
				.'data-idmultibutton="'.$this->id.'" '
				.'data-default=\''.$this->element['default'].'\' '
				.'data-start="1" '
			.'>'
				.'<div class="twoj_multibutton_buttonset_wrap" id="twoj_multibutton_buttonset_wrap_id'.$this->id.'" '.($this->element['hideButton']?' style="display: none;"':'').'>'.$this->htmlButton.'</div>'
				.'<div class="twoj_multibutton_panels_wrap" id="twoj_multibutton_panels_wrap_id'.$this->id.'">'.$this->htmlPanel.'</div>'
				.'<div class="twoj_multibutton_hidden_wrap twoj_hiddenblock">'.parent::getInput().'</div>'
			.'</div>';
	}
	
	protected function getLabel(){
		$buttonDefault = '<span id="twoj_multibutton_default_button_id'.$this->id.'" data-id="'.$this->id.'" style="display: none;" class="twoj_multibutton_default_button">[ set default ]</span>';
		return parent::getLabel().$buttonDefault;
	}
	
	protected function setXmlVal($title, $value){
		if(TJTB_JVERSION_FULL=='3.2') $this->$title = $value; 
			else  $this->element[$title] = $value;
	}
}