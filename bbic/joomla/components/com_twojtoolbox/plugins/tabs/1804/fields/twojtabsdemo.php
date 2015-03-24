<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die;

jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('text');

class JFormFieldTwoJTabsDemo extends JFormFieldText{
	protected $type = 'TwoJTabsDemo';
	
	protected function getLabel(){
		if( JComponentHelper::getParams('com_twojtoolbox')->get('twojpreview', 0)==0 ){
			$type_plugin = $this->form->getValue('type');
			if(!$type_plugin) return false;
			$plugin_info  = TwojToolboxHelper::plugin_info( $type_plugin, 1);
			$twoj_add_css_field = JRequest::getVar('twoj_add_css_field', array(), '', 'array');
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.tabs';
			JRequest::setVar('twoj_add_css_field', $twoj_add_css_field );
			JFactory::getDocument()->addScriptDeclaration("
				var twojtoolbox_style_addurl = '".$type_plugin.'*'.$plugin_info->v_active."*css*';
			");
			$twoj_add_js_field = JRequest::getVar('twoj_add_js_field', array(), '', 'array');
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*admin*2j.demo';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.tabs';	
			JRequest::setVar('twoj_add_js_field', $twoj_add_js_field );
		}
		return parent::getLabel();
	}
	
	protected function getInput(){
		if( JComponentHelper::getParams('com_twojtoolbox')->get('twojpreview', 0) ) return  '<div class="twojtoolbox_form_addtext">'.JText::_('JDISABLED' ).'</div>';
		$ret_html = '<br /><div id="tabs_demo" style="display: none; float:left; height:220px;"></div>';
		return $ret_html;
	}
}