<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/
defined('_JEXEC') or die('Restricted Access');

if( !JFactory::getApplication()->isAdmin() ){
	$lang = JFactory::getLanguage();
	$lang->load('com_twojtoolbox');
}

abstract class TwojToolBoxSiteHelper{
	
	public static function PluginÑallback( $id ){
		$id = (int) $id;
		if( !$id  ) return JText::_('COM_TWOJTOOLBOX_ERROR_PLUGININFO');
		require_once (JPATH_SITE.'/components/com_twojtoolbox/plugincallback.php');
		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('plu.*, ite.id AS item_id, ite.title AS item_title, ite.params, ite.state');
		$query->where('ite.`id` = '.$id);
		$query->where('plu.`type` = ite.`type`');
		$query->where('ite.`state` = 1');
		$query->from('#__twojtoolbox AS ite, #__twojtoolbox_plugins AS plu');
		$db->setQuery($query); 
		if( !$plugin_info = $db->loadObject() ) return JText::_('COM_TWOJTOOLBOX_ERROR_PLUGININFO');
		$plugin_callbackfile = JPATH_SITE.'/'.
								'components/'.
								'com_twojtoolbox/'.
								'plugins/'.
								$plugin_info->type.'/'.
								$plugin_info->v_active.'/'.
								'twoj_'.$plugin_info->type.'_callback.php';
		if( !JFile::exists($plugin_callbackfile) ) return JText::_('COM_TWOJTOOLBOX_ERROR_FILE_ERROR');
		require_once ($plugin_callbackfile);
		$class = 'TwoJToolBox'.ucfirst($plugin_info->type).'CallBack';
		if (class_exists($class)) $instance = new $class($plugin_info);
			else return JText::sprintf('JLIB_APPLICATION_ERROR_INVALID_CONTROLLER_CLASS', $class);
		if( $instance->error_text  ) return  $instance->error_text;
		return $instance->outContent();
	}
	
	public static function PluginLess( $id, $cacheid, $uniqueId=0 ){
		$id = (int) $id;
		$cacheid = (int) $cacheid;
		if( !$id  ) return JText::_('COM_TWOJTOOLBOX_ERROR_PLUGININFO');
		require_once (JPATH_SITE.'/components/com_twojtoolbox/pluginclass.php');
		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('plu.*, ite.id AS item_id, ite.title AS item_title, ite.params, ite.state, ite.cacheid');
		$query->where('ite.`id` = '.$id);
		$query->where('plu.`type` = ite.`type`');
		$query->where('ite.`state` = 1');
		$query->from('#__twojtoolbox AS ite, #__twojtoolbox_plugins AS plu');
		$db->setQuery($query);
		if( !$plugin_info = $db->loadObject() ) return JText::_('COM_TWOJTOOLBOX_ERROR_PLUGININFO');
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
		return $instance->outLess($uniqueId);
	}
	
	protected static function _getPluginInfo($cid, $type = ''){
		$cid = (int) $cid;
		if( !$cid  ) return JText::_('COM_TWOJTOOLBOX_ERROR_PLUGININFO');
		
		require_once (JPATH_SITE.'/components/com_twojtoolbox/pluginclass.php');
		jimport('joomla.filesystem.file');
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('plu.*, ite.id AS item_id, ite.title AS item_title, ite.params, ite.state, ite.cacheid');
		$query->where('ite.id = '.(int) $cid.' AND plu.`type` = ite.`type`');
		$query->from('#__twojtoolbox AS ite, #__twojtoolbox_plugins AS plu');
		$db->setQuery($query);
		//echo $db->getQuery();
		if( !$plugin_info = $db->loadObject() ) return JText::_('COM_TWOJTOOLBOX_ERROR_PLUGININFO');
		if( $type && $plugin_info->type != $type ) return JText::_('COM_TWOJTOOLBOX_ERROR_PLUGININFO');
		if( $plugin_info->state != 1) return JText::_('COM_TWOJTOOLBOX_PLEASEPUBLIC');

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
		return $instance;
	}
	
	public static function parseMultiTag( $page_array, $type, $id ){
		$instance = self::_getPluginInfo($id, $type);
		if( !is_object($instance ) ) return '<br /><strong>[ 2J ToolBox: '.$instance.' ]</strong><br />'; ;
		$instance->page_array = $page_array;
		return $instance->getMultiElement();
	}
	
	public static function parseMultiTagCalback( $elem ){
		
		$regex_long = '/\{2jtoolbox_content\s+([a-z]*)\s+id:([0-9]*)\s+begin(\s+title:([^}]*)){0,1}\}.*\{2jtoolbox_content\s+\\1\s+id:\\2\s+end\}/isU';
		
		if( is_array($elem) && count($elem) >= 3 ){
			$id = (int) $elem[2];
			$type = $elem[1];
			$text_elements = $elem[0];
			$regex = '/{2jtoolbox_content\s+'.$type.'\s+id:'.$id.'((?:\s+begin)|(?:\s+end)|)(?:\s+title:([^}]*))?}/is';
			$page_array = $match = array();
			preg_match_all($regex, $text_elements, $match, PREG_SET_ORDER);
			if( is_array($match) && count($match)>=2 ){
				for($i=0;$i<(count($match)-1);$i++){
					$match_row = $match[$i];
					$match_nextrow = $match[$i+1];
					if(is_array($match_row) && count($match_row)>=2 ){
						$start_pos = stripos($text_elements, $match_row[0]);
						$start_pos += strlen ($match_row[0]);
						$end_pos = stripos($text_elements, $match_nextrow[0]);
						$title = ( isset($match_row[2]) ? $match_row [2] : '' );
						$temp_obj = new JObject;
						$temp_obj->conten = substr ( $text_elements, $start_pos, $end_pos - $start_pos  );
						if (strpos($temp_obj->conten, '{2jtoolbox_content') !== false){
							if( ($new_conten = preg_replace_callback($regex_long, 'TwojToolBoxSiteHelper::parseMultiTagCalback', $temp_obj->conten) )!==NULL ){
								$temp_obj->conten = $new_conten;
							}
						}
						$temp_obj->title = $title;
						$page_array[] = $temp_obj;
					}
				}
				return TwojToolBoxSiteHelper::parseMultiTag($page_array, $type, $id);
			}
		}
		return JText::_('COM_TWOJTOOLBOX_ERROR_PARSINGTAG');
	}
	
	public static function getPluginContent($id, $parent_article_id=0){
		$instance = self::_getPluginInfo($id);
		if( is_object($instance ) ){
			$instance->parent_article_id = $parent_article_id;
			return $instance->getElement();
		} else return '<br /><strong>[ 2J ToolBox: '.$instance.' ]</strong><br />'; ;
	}
	
	
	public static function content_cleanIntrotext($introtext){
		$introtext = str_replace('<p>', ' ', $introtext);
		$introtext = str_replace('</p>', ' ', $introtext);
		$introtext = strip_tags($introtext, '<a><em><strong>');
		$introtext = trim($introtext);
		return $introtext;
	}

	public static function content_truncate($html, $maxLength = 0){
		$printedLength = 0;
		$position = 0;
		$tags = array();
		$output = '';
		if (empty($html)) {
			return $output;
		}
		while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position)){
			list($tag, $tagPosition) = $match[0];
			// Print text leading up to the tag.
			$str = JString::substr($html, $position, $tagPosition - $position);
			if ($printedLength + JString::strlen($str) > $maxLength) {
				$output .= JString::substr($str, 0, $maxLength - $printedLength);
				$printedLength = $maxLength;
				break;
			}
			$output .= $str;
			$lastCharacterIsOpenBracket = (JString::substr($output, -1, 1) === '<');
			if ($lastCharacterIsOpenBracket) {
				$output = JString::substr($output, 0, JString::strlen($output) - 1);
			}
			$printedLength += JString::strlen($str);
			if ($tag[0] == '&') {
				// Handle the entity.
				$output .= $tag;
				$printedLength++;
			} else {
				// Handle the tag.
				$tagName = $match[1][0];
				if ($tag[1] == '/') {
					// This is a closing tag.
					$openingTag = array_pop($tags);

					$output .= $tag;
				} else if ($tag[JString::strlen($tag) - 2] == '/') {
					// Self-closing tag.
					$output .= $tag;
				} else {
					// Opening tag.
					$output .= $tag;
					$tags[] = $tagName;
				}
			}
			// Continue after the tag.
			if ($lastCharacterIsOpenBracket) {
				$position = ($tagPosition - 1) + JString::strlen($tag);
			} else {
				$position = $tagPosition + JString::strlen($tag);
			}
		}
		// Print any remaining text.
		if ($printedLength < $maxLength && $position < JString::strlen($html)) {
			$output .= JString::substr($html, $position, $maxLength - $printedLength);
		}
		// Close any open tags.
		while (!empty($tags)){
			$output .= sprintf('</%s>', array_pop($tags));
		}
		$length = JString::strlen($output);
		$lastChar = JString::substr($output, ($length - 1), 1);
		$characterNumber = ord($lastChar);
		if ($characterNumber === 194) {
			$output = JString::substr($output, 0, JString::strlen($output) - 1);
		}
		$output = JString::rtrim($output);
		return $output.'&hellip;';
	}

	public static function contentGroupBy($list, $fieldName, $article_grouping_direction, $fieldNameToKeep = null){
		$grouped = array();
		if (!is_array($list)) {
			if ($list == '')return $grouped;
			$list = array($list);
		}
		foreach($list as $key => $item){
			if (!isset($grouped[$item->$fieldName])) $grouped[$item->$fieldName] = array();
			if (is_null($fieldNameToKeep)) {
				$grouped[$item->$fieldName][$key] = $item;
			} else {
				$grouped[$item->$fieldName][$key] = $item->$fieldNameToKeep;
			}
			unset($list[$key]);
		}
		$article_grouping_direction($grouped);
		return $grouped;
	}

	public static function contentGroupByDate($list, $type = 'year', $article_grouping_direction, $month_year_format = 'F Y'){
		$grouped = array();
		if (!is_array($list)){
			if ($list == '') return $grouped;
			$list = array($list);
		}
		foreach($list as $key => $item){
			switch($type){
				case 'month_year':
					$month_year = JString::substr($item->created, 0, 7);
					if (!isset($grouped[$month_year])) {
						$grouped[$month_year] = array();
					}
					$grouped[$month_year][$key] = $item;
					break;
				case 'year':
				default:
					$year = JString::substr($item->created, 0, 4);
					if (!isset($grouped[$year])) $grouped[$year] = array();
					$grouped[$year][$key] = $item;
					break;
			}
			unset($list[$key]);
		}
		$article_grouping_direction($grouped);
		if ($type === 'month_year'){
			foreach($grouped as $group => $items){
				$date = new JDate($group);
				$formatted_group = $date->format($month_year_format);
				$grouped[$formatted_group] = $items;
				unset($grouped[$group]);
			}
		}
		return $grouped;
	}
	
	
	public static function imageResizeSave( $url ){
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.arrayhelper');
		jimport('joomla.filesystem.folder');
		if( strpos( $url, '?' )===false || strpos( $url, '&' )===false ){
			echo "2JToolbox: function image resize - input data error";
			return false;
		}
		$url_query = parse_url($url);
		if( !isset($url_query['query']) ){
			echo "2JToolbox: function image resize - input data error[query]";
			return false;
		}
		parse_str( $url_query['query'], $url_params );
		$params_obj = JArrayHelper::toObject($url_params, 'JObject');
		
		$id			= 	(int) $params_obj->get('id', 0);
		$ems_cache	= 	(int) $params_obj->get('ems_cache', 1);
		$max_width 	= 	(int) $params_obj->get('ems_max_width', 120);
		$max_height = 	(int) $params_obj->get('ems_max_height', 120);
		$bg			= 	$params_obj->get('ems_bg', 'transparent');

		$type_img	= 	$params_obj->get('ems_type_img', 'png');
		$type_res	= 	(int) $params_obj->get('ems_type_res', 0);
		$position	= 	(int) $params_obj->get('ems_position', 0);
	
		$ems_root	= 	(int) $params_obj->get('ems_root', 0);
		$debug 		= JRequest::getInt('ems_debug', 0);
		
		$crop = 0;
		if($type_res==2){
			$crop = 1;
			$type_res = 0;
		}
		if($type_res) 	$position = 0;
		
		if( $type_img!='png' && $type_img!='gif' ) $type_img = 'jpg';
		
		if($max_width==0) 	$max_width = 120;
		if($max_height==0) 	$max_height = 108;
		if( $max_width  < 2000) $max_width  = $max_width; 
		if( $max_height < 1000) $max_height = $max_height; 
	
		if( !$id ) {
			$image_filename_in	= $params_obj->get('ems_file', '');
			$image_filename = TwojToolboxHelper::path_twojcode($image_filename_in, 1);
		} else {
			$database =  JFactory::getDbo();
			$database->setQuery( "SELECT img FROM #__twojtoolbox_elements WHERE id = ".$id );
			$image_filename = $database->loadResult();
		}
	
		if( !$image_filename ){
			echo "input parametr error";
			return false;
		}
		$image_filename = str_replace('\\', '/', $image_filename);
		$image_filename = str_replace('/', '/', $image_filename);
	
		if($ems_root) $image = JPATH_SITE.'/'.$image_filename;
			else $image = JPATH_SITE.'/media/com_twojtoolbox/'.$image_filename;
	
		if( !JFile::exists( $image ) ){
			echo "2JToolbox: function image resize - file $image_filename read error";
			return false;
		}
	
		$file_size = ( function_exists('md5_file') ? md5_file($image) : filesize($image) );
		
		$name_string = ($id==0?str_replace(array('.', '(', ')'), '', $image_filename_in):$id)
		.'_size'.$max_width.'x'.$max_height
		.'_bg'.$bg
		.'_fs'.$file_size 
		.'_tr'.($crop?2:$type_res)
		.'_p'.$position
		.'.'
		.$type_img;
		
		$cacheFodler = JPATH_CACHE.'/twojtoolbox/';
		$cacheFodlerUrl = JURI::root(true).'/cache/twojtoolbox/';
		if(JComponentHelper::getParams('com_twojtoolbox')->get('twojcachefolder', 0)){
			$cacheFodler = JPATH_ROOT.'/media/com_twojtoolbox/cache/';
			$cacheFodlerUrl = JURI::root(true).'/media/com_twojtoolbox/cache/';
		}
		
		$resized = $cacheFodler.$name_string;
		$resized_url = $cacheFodlerUrl.$name_string;
		
		if( JFile::exists($resized)){
			$imageModified = @filemtime($image);
			$thumbModified = @filemtime($resized);
			if($imageModified<$thumbModified  ) {
				return $resized_url;
			}
		} else return false;
	}
	
	public static function scriptSave( $files_list='', $type = 'css', $noFileWrite = 0 ){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$break_symbol = $type=='js' ? ";\n" : '' ;
		if( !$files_list ){
			echo "2jToolbox: function css/js cache - input data error";
			return false;
		}
		
		$cacheFodler = JPATH_SITE.'/cache/twojtoolbox/';
		if(JComponentHelper::getParams('com_twojtoolbox')->get('twojcachefolder', 0)){
			$cacheFodler = JPATH_ROOT.'/media/com_twojtoolbox/cache/';
		}
		if( !JFolder::exists($cacheFodler)) JFolder::create($cacheFodler);
		
		$prepare_files_list = TwojToolBoxSiteHelper::prepareFilesName($files_list);
		$cache_files = $cacheFodler.$prepare_files_list.'.'.($type=='less'?'css':$type);
		$ret_text = "/* ".$files_list." */\n";
		$file_need = explode('2jbrs2', $files_list);
		for($i=0;$i<count($file_need);$i++){
			if($type=='less'){
				$fileInfo= explode('*', $file_need[$i]);
				if( count($fileInfo) == 2) 	$ret_text .= TwojToolBoxSiteHelper::PluginLess( $fileInfo[0], $fileInfo[1]);
				if( count($fileInfo) == 3) 	$ret_text .= TwojToolBoxSiteHelper::PluginLess( $fileInfo[0], $fileInfo[1], $fileInfo[2]);
				
			} else {
				if( strpos( $file_need[$i], '*')===false ){
					$prep_file_name = JPATH_SITE.'/components/com_twojtoolbox/' .$type.'/2j.'.$file_need[$i].'.'.$type;
					$prep_file_name_url = JURI::root(true).'/components/com_twojtoolbox/'.$type.'/2j.'.$file_need[$i].'.'.$type;
				} else {	
					$prep_file_name = JPATH_SITE.'/' .'components/com_twojtoolbox/plugins/'.str_replace('*', '/', $file_need[$i]).'.'.$type;
					$prep_file_name_url = JURI::root(true).'/components/com_twojtoolbox/plugins/'.str_replace('*', '/', $file_need[$i]).'.'.$type;
				}
				if( JFile::exists($prep_file_name) ){
					$c_file_content = JFile::read($prep_file_name);
					$c_file_content = str_replace( '%%'.strtoupper($type).'_URL%%',  dirname($prep_file_name_url), $c_file_content);
					$ret_text .= $c_file_content.$break_symbol;
				}
			}
		}
		if( !$noFileWrite ) JFile::write($cache_files, $ret_text);
		echo $ret_text;
	}
	
	public static function scriptCompile( $files_list='', $type = 'css' ){
		jimport('joomla.filesystem.file');
		if( !$files_list ){
			echo "2jToolbox: function css/js cache - input data error";
			return false;
		}
		$cacheFodler = JPATH_SITE.'/cache/twojtoolbox/';
		$cacheFodlerUrl = JURI::root(true).'/cache/twojtoolbox/';
		if(JComponentHelper::getParams('com_twojtoolbox')->get('twojcachefolder', 0)){
			$cacheFodler = JPATH_ROOT.'/media/com_twojtoolbox/cache/';
			$cacheFodlerUrl = JURI::root(true).'/media/com_twojtoolbox/cache/';
		}
		
		$prepare_files_list = TwojToolBoxSiteHelper::prepareFilesName($files_list);
		$cache_files = $cacheFodler.$prepare_files_list.'.'.($type=='less'?'css':$type);
		$cache_files_url = $cacheFodlerUrl.$prepare_files_list.'.'.($type=='less'?'css':$type);
		if( JFile::exists($cache_files)){
			if($type=='less') return $cache_files_url; //$false;
			$cacheFileModified = @filemtime($cache_files);
			$file_need = explode('2jbrs2', $files_list);
			$FileLastModified = 0;
			for($i=0;$i<count($file_need);$i++){
				$prep_file_name = JPATH_SITE.'/components/com_twojtoolbox/';
				if( strpos( $file_need[$i], '*')===false ) $prep_file_name .= $type.'/2j.'.$file_need[$i];
					else $prep_file_name .= 'plugins/'.str_replace('*', '/', $file_need[$i]);
				$prep_file_name .= '.'.$type;
				if( JFile::exists($prep_file_name) ){
					$FileModified = @filemtime($prep_file_name);
					if( $FileModified > $FileLastModified ) $FileLastModified = $FileModified;
				}
			}
			if($cacheFileModified > $FileLastModified  ) return $cache_files_url;
		}
		
		return false;
	}
	
	public static function prepareFilesName( $files_list='' ){
		if( function_exists('md5') ){
			$prepare_files_list = '2jtoolboxcache_'.md5( $files_list );
		} else {	
			$prepare_files_list = str_replace( array('2jbrs2', ';', '.', '*'), '', $files_list);
		}
		return $prepare_files_list;
	}
}

