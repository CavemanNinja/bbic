<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.8 $
**/

defined('JPATH_BASE') or die;
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('twojtextarea');

class JFormFieldTwoJMultiValues extends JFormFieldTwoJTextArea{
	protected $type = 'TwoJMultiValues';
	public $hide = 0;
	
	protected function getInput(){
		$default= str_replace( "'", "\"", $this->element['default']);
		$hide_width 	= (int) (isset($this->element['hide_width']) && $this->element['hide_width']==1);
		$hide_style 	= (int) (isset($this->element['hide_style']) && $this->element['hide_style']==1);
		$hide_opacity 	= (int) (isset($this->element['hide_opacity']) && $this->element['hide_opacity']==1);
		if( isset($this->element['enabled_text']) && $this->element['enabled_text'] ) 	$enabled_text = JText::_($this->element['enabled_text']); 	else $enabled_text = JText::_('JENABLED');
		if( isset($this->element['disabled_text']) && $this->element['disabled_text'] ) $disabled_text = JText::_($this->element['disabled_text']); else $disabled_text = JText::_('JDISABLED');
		if( !$hide_style ){
			$border_options = array( JHtml::_('select.option', 'none', 	'none',   'value', 'text'), JHtml::_('select.option', 'dotted', 'dotted', 'value', 'text'), JHtml::_('select.option', 'dashed', 'dashed', 'value', 'text'), JHtml::_('select.option', 'solid', 	'solid',  'value', 'text'), JHtml::_('select.option', 'double', 'double', 'value', 'text'), JHtml::_('select.option', 'groove', 'groove', 'value', 'text'), JHtml::_('select.option', 'ridge', 	'ridge',  'value', 'text'), JHtml::_('select.option', 'inset', 	'inset',  'value', 'text'), JHtml::_('select.option', 'outset', 'outset', 'value', 'text') );
			$border_style = JHtml::_('select.genericlist', $border_options, 'twojmultivalues_style', 'class="twojmultivalues_inputbox"', 'value', 'text', 'none', 'twojmultivalues_style_id'.$this->id);
		}
		$javascript =  	'emsajax("#'.$this->id.'").data( {"default_value": '.$default.', "attr_id": "'.$this->id.'", "hide_width": '.$hide_width.', "hide_style": '.$hide_style.', "hide_opacity": '.$hide_opacity.', "start": 1} );'.
						'emsajax("#twojmultivalues_id'.$this->id.' .twojmultivalues_enabledbutton").button().bind("click", { attr_id: "'.$this->id.'", disabled_text:"'.$disabled_text.'", enabled_text:"'.$enabled_text.'" }, TwoJMultiBoardClick);'.
						'TwoJMultiBoardReadData("'.$this->id.'");';
		$mainDiv = '<div id="twojmultivalues_id'.$this->id.'" class="twojmultivalues">';
			$mainDiv .= '<button class="twojmultivalues_enabledbutton" id="twojmultivalues_enabledbutton_id'.$this->id.'"></button>';
			$mainDiv .= '<div class="twojmultivalues_line">'; 
				if( !$hide_width ) $mainDiv 	.= 	JText::_("Width").':	<input name="twojmultivalues_width" 	class="twojmultivalues_inputbox" size="2" maxlength="2" value="1" 		type="text"><span >px</span>&nbsp;&nbsp;&nbsp;&nbsp;';
				if( !$hide_style ) $mainDiv 	.= 	JText::_("Style").':'.	$border_style.'&nbsp;&nbsp;&nbsp;&nbsp;';
				$mainDiv 						.= 	JText::_("Color").':	<input name="twojmultivalues_color" 	class="twojmultivalues_inputbox" size="7" maxlength="7"	value="#000000" type="text" readonly="readonly">&nbsp;&nbsp;&nbsp;&nbsp;';
				if( !$hide_opacity ) $mainDiv 	.= 	JText::_("Opacity").':	<input name="twojmultivalues_opacity" 	class="twojmultivalues_inputbox" size="3" maxlength="3" value="100" 	type="text"><span >%</span>';
			$mainDiv .= '</div>';
		$mainDiv .= '</div>';
		
		$this->setXmlVal('class', ' twojToolBoxSaveItem');
		$this->element['filter']='raw';
		
		$parent_input = parent::getInput();
		JFactory::getDocument()->addScriptDeclaration('emsajax(document).ready(function(){'.$javascript.'});');
		return '<div>'.$mainDiv.'<div style="display: none;">'.$parent_input.'</div></div>';
	}
	
	protected function setXmlVal($title, $value){
		if( TJTB_JVERSION_FULL=='3.2') $this->$title = $value; 
			else  $this->element[$title] = $value;
	}
}
