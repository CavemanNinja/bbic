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

class JFormFieldTwoJGalleryDemo extends JFormFieldText{
	protected $type = 'TwoJGalleryDemo';

	protected function getLabel(){
		if( JComponentHelper::getParams('com_twojtoolbox')->get('twojpreview', 0)==0 ){
			$type_plugin = $this->form->getValue('type');
			if(!$type_plugin) return false;
			$plugin_info  = TwojToolboxHelper::plugin_info( $type_plugin, 1);
			require_once (JPATH_SITE.'/components/com_twojtoolbox/pluginclass.php');
			jimport('joomla.filesystem.file');
			$plugin_info->state = 1;
			$plugin_info->item_id = 0;
			$plugin_info->item_title = 'Demo title';
			$plugin_info->params = '{}';
			$plugin_classfile = JPATH_SITE.'/components/com_twojtoolbox/plugins/'.$plugin_info->type.'/'.$plugin_info->v_active.'/twoj_'.$plugin_info->type.'_plugin.php';
			if( !JFile::exists($plugin_classfile) ) return JText::_('COM_TWOJTOOLBOX_ERROR_FILE_ERROR');
			require_once ($plugin_classfile);
			$class = 'TwoJToolBox'.ucfirst($plugin_info->type);
			if (class_exists($class)) $instance = new $class($plugin_info);
				else return JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $class);
			if( $instance->error_text  ) return  $instance->error_text;


			JFactory::getDocument()->addScriptDeclaration("
				var twojtoolbox_stylerooturl = '".JURI::root()."';
				var twojtoolbox_style_addurl = '".$type_plugin.'*'.$plugin_info->v_active."*css*';
				var started = 0;
				emsajax(document).ready(function(){
					emsajax('.twojtoolbox_demo_section_wrap').show();
					emsajax('#plugin_demo_button').show()
						.button({
							icons: {
								primary: 'ui-icon-search'
							}
						});
				});
				function twojdemosection_click(){
					emsajax('.twojToolBoxSaveItem').trigger('twojToolBoxSaveItem'); 
					emsajax.post( '".JURI::root()."index.php?option=com_twojtoolbox&format=raw&twoj_demosection=1', 
						{ 'twoj_demosection_params': emsajax('#twojtoolbox_item').serializeArray() },  
						function(data) {
							//if(started) emsajax('#plugin_demo_frame > div > div.twojimageviewerv2_wrap').twojimageviewerv2Stop();
							emsajax('#plugin_demo_frame').html('Loading...');
							emsajax('#dynamic_css').remove();
							var data_split = data.split('@*2jtoolbox_split_chere*@');
							emsajax('#plugin_demo_frame').html(data_split[0]);
							emsajax('#plugin_demo_section').show();
							eval(data_split[1]);
							emsajax('#plugin_demo_frame').trigger('scrollstop');
							emsajax('#plugin_demo_frame').trigger('resize');
							started  = 1;
						}
					);
				};
			");

			$twoj_add_js_field = JRequest::getVar('twoj_add_js_field', array(), '', 'array');
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.preload';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.gallery';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.lb1';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.lb2';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.lb3';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.lb4';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.lb5';
			$twoj_add_js_field[] = $type_plugin.'*'.$plugin_info->v_active.'*js*2j.lb6';
			
			JRequest::setVar('twoj_add_js_field', $twoj_add_js_field );
			
			$twoj_add_css_field = JRequest::getVar('twoj_add_css_field', array(), '', 'array');
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.base';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.style0';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.style1';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.style2';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.style3';
			
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle0';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle1';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle2';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle3';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle4';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle5';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle6';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.gallery.innerstyle7';
			
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.lb1';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.lb2';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.lb3';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.lb4';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.lb5';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.lb5';
			$twoj_add_css_field[] = $type_plugin.'*'.$plugin_info->v_active.'*css*2j.lb6';
			
			JRequest::setVar('twoj_add_css_field', $twoj_add_css_field );
		}
		return ''; //parent::getLabel();
	}
	
	protected function getInput(){
		$ret_html = '';
		//if( JComponentHelper::getParams('com_twojtoolbox')->get('twojpreview', 0) ) return  '<div class="twojtoolbox_form_addtext">'.JText::_('JDISABLED' ).'</div>';
		//$ret_html = '<br /><div id="tabs_demo" style="display: none; float:left; height:220px;"></div>';
		return $ret_html;
	}
}