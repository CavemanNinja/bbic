<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('joomla.utilities.arrayhelper');
JFormHelper::loadFieldClass('filelist');

class JFormFieldCssFileList extends JFormFieldFileList{
	public $type = 'CssFileList';
	public $hide = 0;
	public $json = '';
	
	protected function getInput(){
		if( isset($this->element['hide'])) $this->hide = $this->element['hide'];
		if( isset($this->element['json']) && $this->json = (string)$this->element['json']){
			$this->setXmlVal('class', ' twojtoolbox_fieldrefresh');
		}
		$ret_html = parent::getInput();
		$ret_html .= $this->element['addtext'] ? '<div class="twojtoolbox_form_addtext" style="margin-right: 10px;">'.$this->element['addtext'].' <strong>'.$this->getXmlVal('directory')."</strong></div>" : '';

		if( $this->element['editCssButton'] ){
			$ret_html .=  ' <button id="'.$this->id.'_editCssButton" class="twojtoolbox_editCssButton">'.JText::_('COM_TWOJTOOLBOX_ITEM_CSSEDITBUTTON').'</button><div id="'.$this->id.'_editCssInlineStatus" class="twojtoolbox_editCssStatus"></div>';
			JFactory::getDocument()->addScriptDeclaration(' emsajax(document).ready( function(){ twojtoolbox_editCssEvent("'.$this->id.'", "'.$this->form->getValue('type').'"); }); ');
		}
		
		if( $this->json ){ 
			JFactory::getDocument()->addScriptDeclaration(' emsajax(document).ready( function(){  emsajax("#'.$this->id.'").change( function(){ ems_twojtoolbox_onchange( this, '.$this->json.'); }); }); '); 
		}
		return $ret_html;
	}
	protected function getOptions(){	
		$type_plugin = $this->form->getValue('type');
		if(!$type_plugin) return false;
		$plugin_info  = TwojToolboxHelper::plugin_info( $type_plugin, 1);
		
		$this->setXmlVal('directory', str_replace(array('\\', '\\\\', '//'), '/', $this->element['directory']) );
		
		if($plugin_info){
			$this->setXmlVal('directory', 'components/com_twojtoolbox/plugins/'.$type_plugin.'/'.$plugin_info->v_active.'/'.( $this->getXmlVal('directory')? $this->getXmlVal('directory') :'css'));
		} else return false;
		
		if( !(string)$this->element['filter'] ) $this->setXmlVal('filter', '.css');
		

		if( TJTB_JVERSION_FULL=='3.2'){
			$this->setXmlVal('hideNone', 1);
			$this->setXmlVal('hideDefault', 1);
		} else {
			$this->setXmlVal('hide_none', 1);
			$this->setXmlVal('hide_default', 1);
		}		
		
		$options = parent::getOptions();
		
		$map_path = JPATH_ROOT.'/'.$this->getXmlVal('directory').'/'.($this->element['cssMap'] ? $this->element['cssMap'] :'css.map.txt');
		if( JFile::exists( $map_path ) ){ 
			$json_array = json_decode( JFile::read( $map_path ), true );
			if( $config !== NULL ){
				for($i=0;$i<count($options); $i++){
					if( isset($json_array[$options[$i]->text]) ) $options[$i]->text = $json_array[$options[$i]->text];
				}
			}
		}
		return $options;
	}
	
	protected function setXmlVal($title, $value){
		if(TJTB_JVERSION_FULL=='3.2') $this->$title = $value; 
			else  $this->element[$title] = $value;
	}
	protected function getXmlVal($title){
		if( TJTB_JVERSION_FULL=='3.2'){
			if(!isset($this->$title)) return false;
			return $this->$title; 
		} else {
			if(!isset($this->element[$title])) return false;
			return $this->element[$title];
		}
	}
}