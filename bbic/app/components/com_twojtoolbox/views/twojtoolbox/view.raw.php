<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class TwojtoolboxViewTwojtoolbox extends TwojJView{
	function display($tpl = null){
		
		if( JRequest::getInt('twoj_showPlugin', 0) || JRequest::getInt('twoj-show-plugin', 0) ){
			$id = JRequest::getInt('twoj_pluginId', 0);
			if(!$id) $id = JRequest::getInt('twoj-plugin-id', 0); 
			if($id){
				JLoader::register('TwojToolBoxSiteHelper', JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');
				if( !class_exists('TwojToolBoxSiteHelper') ) return '';
				$out = TwojToolBoxSiteHelper::getPluginContent( $id );
				echo $out;
				return ;
			}
		}
		
		if( JRequest::getInt('twoj_demosection', 0) ){
			$tpl = 'raw';
			$type_plugin = '';
			$params = JRequest::getVar('twoj_demosection_params', array(), 'POST', 'array');
			$correct_array = array();
			//print_r($params);
			for($i=0;$i<count($params);$i++){
				$cur_item = $params[$i];
				if( strpos($cur_item['name'],  'jform[params]')===0 ){
					$correct_name = preg_replace( '/jform\[params\]\[([a-zA-Z0-9_-]*)\]/', '$1', $cur_item['name'] );
					$correct_name_array = preg_replace( '/([a-zA-Z0-9_-]*)\[\]/', '$1', $correct_name );
					if( $correct_name!= $correct_name_array ){
						if( !isset($correct_array[$correct_name_array]) ) $correct_array[$correct_name_array] = array();
						$correct_array[$correct_name_array][] = $cur_item['value'];
					} else $correct_array[$correct_name] = $cur_item['value'];
				}
				if( $cur_item['name'] == 'jform[type]' ){
					$type_plugin = $cur_item['value'];
				}
			}
			
			//print_r($correct_array);
			
			if( !$type_plugin || !count($correct_array) ){
				return '';
			}
			
			require_once (JPATH_SITE.'/administrator/components/com_twojtoolbox/helpers/twojtoolbox.php');
			$plugin_info  = TwojToolboxHelper::plugin_info( $type_plugin, 1);
			
			require_once (JPATH_SITE.'/components/com_twojtoolbox/pluginclass.php');
			jimport('joomla.filesystem.file');
			$plugin_info->state = 1;
			$plugin_info->item_id = -1;
			$plugin_info->item_title = 'Demo title';
			$plugin_info->params = json_encode($correct_array);
			
			$plugin_classfile = JPATH_SITE.'/'.
								'components/'.
								'com_twojtoolbox/'.
								'plugins/'.
								$plugin_info->type.'/'.
								$plugin_info->v_active.'/'.
								'twoj_'.$plugin_info->type.'_plugin.php';
								
			if( !JFile::exists($plugin_classfile) ) return JText::_('COM_TWOJTOOLBOX_ERROR_FILE_ERROR');
			require_once ($plugin_classfile);
			$class = 'TwoJToolBox'.ucfirst($plugin_info->type);
			if (class_exists($class)) $instance = new $class($plugin_info);
				else return JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $class);
			if( $instance->error_text  ) return  $instance->error_text;
			echo $instance->getElement();
			echo '@*2jtoolbox_split_chere*@';
			echo $instance->javascript_code;
		}
	
		parent::display($tpl);
	}
}
