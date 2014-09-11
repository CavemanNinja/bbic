<?php
/**
* @package      2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright    Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.11 $
**/

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.database.table' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables/');

class TwojToolboxControllerAjax extends TwojController{
		
	public function callback(){
		$user = JFactory::getUser();
		if( !$user->authorise('core.edit', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		JLoader::register('TwojToolBoxSiteHelper', JPATH_SITE.'/components/com_twojtoolbox/helpers/twojtoolboxsite.php');
		if( $plugin_id = JRequest::getInt('plugin_id', 0) ){
			echo TwojToolBoxSiteHelper::PluginÑallback( $plugin_id );
		} else {
			echo JText::_('2jToolBox::Plugin callBack Error [1]');
		}
		return ;
	}

	
	public function new_image_upload(){
		$user		= JFactory::getUser();
		if( !$user->authorise('core.create', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		$root_folder = JPATH_SITE.'/media/com_twojtoolbox/';
		$dir_cur = JRequest::getVar('dir_cur', '', 'POST', 'string');
		$dir_cur = TwojToolboxHelper::path_twojcode($dir_cur, 1);
		$dir_cur = TwojToolboxHelper::pathCheck($dir_cur);
		$dir_cur .= '/';
		$userfile = JRequest::getVar('image_file', null, 'files', 'array');
		if (!(bool) ini_get('file_uploads') ) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLFILE');
			return false;
		}
		if (!is_array($userfile)) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_NO_FILE_SELECTED');
			return false;
		}
		if ($userfile['error'] || $userfile['size'] < 1  ) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLUPLOADERROR');
			return false;
		}
		$tmp_src	= $userfile['tmp_name'];
		$name_file	= JFile::makeSafe($userfile['name']);
		
		if( in_array( strtolower(JFile::getExt($name_file)), array('png', 'jpg', 'jpeg', 'gif')) && JFolder::exists($root_folder.$dir_cur) ){
			$version_temp = $root_folder.$dir_cur.$name_file;
			$i=0;
			while( JFile::exists( $version_temp ) ){
				$version_temp = $root_folder.$dir_cur.'('.++$i.')'.$name_file;
			}
			$tmp_src	= $userfile['tmp_name'];
			if( JFile::upload($tmp_src, $version_temp) ){
				echo '00allokmess00'; 
				return ;
			}
		}
		echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLUPLOADERROR');
		
	}
	
	public function new_folder(){
		$user		= JFactory::getUser();
		if( !$user->authorise('core.create', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		$root_folder = JPATH_SITE.'/media/com_twojtoolbox/';
		$new_folder = JRequest::getVar('new_folder', '', 'POST', 'string');
		$new_folder = TwojToolboxHelper::pathCheck($new_folder);
		
		$dir_cur = JRequest::getVar('dir_cur', '', 'POST', 'string');
		$dir_cur = TwojToolboxHelper::path_twojcode($dir_cur, 1);
		$dir_cur = TwojToolboxHelper::pathCheck($dir_cur);
		if($dir_cur=='') $dir_cur = '/';
		
		$new_folder = str_replace(' ', '_TWOJ_PRB_', $new_folder);
		$new_folder = JFolder::makeSafe($new_folder);
		$new_folder = str_replace( '_TWOJ_PRB_', ' ', $new_folder);
		
		if( $new_folder && $dir_cur ) {
			$new_folder_all = $root_folder.$dir_cur.'/'.$new_folder;
			$version_temp = $new_folder_all;
			$i=0;
			while( JFolder::exists( $version_temp ) ){
				$version_temp = $new_folder_all.'('.++$i.')';
			}
			JFolder::create($version_temp );
		} else 'error';
	}

	public function get_image_info($clear_file = 0){
		$twojtoolbox_imege = JRequest::getVar('twojtoolbox_imege', '', 'POST', 'string');
		$twojtoolbox_imege = TwojToolboxHelper::path_twojcode($twojtoolbox_imege, 1);
		$twojtoolbox_imege = JPATH_SITE.'/media/com_twojtoolbox/'.$twojtoolbox_imege;
		if(!$clear_file){
			$name_value = JFile::getName($twojtoolbox_imege);
			$format_value = strtoupper(JFile::getExt($twojtoolbox_imege));
			list($width, $height) = getimagesize($twojtoolbox_imege); 
			$res_value = $width.' x '.$height;
			$date_value = date ("m/d/y H:i:s", filemtime($twojtoolbox_imege));
			$size_value = TwojToolboxHelper::format_bytes(filesize($twojtoolbox_imege));
		} else {
			$name_value = '-';
			$format_value = '-';
			$res_value = '-';
			$date_value = '-';
			$size_value = '-';
		}
		$ret = '';
		if( JFile::exists( $twojtoolbox_imege) || $clear_file ) { 
			$ret .=
			'<div class="twojtoolbox_filesbrowse_rightpanel_row1">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_HEADERINFO_NAME')
			.'</div><div class="twojtoolbox_filesbrowse_rightpanel_row2">'.$name_value.'</div><div class="twoj_clear"></div>'
			.'<div class="twojtoolbox_filesbrowse_rightpanel_row1">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_HEADERINFO_FORMAT')
			.'</div><div class="twojtoolbox_filesbrowse_rightpanel_row2">'.$format_value.'</div><div class="twoj_clear"></div>'
			.'<div class="twojtoolbox_filesbrowse_rightpanel_row1">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_HEADERINFO_RES')
			.'</div><div class="twojtoolbox_filesbrowse_rightpanel_row2">'.$res_value.'</div><div class="twoj_clear"></div>'
			.'<div class="twojtoolbox_filesbrowse_rightpanel_row1">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_HEADERINFO_DATE')
			.'</div><div class="twojtoolbox_filesbrowse_rightpanel_row2">'.$date_value.'</div><div class="twoj_clear"></div>'
			.'<div class="twojtoolbox_filesbrowse_rightpanel_row1">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_HEADERINFO_SIZE')
			.'</div><div class="twojtoolbox_filesbrowse_rightpanel_row2">'.$size_value.'</div><div class="twoj_clear"></div>';
		}
		if($clear_file) return $ret; else echo $ret;
	}
	
	public function get_images_list(){
		$root_folder = JPATH_SITE.'/media/com_twojtoolbox/';
		
		$itsimage = JRequest::getVar('itsimage', 0, 'POST', 'int');
		$twojtoolbox_dir_in = JRequest::getVar('twojtoolbox_dir', '', 'POST', 'string');
		
		$twojtoolbox_dir_in_image = '';
		if($itsimage){
			$twojtoolbox_dir_in_image = $twojtoolbox_dir_in;
			$twojtoolbox_dir_in = dirname($twojtoolbox_dir_in);
			if( $twojtoolbox_dir_in=='.' ) $twojtoolbox_dir_in = '';
		}
		
		$twojtoolbox_dir_in = TwojToolboxHelper::path_twojcode($twojtoolbox_dir_in, 1);
		$twojtoolbox_dir_in = TwojToolboxHelper::pathCheck($twojtoolbox_dir_in);
		
		
		//$twojtoolbox_dir_in = JFolder::makeSafe($twojtoolbox_dir_in);
		
		$twojtoolbox_dir = $root_folder.$twojtoolbox_dir_in;
		
		$no_up_button = 0;
		
		if($twojtoolbox_dir_in!='') $twojtoolbox_dir .= '/';
		
		if( JFolder::exists($twojtoolbox_dir) ) {
			if( $twojtoolbox_dir_in!='' ){ 
				$up_level =  dirname($twojtoolbox_dir).'/';
				if( !JFolder::exists($up_level) ) $up_level = $root_folder;
			} else $up_level = $root_folder;
			
			if($twojtoolbox_dir_in=='') $no_up_button = 1;
			
			$up_level = TwojToolboxHelper::pathCheck(str_replace( $root_folder, '', $up_level));
	
			$folders_list = JFolder::folders($twojtoolbox_dir);
			
			//natcasesort($files);
			echo '<div class="twojtoolbox_filesbrowse_path_label">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_PATHTEXT').'</div>';
			echo '<div class="twojtoolbox_filesbrowse_path">'.$twojtoolbox_dir.'</div>';
			if(!$no_up_button){
			echo '<button  class="twojtoolbbox_filesbrowse_buttonup ui-corner-all">
						'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_FOLDERUP').'
						<span class="twojtoolbox_filesbrowse_filename_hidden">'.$up_level.'</span>
					</button>';
			}

			echo '<button  class="twojtoolbbox_filesbrowse_newfolder ui-corner-all">
						'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_NEWFOLDER').'
						<span class="twojtoolbox_filesbrowse_filename_hidden">'.TwojToolboxHelper::path_twojcode(TwojToolboxHelper::pathCheck(str_replace( $root_folder, '',$twojtoolbox_dir ))).'</span>
				</button>';
			
			echo '<div class="twoj_clear"></div>
				<div class="twojtoolbox_filesbrowse_rightpanel">
					<div class="twojtoolbox_filesbrowse_rightpanel_headertext">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_HEADERPREVIEW').'</div>
					<div class="twojtoolbox_filesbrowse_rightpanel_preview">
						<a class="twojtoolbox_filesbrowse_rightpanel_preview_link" href="#" title="'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_PREVIEWTEXT').'" alt="'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_PREVIEWTEXT').'">
						<div class="twojtoolbox_filesbrowse_rightpanel_preview_inner"></div>
						</a>
					</div>
					
					<div class="twojtoolbox_filesbrowse_rightpanel_info ui-corner-all">
						'.TwojToolboxControllerAjax::get_image_info(1).'
					</div>
					<div class="twojtoolbox_filesbrowse_rightpanel_headertext">'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_HEADERUPLOAD').'</div>
					<div class="twojtoolbox_filesbrowse_rightpanel_upload ui-corner-all">
						<form enctype="multipart/form-data"  action="'.JRoute::_('index.php?option=com_twojtoolbox&view=plugins').'" method="post" name="adminFormPluginUpload" id="adminFormPluginUpload">
							<div id="twojtoolbox_selectfilepanel">
								'.JText::_('COM_TWOJTOOLBOX_FILEBROWSE_UPLOADTEXT').'<br />
								<input type="file" id="twojtoolbox_filesbrowse_field_uploadimage" name="image_file" size="35" class="inputbox" /><br />
								<button type="button" id="twojtoolbox_buttonupload">'.JText::_('COM_TWOJTOOLBOX_FILELIST_UPLOADBUTTON').'</button>
							</div>
							<div id="twojtoolbox_loadingpanel"></div>
							<input type="hidden" name="task" value="install" />
							'.JHtml::_('form.token').'
						</form>
					</div>
				</div>
				<br />
				<div class="twojtoolbox_filesbrowse_leftpanel">';
			
			if(count($folders_list)){
				foreach( $folders_list as $c_folder ) {
					if( JFolder::exists( $twojtoolbox_dir.$c_folder.'/') ) {
						$folder_ot = str_replace( $root_folder, '',$twojtoolbox_dir.$c_folder.'/' );
						$folder_ot = TwojToolboxHelper::path_twojcode($folder_ot);
						
						echo '
						<div class="twojtoolbbox_filesbrowse_folder">
							<div class="twojtoolbbox_filesbrowse_file_inner">
								<span class="twojtoolbox_filesbrowse_filename">'.htmlentities($c_folder).'</span>
								<span class="twojtoolbox_filesbrowse_filename_hidden">'.$folder_ot.'</span>
							</div>
						</div>';
					}
				}
			}
			$files_list = JFolder::files($twojtoolbox_dir, '.jpg|.jpeg|.gif|.png|.JPG|.JPEG|.GIF|.PNG');
			if(count($files_list)){
				foreach( $files_list as $c_file ) {
					if( JFile::exists( $twojtoolbox_dir . $c_file) ) {
						$folder_ot =str_replace( $root_folder, '',$twojtoolbox_dir );
						$image_url = $folder_ot.$c_file;
						$sel_add = ($itsimage && $twojtoolbox_dir_in_image==$image_url ? ' twojtoolbox_filesbrowse_file_select': '');
						$image_url = TwojToolboxHelper::path_twojcode($image_url);
						echo '
						<div class="twojtoolbbox_filesbrowse_file'.$sel_add.'" style="background: url(\''.JURI::root().'index.php?option=com_twojtoolbox&task=ajax.twojtoolbox_image_resize&format=raw&ems_file='.$image_url.'&ems_max_width=55&ems_max_height=41\') center 3px no-repeat transparent;">
							<div class="twojtoolbbox_filesbrowse_file_inner">
								<span class="twojtoolbox_filesbrowse_filename">'.htmlentities($c_file).'</span>
								<span class="twojtoolbox_filesbrowse_filename_hidden">'.$image_url.'</span>
							</div>
						</div>';
					}
				}
			}
			echo '</div>'; 
		}
	}
	
	public function delete_plugin(){
		$user		= JFactory::getUser();
		if( !$user->authorise('core.admin', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		$plugins_delete = explode(",", JRequest::getVar('list_delete', '', 'POST', 'string') );
		$delete_option = JRequest::getInt('delete_option', 0, 'POST');
		if( count($plugins_delete) && $delete_option ){
			$row	= JTable::getInstance('Plugin', 'TwojToolboxTable');
			foreach($plugins_delete as $plugin_id){
				$row->load($plugin_id);
				$type_plugin =  $row->type;
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				
				if( $delete_option==1 ){
					if($row->images){
						$media_path = JPATH_SITE.'/media/com_twojtoolbox/'.$type_plugin;
						if(JFolder::exists($media_path)) JFolder::delete($media_path);
					}
					$plugin_dir = JPATH_SITE.'/components/com_twojtoolbox/plugins/'.$type_plugin;
					if(JFolder::exists($plugin_dir)) JFolder::delete($plugin_dir);
				}
				if( $delete_option<3 ){
					$db->setQuery('DELETE elem FROM #__twojtoolbox_elements AS elem INNER JOIN #__twojtoolbox AS item ON ( elem.catid = item.id  AND  item.type = '.$db->quote($type_plugin).')');
					if( !$db->Query() ){
						echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_ERROR_DB');
						return ;
					}
					
					$query->delete('#__twojtoolbox')->where('type='.$db->quote($type_plugin));
					$db->setQuery($query);
					if( !$db->Query() ){
						echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_ERROR_DB');
						return ;
					}
				}
				
				
				$query->clear();
				$query->select('extension_id');
				$query->from('#__extensions');
				$query->where('type="component" AND element="com_2j'.$type_plugin.'"');
				$db->setQuery( (string) $query);
				$id_component = $db->loadResult();
				if($id_component){
					$installer = new JInstaller();
					$installer->uninstall('component', $id_component);
				}
				
				$query->clear();
				$query->delete('#__twojtoolbox_plugins')->where('type='.$db->quote($type_plugin));
				$db->setQuery($query);
				if( !$db->Query() ){
					echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_ERROR_DB');
					return ;
				}
			}
			echo 1;
		} else echo JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_ERROR_SELECTITEM');
		
		return ;
	}
	
	public function saveversion(){
		$type_select = JRequest::getVar('type_select', '', 'POST', 'string');
		$version_select = JRequest::getVar('version_select', '', 'POST', 'string');
		if( $type_select && $version_select ){
			$row	= JTable::getInstance('Plugin', 'TwojToolboxTable');
			$row->load( array('type'=> $type_select ));
			if( $row->install ){
				$row->v_active = $version_select;
				if( $row->store() ) echo '1';
			}
		}
		return ;
   }
   
   function updatelisting(){
		$pdata = JRequest::getVar('pdata', array(), 'POST', 'ARRAY');
		if( isset($pdata['items']) && count($pdata['items']) ){ 
			foreach($pdata['items'] as $item){
				if( isset($item) ) {
					$row = JTable::getInstance('Plugin', 'TwojToolboxTable');
					$type = trim((string) $item['type']);
					$row->load( array('type'=> $type ));
					$row->type = $type;
					$row->title = (string) $item['title'];
					if( isset($item['desc']) &&  $desc = $item['desc'] ) $row->desc = $desc;
					if( isset($item['desc_small']) &&  $desc_small = $item['desc_small'] ) $row->desc_small = $desc_small;
					$row->v_server = $item['version'];
					if( !$row->install ) $row->adddate = time(); 	
					if( isset($item['ordering']) ) $row->ordering = (int) $item['ordering'];
					if( isset($item['price']) ) $row->price = trim($item['price']);
					$row->store();
				}
			}
			
			if( isset($pdata['news']) && count($pdata['news']) ){
				foreach($pdata['news'] as $news){
					if( isset($news) ) {
						$row_n = JTable::getInstance('News', 'TwojToolboxTable');
						$id = (int) $news['id'];
						$row_n->load( array('id_news'=> $id  )  );
						$row_n->id_news = $id;
						$row_n->message = trim((string) $news['message']);
						if( !$row_n->date_in ) $row_n->date_in = time();
						if( !$row_n->read ) $row_n->read = 0; 
						$row_n->store();
					};
				} 
			};
			
			$row_c = JTable::getInstance('Config', 'TwojToolboxTable');
			$row_c->load(1);
			if( isset($pdata['toolbox']) && ( (int) $pdata['toolbox'] ) ) $row_c->version_available = $pdata['toolbox'];
			$row_c->update = time();
			$row_c->store();
		}
		echo 1;
	}
	
	
	function btGetType(){
		$tag = JRequest::getInt('tag', 0);
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('type,title');
		$query->from('#__twojtoolbox_plugins');
		$query->order('title');
		$query->where('install = 1 '.($tag?' AND multitag = 1':''));
		$db->setQuery((string)$query);
		$elements = $db->loadObjectList();
		$options = array();
		if ($elements ){
			foreach($elements as $element){
				$options[] = JHtml::_('select.option', $element->type, $element->title );
			}
			echo '00allokmess00';
			echo JHtml::_('select.genericlist', $options, 'twojtoolbox_select_type', 'id="twojtoolbox_select_type" size="1"', 'value', 'text');
		} else echo  JText::_('COM_TWOJTOOLBOX_ERROR_MULTITAG_NOPLUGIN');
		
		
		return ;
	}
	
	
	function btGetItem(){

		$type = JRequest::getString('type', '');
		if( !$type){
			echo JText::_('COM_TWOJTOOLBOX_ERROR_SELECTTAG');
			return ;
		}
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,title');
		$query->from('#__twojtoolbox');
		$query->order('title');
		$query->where('type = '.$db->quote($type).' AND state=1');
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items ){
			foreach($items as $item){
				$options[] = JHtml::_('select.option', $item->id, $item->title );
			}
			echo '00allokmess00';
			echo JHtml::_('select.genericlist', $options, 'twojtoolbox_select_item', 'id="twojtoolbox_select_item" size="1"', 'value', 'text');
		} else echo  JText::_('COM_TWOJTOOLBOX_ERROR_MULTITAG_NOPLUGIN');
		
		
		return ;
	}
	
	public function enable_plugin(){
		$type = JRequest::getString('type', '');
		TwojToolboxHelper::enable_plugin($type);
	}
	
	public function reeinstall(){
		$type = JRequest::getString('type', '');
		TwojToolboxHelper::reeinstall($type);
	}
	
	public function about(){
		echo TwojToolboxHelper::about( JRequest::getString('after_install', '0') );
	}
	
	function updateNewsStatus(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->SET('`read` = 1');
		$query->update('#__twojtoolbox_news');
		$query->where('`read` = 0');
		$db->setQuery($query);
		$db->query();
		return ;
	}
	
	function getIconSelectList( $write = 0 ){
		$user		= JFactory::getUser();
		if( !$user->authorise('core.admin', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		$iconList = 'ERROR: Icons list file do not exist';
		$type = JRequest::getString('plugin', '');
		$page = JRequest::getString('page', '');
		if(!$page){
			$page = 'css/font-awesome/allIcon';
		}elseif( strpos( $page, '@@ROOT@@' ) !== false ){
			$page = 'css/font-awesome/'.str_replace( '@@ROOT@@', '', $page);
		}else {
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('v_active');
			$query->from('#__twojtoolbox_plugins');
			$query->where('type = '.$db->quote($type).' AND install=1');
			$db->setQuery((string)$query);
			$v_active = $db->loadResult();
			if( !$v_active){ echo JText::_('COM_TWOJTOOLBOX_ERROR_SELECTICONFILE'.':: error 3'); return ; }
			$page = 'plugins/'.$type.'/'.$v_active.'/icon/'.$page;
		}
		if( JFile::exists(  JPATH_SITE.'/components/com_twojtoolbox/'.$page.'.html'  )  ){
			 $iconList = JFile::read(JPATH_SITE.'/components/com_twojtoolbox/'.$page.'.html' );
		}
		echo $iconList;
		return ;
	}
	
	function getCssFile( $write = 0, $cssData='' ){
		$user		= JFactory::getUser();
		if( !$user->authorise('core.admin', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		$type = JRequest::getString('plugin', '');
		$cssname = JFile::makeSafe(JRequest::getString('cssname', ''));
		if( !$type || !$cssname || JFile::getExt($cssname)!='css' ){
			echo JText::_('COM_TWOJTOOLBOX_ERROR_SELECTCSSFILE'.':: error 1');
			return ;
		}
		if($write && !$cssData ){
			echo JText::_('COM_TWOJTOOLBOX_ERROR_SELECTCSSFILE'.':: error 2');
			return ;
		}
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('v_active');
		$query->from('#__twojtoolbox_plugins');
		$query->where('type = '.$db->quote($type).' AND install=1');
		$db->setQuery((string)$query);
		$v_active = $db->loadResult();
		if( !$v_active){
			echo JText::_('COM_TWOJTOOLBOX_ERROR_SELECTCSSFILE'.':: error 3');
			return ;
		}
		$css_path = JPATH_SITE.'/components/com_twojtoolbox/plugins/'.$type.'/'.$v_active.'/css/'.$cssname;
		if( JFile::exists($css_path) ){
			if($write){
				if( JFile::write($css_path, $cssData) ){
					echo 1;
					return ;
				}
			} elseif( $cssData = JFile::read($css_path) ){
				echo $cssData;
				return ;
			}
		}
		echo JText::_('COM_TWOJTOOLBOX_ERROR_SELECTCSSFILE'.':: error 4');
		return ;
	}
	
	function setCssFile(){
		$cssData = JRequest::getVar('cssData', '', 'post', 'STRING', JREQUEST_ALLOWRAW);
		if( !$cssData ){
			echo JText::_('COM_TWOJTOOLBOX_ERROR_SELECTCSSFILE'.':: error 5');
			return ;
		}
		$this->getCssFile(1, $cssData);
	}
	
	
	function scanFoldersFilesRead(){
		$readDataList = array( 'folderSource' => '', 'folders' => array(), 'images' => array(), 'status' => 0);
		$jsonString = '{"folderSource": "", "status": 0}';
		
		$scanFolderPatch = JRequest::getVar('scanFolderPatch', '', 'get', 'STRING');
		$scanFolderSource = JRequest::getVar('scanFolderSource', '', 'get', 'STRING');
		$scanFolderOption = JRequest::getVar('scanFolderOption', '', 'get', 'INTEGER');
		
		$scanFolderPatch = str_replace( array( '\\', '//', '/../', '../', '/..'), '/', TwojToolboxHelper::pathCheck( $scanFolderPatch ));
		$scanFolderSource = str_replace( array( '\\', '//', '/../', '../', '/..'), '/', TwojToolboxHelper::pathCheck( $scanFolderSource ));
		
		if( $scanFolderOption==1 ){
			$scanFolderSource = dirname($scanFolderSource);
			if($scanFolderSource=='.') $scanFolderSource = ''; 
			$scanFolderPatch = '';
		}
		if( $scanFolderOption==2 ){
			$scanFolderSource = ''; 
			$scanFolderPatch = '';
		}
		
		$patchFlag = 0;
		if( $scanFolderPatch!='' && $scanFolderPatch !='/' ) $patchFlag = 1; 
		
		$sourceFlag = 0;
		if( $scanFolderSource!='' && $scanFolderSource !='/' ) $sourceFlag = 1; 
		
		$scanFolderPatchRoot = JPATH_ROOT.'/'.($sourceFlag?$scanFolderSource.'/':'').$scanFolderPatch;
		
		if( $patchFlag || $sourceFlag ) $readDataList['folders'][] = 'TwojUpFolder';
		if(JFolder::exists($scanFolderPatchRoot)){ 
			$readDataList['folderSource'] = ( $sourceFlag ? $scanFolderSource.'/' : '' ).$scanFolderPatch;
			JFactory::getApplication()->setUserState('com_twojtoolbox.edit.element.scan.path', $readDataList['folderSource']);
			$readDataList['folders'] = array_merge( $readDataList['folders'], JFolder::folders( $scanFolderPatchRoot ));
			$imageListing =  JFolder::files( $scanFolderPatchRoot, '.jpg|.png|.gif|.jpeg|.JPG|.PNG|.GIF|.JPEG' );
			$imagesArray = array();
			for($i=0;$i<count($imageListing);$i++){
				$imagesArray[$i]['name'] = $imageListing[$i];
				$imagesArray[$i]['nameStripExt'] = JFile::stripExt($imageListing[$i]);
				$imagesArray[$i]['href'] = JURI::root().($sourceFlag?$scanFolderSource.'/':'').$scanFolderPatch.'/'.$imageListing[$i];
				$twoj_path = TwojToolboxHelper::path_twojcode( ($sourceFlag?$scanFolderSource.'/':'').$scanFolderPatch.'/'.$imageListing[$i] );
				$imagesArray[$i]['resizeUrl'] = JURI::root().'index.php?option=com_twojtoolbox&task=ajax.twojtoolbox_image_resize&format=raw&ems_max_width=150&ems_max_height=70&ems_root=1&ems_file='.$twoj_path; //&ems_cache=0
				$size = getimagesize($scanFolderPatchRoot.'/'.$imageListing[$i] );
				$imagesArray[$i]['res'] = $size[0].'x'.$size[1];
			}
			$readDataList['images'] = $imagesArray;
			$readDataList['status'] = 1;
			$jsonString = json_encode($readDataList);
		}
		echo  $jsonString;
	}
	
	function scanFoldersFileCopy(){
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$app = JFactory::getApplication();
		
		$dir_upload =  JPATH_ROOT.'/media/com_twojtoolbox/';
		
		$jform = JRequest::getVar('jform', array(), 'POST', 'array');
		
		$folderlist = JRequest::getVar( 'folderlist', '', 'post', 'string' );
		$state = (int)  JRequest::getVar( 'state', 0, 'post', 'integer' );
		$language = JRequest::getVar( 'language', '', 'post', 'string' );
		$foldernew = JRequest::getVar( 'foldernew', '', 'post', 'string' );
		
		$folderlist = TwojToolboxHelper::pathCheck($folderlist);
		if(JFolder::exists($dir_upload.$folderlist)){
			$dir_upload .= $folderlist.'/';
		} else {
			$folderlist = '';
		}
		
		if( $foldernew ){
			$foldernew = TwojToolboxHelper::pathCheck(JFolder::makeSafe($foldernew));
			if( JFolder::exists($dir_upload.$foldernew) || JFolder::create($dir_upload.$foldernew) ) {
				$folderlist.= ($folderlist?'/':'').$foldernew;
				$dir_upload.=$foldernew.'/'; 
			} else {
				echo JText::_('COM_TWOJTOOLBOX_ERROR_CREATEFOLDER');
				return ;
			}
		}
		
		$catid 	= JRequest::getVar( 'category_id', 0, 'post', 'integer' );
		
		$path 	= TwojToolboxHelper::pathCheck( JRequest::getVar( 'scanFolderSource', 'images', 'post', 'string' ) );
		$img_path = JPATH_ROOT.'/'.$path.'/';
		
		$image_name_org  = JRequest::getVar( 'image_filename', '', 'post', 'string' );
		
		$image_name = JFile::makeSafe($image_name_org);
		
		if( $catid  && $path && JFolder::exists($img_path) && JFile::exists( $img_path.$image_name_org ) ){

				$item_name = JRequest::getVar( 'item_name', '', 'post', 'string' );
				$item_desc = JRequest::getVar( 'item_desc', '', 'post', 'string' );
				
				if (JFile::exists( $dir_upload.$image_name )){ 
					$k=1;
					$ext_only = JFile::getExt($image_name);
					$name_only = JFile::stripExt($image_name);
					$version_temp = $name_only.'('.$k.').'.$ext_only;
					while( JFile::exists( $dir_upload.$version_temp ) ){
						$version_temp = $name_only.'('.++$k.').'.$ext_only;
					}
					$image_name = $version_temp;
				}
				
				
 				if( JFile::copy( $img_path.$image_name_org, $dir_upload.$image_name) ){
					$row = JTable::getInstance('Element', 'TwojToolboxTable');
					$row->load(0);
					$row->title = ($item_name?$item_name:$image_name);
					$row->desc = isset($item_desc)?$item_desc:'';
					$row->img =  TwojToolboxHelper::pathCheck( $folderlist.'/'.$image_name);
					$row->language = $language;
					$row->state = $state;
					$row->catid = $catid;
					$row->check();
					$row->store();
				}
			echo 1;
			return ;
		}
		echo JText::_('COM_TWOJTOOLBOX_UPLOAD_UPLOADIMAGES_ERROR');
		return ;
	}
	
	function batchUpload(){
		
		$user		= JFactory::getUser();
		if( !$user->authorise('core.create', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		
		$imageFiles = JRequest::getVar('imageFiles', null, 'files', 'array');
		if (!(bool) ini_get('file_uploads') ) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLFILE');
			return false;
		}
		if (!is_array($imageFiles)) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_NO_FILE_SELECTED');
			return false;
		} 

		if ($imageFiles['error'] || $imageFiles['size'] < 1  ) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLUPLOADERROR');
			return false;
		}
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$app = JFactory::getApplication();
		
		$dir_upload =  JPATH_ROOT.'/media/com_twojtoolbox/';
		
		$jform = JRequest::getVar('jform', array(), 'POST', 'array');
		
		$folderlist = JRequest::getVar( 'folderlist', '', 'post', 'string' );
		$state = (int)  JRequest::getVar( 'state', 0, 'post', 'integer' );
		$language = JRequest::getVar( 'language', '', 'post', 'string' );
		$foldernew = JRequest::getVar( 'foldernew', '', 'post', 'string' );
		
		$folderlist = TwojToolboxHelper::pathCheck($folderlist);
		if(JFolder::exists($dir_upload.$folderlist)){
			$dir_upload .= $folderlist.'/';
		} else {
			$folderlist = '';
		}
		
		if( $foldernew ){
			$foldernew = TwojToolboxHelper::pathCheck(JFolder::makeSafe($foldernew));
			if( JFolder::exists($dir_upload.$foldernew) || JFolder::create($dir_upload.$foldernew) ) {
				$folderlist.= ($folderlist?'/':'').$foldernew;
				$dir_upload.=$foldernew.'/'; 
			} else {
				echo JText::_('COM_TWOJTOOLBOX_ERROR_CREATEFOLDER');
				return ;
			}
		}

		$catid 	= JRequest::getVar( 'category_id', 0, 'post', 'integer' );
		
		$ret = array();
		
		
		$image_name_org  = $imageFiles["name"];
		$image_name = JFile::makeSafe($image_name_org);
		$image_name = str_replace( ' ', '_', $image_name);
		$name_only = JFile::stripExt($image_name);
		$ext_only = strtolower(JFile::getExt($image_name));
		if(! $name_only ){
			$name_only = 'image';
			$image_name = $name_only.'.'.$ext_only;
		}
			
		if( $catid  && in_array( $ext_only, array('png', 'jpg', 'jpeg', 'gif')) ){
	
			if (JFile::exists( $dir_upload.$image_name )){ 
				$k=1;
				$version_temp = $name_only.'_copy'.$k.'.'.$ext_only;
				while( JFile::exists( $dir_upload.$version_temp ) ){
					$version_temp = $name_only.'_copy'.++$k.'.'.$ext_only;
				}
				$image_name = $version_temp;
			}
				
				
			$tmp_src	= $imageFiles['tmp_name'];

			if( JFile::upload($tmp_src, $dir_upload.$image_name) ){
				$ret[] = $image_name;
				$row = JTable::getInstance('Element', 'TwojToolboxTable');
				$row->load(0);
				$row->title = $image_name;
				$row->desc = '';
				$row->img =  TwojToolboxHelper::pathCheck( $folderlist.'/'.$image_name);
				$row->language = $language;
				$row->state = $state;
				$row->catid = $catid;
				$row->check();
				$row->store();
			}
		} else echo JText::_('COM_TWOJTOOLBOX_UPLOAD_UPLOADIMAGES_CHECK_ERROR');
		
		if( count($ret) ) echo json_encode($ret);
			else echo JText::_('COM_TWOJTOOLBOX_UPLOAD_UPLOADIMAGES_ERROR');
		return ;
	}
}
