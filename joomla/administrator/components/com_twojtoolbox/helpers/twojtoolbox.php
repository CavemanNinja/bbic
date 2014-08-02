<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

abstract class TwojToolboxHelper{	
	
	public static function install_plugin( $extractdir, $archivename = '', $need_delete = 1, $typeError = 0 ){
		
		jimport('joomla.filesystem.path');
		jimport('joomla.installer.helper');
		
		if( JFolder::exists($extractdir.'/plugin') ) $extractdir .= '/'.'plugin';
		
		$setup_file = JFolder::files($extractdir, 'twojtoolboxsetup(.*).xml', 1); //need check
		if ( count($setup_file)>0 ){
			$setup_file =  $extractdir.'/'.$setup_file[0];
			$setup_dir = dirname($setup_file);
			$xml =JFactory::getXML($setup_file);
			if($xml){
				$need_twojtoolbox_version = (int) (string) $xml->twojtoolbox;
				$install_twojtoolbox_version = TwojToolboxHelper::getTBVersion(1);
				
				if( $need_twojtoolbox_version > $install_twojtoolbox_version ){
					if( $typeError ){
						 Jerror::raiseWarning(null, JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_TOOLBOXVERSION'));
					} else {
						echo  '<h2>'.JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_TOOLBOXVERSION').'</h2>';
					}
					if($need_delete) JFolder::delete($extractdir);
					if($need_delete && $archivename) JFile::delete($archivename);
					return false;
				}
				$version = (string) $xml->version;
				$type = (string) $xml->type;
				$name_plugin = (string) $xml->name;
				$plugin_dir = JPATH_ROOT.'/components/com_twojtoolbox/plugins/'.$type;
				
				if( !JFolder::exists( $plugin_dir.'/'.$type ) ){
					if( !JFolder::create( $plugin_dir  ) ){
						echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_CREATEDIR_ERROR');
						if($need_delete) JFolder::delete($extractdir);
						if($need_delete && $archivename) JFile::delete($archivename);
						return false;
					}
				}
				$version_temp = $version;
				if( JFolder::exists( $plugin_dir.'/'.$version_temp ) ){
					$i=1;
					$version_temp = $version.'('.$i.')';
					while( JFolder::exists( $plugin_dir.'/'.$version_temp ) ){
						$version_temp = $version.'('.++$i.')';
					}
				}
				if( !JFolder::create( $plugin_dir.'/'.$version_temp  ) ){
					echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_CREATEDIR_ERROR');
					if($need_delete) JFolder::delete($extractdir);
					if($need_delete && $archivename) JFile::delete($archivename);
					return false;
				} else $plugin_dir = $plugin_dir.'/'.$version_temp;
			
				if(isset($xml->files) && isset($xml->files->filename) && count($xml->files->filename)){
					foreach ($xml->files->folder as $folder) {
						$folder = JPath::clean( (string) $folder);
						if( !JFolder::create( $plugin_dir.'/'.$folder ) ){
							echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_CREATEDIR_ERROR');
							if($need_delete) JFolder::delete($extractdir);
							if($need_delete && $archivename) JFile::delete($archivename);
							JFolder::delete($plugin_dir);
							return false;
						}
						if( JFolder::exists( $setup_dir.'/'.$folder) ){
							if( !JFolder::copy( $setup_dir.'/'.$folder, $plugin_dir.'/'.$folder, '', true ) ){
								echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_COPYFILES_ERROR');
								if($need_delete) JFolder::delete($extractdir);
								if($need_delete && $archivename) JFile::delete($archivename);
								JFolder::delete($plugin_dir);
								return false;
							}
						} else {
							echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_ARHIVE_ERROR');
							if($need_delete) JFolder::delete($extractdir);
							if($need_delete && $archivename) JFile::delete($archivename);
							JFolder::delete($plugin_dir);
							return false;
						}
					}
					foreach ($xml->files->filename as $filename) {
						$filename = str_replace('\\', '/', $filename);
						$filename = str_replace('/', '/', $filename);
						if( JFile::exists( $setup_dir.'/'.$filename) ){
							if( !JFile::copy( $setup_dir.'/'.$filename, $plugin_dir.'/'.$filename ) ){
								echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_COPYFILES_ERROR');
								if($need_delete) JFolder::delete($extractdir);
								if($need_delete && $archivename) JFile::delete($archivename);
								JFolder::delete($plugin_dir);
								return false;
							}
							
						} else {
							echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_ARHIVE_ERROR');
							if($need_delete) JFolder::delete($extractdir);
							if($need_delete && $archivename) JFile::delete($archivename);
							JFolder::delete($plugin_dir);
							return false;
						}
					}
				}
				JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/tables/');
				$row	= JTable::getInstance('Plugin', 'TwojToolboxTable');
				$row->load( array('type'=>$type ));
				$row->title = (string) $name_plugin;
				$row->type = (string) $type;
				$row->install = 1;
				$row->desc = (string) $xml->description;
				$row->desc_small = (string) $xml->description_small;
				if( ( !((int) $row->v_install) > 0) ) $row->v_install = $version;
				$row->v_install = $row->v_install > $version ? $row->v_install : $version ;
				$row->v_active = $version_temp;
				$row->adddate = time();
				$row->multi =(int) $xml->multi; 	
				$row->multitag =(int) $xml->multitag; 	
				$row->daemon =(int) $xml->daemon; 
				$row->images = (int) $xml->images; 	
				$row->ordering = (int) $xml->ordering;
				
				if( !$row->store() ){
					echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_ERROR_DATABASE');
					if($need_delete) JFolder::delete($extractdir);
					if($need_delete && $archivename) JFile::delete($archivename);
					JFolder::delete($plugin_dir);
					return false;
				}
				if( $row->images && $row->multi ){
					
					$image_dir = JPATH_SITE.'/media/com_twojtoolbox';
					if( !JFolder::exists( $image_dir ) ){
						if( !JFolder::create( $image_dir  ) ){
							echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_CREATEDIR_ERROR');
							return false;
						}
					}
					$image_dir = JPATH_SITE.'/media/com_twojtoolbox/'.$type;
					if( !JFolder::exists( $image_dir ) ){
						if( !JFolder::create( $image_dir  ) ){
							echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_CREATEDIR_ERROR');
							return false;
						}
					}
				}
			} else {
				echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_SETUPFILE_ERROR');
				return false;
			}
					
		} else {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_SETUPFILE_ERROR');
			return false;
		}
		return 1;
	}
	
	public static function enable_plugin( $type, $ret = 0 ){
		$user		= JFactory::getUser();
		if( !$user->authorise('core.admin', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		$db_state = false;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->SET('enabled=1');
		$query->update('#__extensions');
		switch($type){
			case 'plugin':
				$query->where('type="plugin" AND element="twojtoolbox" AND folder="system"');
				break;
			case 'button':
				$query->where('type="plugin" AND element="twojtoolboxbutton" AND folder="editors-xtd"');
				break;
			default: echo 0; return ;
		}
		//echo (string) $query;
		$db->setQuery( (string) $query);
		$ret_val =  $db->query();
		if($ret) return ;
		echo $ret_val;
		return ;
	}
	
	public static function reeinstall($type, $ret = 0){

		$user		= JFactory::getUser();
		if( !$user->authorise('core.admin', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		
		$name_pack = '';
		switch($type){
			case 'module':
				$name_pack  = 'mod_twojtoolbox';
				break;
			case 'plugin':
				$name_pack  = 'plugin_twojtoolbox';
				break;
			case 'button':
				$name_pack  = 'button_twojtoolbox';
				break;
		}
		if($name_pack){
			$name_pack  = JPATH_ADMINISTRATOR.'/components/com_twojtoolbox/install/'.$name_pack;
			if(JFolder::exists($name_pack) ){
				jimport('joomla.installer.installer');
				$installer = new JInstaller();
				$inst_res = $installer->install($name_pack);
				if($inst_res && $type!='module'){
					TwojToolboxHelper::enable_plugin( $type, 1);
				}
				if($ret) return $inst_res;
				else  echo $inst_res;
				return;
			}
		}
		if($ret) return 0;
				else  echo '0';
		return;
	}
	
	public static function about( $after_install = 0, $only_component = 0 ){
		if($after_install){
			if(!$only_component){
				TwojToolboxHelper::reeinstall('module', 1);
				TwojToolboxHelper::reeinstall('plugin', 1);
				TwojToolboxHelper::reeinstall('button', 1);
			}
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select(' `id`, `title`, `type`, `desc`, `v_active`, `desc_small`');
			$query->from('#__twojtoolbox_plugins');
			$query->where('install = 1'.(!is_int($after_install)&&is_string($after_install)?' AND `type`="'.$after_install.'"':''));
			$db->setQuery( (string) $query);
			$plugin_info = $db->loadObject();
		}
		$ret_html = '
			'.(JRequest::getString('option', '')!='com_twojtoolbox'?'<br />':'').'
			'.($after_install && isset($plugin_info) && $plugin_info->id?
				'<div class="twojtoolbox_about_title_plugin">
					<img width="200" src="'.JURI::root().'/components/com_twojtoolbox/plugins/'.$plugin_info->type.'/'.$plugin_info->v_active.'/mid_logo.png" align="left" title="'.JText::_($plugin_info->title).'" />
					'.JText::_($plugin_info->desc_small).'
				</div><br />':
				'<div class="twojtoolbox_about_title">'.JText::_("2JToolBox - it's powerful Joomla! framework with advanced plugins system support").'</div>').'
			
			'.(JRequest::getString('option', '')!='com_twojtoolbox'?'<br /><br />
				<div align="center"><a href="index.php?option=com_twojtoolbox" class="twojbutton">'.JText::_('Open Instances Manager for creation/edit instances').'</a></div>
			':'').'
			<div class="twoj_clear"></div>
			<div class="twojtoolbox_about_header">'.JText::_("Installed elements").'</div>
			<div class="twojtoolbox_about_line1"><strong>'.JText::_("2JToolBox component").'</strong></div>
			<div class="twojtoolbox_about_line2"><div class="twojtoolbox_installed">'.JText::_("Installed").'</div></div>
			<div class="twoj_clear"></div>
			<div class="twojtoolbox_about_line1"><strong>'.JText::_("2JToolBox plugin").'</strong></div>';
		$ret_html .= TwojToolboxHelper::button_for_about('plugin');
		$ret_html .= '<div class="twojtoolbox_about_line1"><strong>'.JText::_("2JToolBox editor plugin").'</strong></div>';
		$ret_html .= TwojToolboxHelper::button_for_about('button');	
		$ret_html .= '<div class="twojtoolbox_about_line1"><strong>'.JText::_("2JToolBox module").'</strong></div>';
		$ret_html .= TwojToolboxHelper::button_for_about('module');	
		$ret_html .= '
			<div class="twojtoolbox_about_header">'.JText::_("Useful Links").'</div>
			<div class="twojtoolbox_about_line1">'.JText::_("Homepage").'</div>
			<div class="twojtoolbox_about_line2"><a href="http://www.2joomla.net" target="_blank" >http://2joomla.net</a></div>
			<div class="twoj_clear"></div>
			<div class="twojtoolbox_about_line1">'.JText::_("Memberplace & Support").'</div>
			<div class="twojtoolbox_about_line2"><a href="http://www.2joomla.net/products" target="_blank" >http://2joomla.net/products</a></div>
			<div class="twoj_clear"></div>
			'.(
				$after_install?
					'<div class="twojtoolbox_about_title">'.JText::_("2JToolBox - it's powerful Joomla! framework with advanced plugins").'</div>':
					'<div class="twojtoolbox_about_header">'.JText::_("Copyright").'</div>'.
					'<div class="twojtoolbox_about_line">'.JText::_("2JToolBox is commercial software released under the GNU License.").'</div>'
			).'<br /><a target="_blank" href="http://www.2joomla.net" class="twojtoolbox_about_powered">'.JText::_("POWERED BY").'</a>
			'.(JRequest::getString('option', '')!='com_twojtoolbox'?'<br /><br />':'').'
			
			
			<div class="twoj_clear"></div>';
		JFactory::getApplication()->setUserState('com_twojtoolbox.options.after_install', 0);
		return $ret_html;
	}

	public static function button_for_about( $typecheck  ) {
		$ret_html = '';
		$check_plugin = TwojToolboxHelper::checkInstall($typecheck);
		$ret_html .= '	
			<div class="twojtoolbox_about_line2">
				<div class="twojtoolbox_'.($check_plugin->result?'':'not').'installed">'.($check_plugin->result?JText::_('Installed'):JText::_('Error')).'</div>
			</div>';
		if(!$check_plugin->result){ 
			if(!$check_plugin->file_state){
				$ret_html .= '
					<div class="twoj_clear"></div>
					<div class="twojtoolbox_about_line3">'.JText::_('Files error ').'</div>
					<div class="twojtoolbox_about_line4"><button id="twojtoolbox_reinstall_'.$typecheck.'" class="button btn">'.JText::_('Reinstall').'</button></div>
				';
			}
			if( $check_plugin->file_state && (!$check_plugin->db_check || !$check_plugin->db_check->id )){
				$ret_html .= '
					<div class="twoj_clear"></div>
					<div class="twojtoolbox_about_line3">'.JText::_('Database error ').'</div>
					<div class="twojtoolbox_about_line4"><button id="twojtoolbox_reinstall_'.$typecheck.'" class="button btn">'.JText::_('Reinstall').'</button></div>
				';
			}
			if( $check_plugin->file_state && (!$check_plugin->db_check || !$check_plugin->db_check->enabled ) && $typecheck!='module' ){
				$ret_html .= '
					<div class="twoj_clear"></div>
					<div class="twojtoolbox_about_line3">'.JText::_('Status disabled ').'</div>
					<div class="twojtoolbox_about_line4"><button id="twojtoolbox_enable_'.$typecheck.'" class="button btn">'.JText::_('Enable').'</button></div>
				'; 
			}
		}
		$ret_html .= '<div class="twoj_clear"></div>';
		return $ret_html;
	}
	
	public static function checkInstall( $typecheck  ) {
		$file_state = false;
		$db_state = false;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		switch($typecheck ){
			case 'plugin':{
				$file_state = (bool) 
					JFolder::exists(JPATH_PLUGINS.'/system/twojtoolbox') && 
					JFile::exists(JPATH_PLUGINS.'/system/twojtoolbox/twojtoolbox.xml') && 
					JFile::exists(JPATH_PLUGINS.'/system/twojtoolbox/twojtoolbox.php');
				$query->select(' extension_id AS id, enabled');
				$query->from('#__extensions');
				$query->where('type="plugin" AND element="twojtoolbox" AND folder="system"');
				break;
			}
			case 'button':{
				$file_state = (bool) 
					JFolder::exists(JPATH_PLUGINS.'/editors-xtd/twojtoolboxbutton') && 
					JFile::exists(JPATH_PLUGINS.'/editors-xtd/twojtoolboxbutton/twojtoolboxbutton.xml') &&
					JFile::exists(JPATH_PLUGINS.'/editors-xtd/twojtoolboxbutton/twojtoolboxbutton.php');
				$query->select(' extension_id AS id, enabled');
				$query->from('#__extensions');
				$query->where('type="plugin" AND element="twojtoolboxbutton" AND folder="editors-xtd"');
				break;
			}
			case 'module':{
				$file_state = (bool) 
					JFolder::exists(JPATH_ROOT.'/modules/mod_twojtoolbox') &&
					JFolder::exists(JPATH_ROOT.'/modules/mod_twojtoolbox/tmpl') &&
					JFile::exists(JPATH_ROOT.'/modules/mod_twojtoolbox/tmpl/default.php') &&
					JFile::exists(JPATH_ROOT.'/modules/mod_twojtoolbox/mod_twojtoolbox.xml') && 
					JFile::exists(JPATH_ROOT.'/modules/mod_twojtoolbox/mod_twojtoolbox.php')
				;
				$query->select(' extension_id AS id, 1 AS enabled');
				$query->from('#__extensions');
				$query->where('type="module" AND element="mod_twojtoolbox"');
				break;
			}
		}
		$db->setQuery( (string) $query);
		$db_check = $db->loadObject();
		$ret_obj = new JObject;
		$ret_obj->file_state = $file_state;
		$ret_obj->db_check = $db_check;
		$ret_obj->result = (bool) ($db_check && $db_check->id && $db_check->enabled && $file_state );
		return $ret_obj;
	}
	
	public static function getTBVersion( $only_cur= 0 ) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('version, version_available');
		$query->from(' #__twojtoolbox_config');
		$query->where('id=1');
		$db->setQuery($query);
		$twojtoolbox_version = $db->loadObject();
		if($only_cur) return $twojtoolbox_version->version;
		return $twojtoolbox_version;
	}
	
	public static function  selectboxType($type =''){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from(' #__twojtoolbox_plugins');
		$query->where('install=1');
		$db->setQuery($query);
		$plugins = $db->loadObjectList();
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('COM_TWOJTOOLBOX_SELLECTPLUGINTYPE') );
		if ($plugins){
			foreach($plugins as $plugin){
				$options[] = JHtml::_('select.option', $plugin->type, $plugin->title );
			}
			return JHtml::_('select.genericlist', $options, 'filter_category_type', 'onchange="this.form.submit()"', 'value', 'text', $type);
		}
	}
	
	public static function plugin_info($cid , $type_in = 0) {
		if(!$type_in) $cid = (int) $cid;
		if(!$cid){
			return false;
		}
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('plu.*');
		if($type_in){
			$query->where('plu.`type` ='.$db->quote($cid) );
			$query->from(' #__twojtoolbox_plugins AS plu');
		} else {
			$query->where('ite.id = '.(int) $cid.' AND plu.`type` = ite.`type`');
			$query->from('#__twojtoolbox AS ite, #__twojtoolbox_plugins AS plu');
		}
		$db->setQuery($query);
		$plugin_info_data = $db->loadObject();
		if($plugin_info_data){
			if( !isset($plugin_info_data->cacheid)) $plugin_info_data->cacheid = 0;
			return $plugin_info_data;
		} else return false;
	}

	public static function format_bytes($bytes) {
	if ($bytes < 1024) return $bytes.' B';
		elseif ($bytes < 1048576) return round($bytes / 1024, 2).' KB';
			elseif ($bytes < 1073741824) return round($bytes / 1048576, 2).' MB';
				elseif ($bytes < 1099511627776) return round($bytes / 1073741824, 2).' GB';
					else return round($bytes / 1099511627776, 2).' TB';
	}
	
	public static function pathCheck($path=''){
		$path = str_replace('\\', '/', $path );
		$path = str_replace('//', '/', $path );
		$path = trim( $path, '/');
		return $path;
	}
	
	public static function path_twojcode($path='', $revers = 0){
		if( $revers ) {
			$path = str_replace('_DS_', '/' ,$path);
			$path = str_replace('_TWOJ_PRB_', ' ',$path);
		} else {
			$path = str_replace('/','_DS_',$path);
			$path = str_replace(' ','_TWOJ_PRB_',$path);	
		}
		return $path;
	}
	
	public static function cgid( $setID = 0 ){
		$catid = JFactory::getApplication()->getUserStateFromRequest('com_twojtoolbox.edit.element.catid', 'catid', $setID);
		if(!$catid && !$setID){
			$app = JFactory::getApplication();
			$app->redirect('index.php?option=com_twojtoolbox', JText::_( 'COM_TWOJTOOLBOX_PLEASESELECITEMS' ));
		}
		return $setID ? '' : $catid;
	}
	
	
	public static function elementMenu($cur, $id=0){
		if(!$id ) $id = TwojToolboxHelper::cgid();
		$document = JFactory::getDocument();
		
		$plobj = TwojToolboxHelper::plugin_info($id);
		$need_type = $plobj->type;
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->where('type = '.$db->quote($need_type) );
		$query->from('#__twojtoolbox');
		$query->order('title');
		$db->setQuery($query);
		$items_list = $db->loadObjectList();
		$options = array();
		if ($items_list) {
			foreach($items_list as $item) {
				$options[] = JHtml::_('select.option', $item->id, $item->title );
			}
		}
		$select_catid = JHtml::_('select.genericlist', $options, 'twojtoolbox_selectitems', '', 'value', 'text', $id, 'twojtoolbox_selectitems'	);
		
		$document->addScriptDeclaration('
			emsajax(document).ready(function(){
				'.( TJTB_JVERSION == 2 ?'emsajax("#element-box").before(emsajax( "#twojtoolbox_elementmenu"));':'').'
				emsajax( "#twojtoolbox_elementmenu_inner" ).buttonset();
				emsajax( "#twojtoolbox_elementmenu_inner button" ).click( function(){
					var idtext =  emsajax(this).attr("id");
					idtext = idtext.replace("twojtoolbox_elementmenu_","");
					if(idtext=="options") window.location.href = "'.JURI::root().'administrator/index.php?option=com_twojtoolbox&task=plitem.edit&id='.$id.'";
							else window.location.href = "'.JURI::root().'administrator/index.php?option=com_twojtoolbox&view="+idtext+"&catid='.$id.'";
				});
				emsajax( "button#twojtoolbox_elementmenu_'.$cur.'").button( "disable" );
				emsajax( "#twojtoolbox_selectitems").change( function(){
					var id_sel = emsajax(this).val();
					'.($cur=='options' ? 'window.location.href = "'.JURI::root().'administrator/index.php?option=com_twojtoolbox&task=plitem.edit&id="+id_sel;':
					'window.location.href = "'.JURI::root().'administrator/index.php?option=com_twojtoolbox&view='.$cur.'&catid="+id_sel;').
					'
				});
			});
		');	?>
		<div id="twojtoolbox_elementmenu">
			<div id="twojtoolbox_elementmenu_selectcatid" <?php if(!$plobj->multi){?>style="padding-right: 25px;"<?php } ?>>
					<label for="twojtoolbox_selectitems"><?php echo JText::_( 'COM_TWOJTOOLBOX_SELECITEMS_LABEL' );?></label>
					<?php echo $select_catid; ?>
			</div>
			<div id="twojtoolbox_elementmenu_inner">
				<?php if($plobj->multi){ ?>
					<button id="twojtoolbox_elementmenu_options" name="twojtoolbox_elementmenu_options"/>		<?php echo JText::_('COM_TWOJTOOLBOX_ELEMENTMENU_OPTIONS');  ?></button>
					<button id="twojtoolbox_elementmenu_elements"  name="twojtoolbox_elementmenu_elements" />		<?php echo JText::_('COM_TWOJTOOLBOX_ELEMENTMENU_ELEMENTS');  ?></button>
						<?php if($plobj->images){ ?>
						<button id="twojtoolbox_elementmenu_batchupload"  name="twojtoolbox_elementmenu_batchupload" />	<?php echo JText::_('COM_TWOJTOOLBOX_ELEMENTMENU_BATCHUPLOAD');  ?></button>
						
						<button id="twojtoolbox_elementmenu_upload"  name="twojtoolbox_elementmenu_upload" />	<?php echo JText::_('COM_TWOJTOOLBOX_ELEMENTMENU_UPLOAD');  ?></button>
						<button id="twojtoolbox_elementmenu_scan"  name="twojtoolbox_elementmenu_scan" />		<?php echo JText::_('COM_TWOJTOOLBOX_ELEMENTMENU_SCAN');  ?></button>
						<?php } ?>
				<?php } ?>
			</div>
			<?php if(TJTB_JVERSION=='2' && !$plobj->multi) echo "<br />"; ?>
		</div>
		<div class="twoj_clear"></div>
		<?php 
	}
	
	public static function addSubmenu($submenu, $twojnews = 0){
		JSubMenuHelper::addEntry(JText::_('COM_TWOJTOOLBOX_SUBMENU_ITEMS'), 'index.php?option=com_twojtoolbox', $submenu == 'plitems');
		JSubMenuHelper::addEntry(JText::_('COM_TWOJTOOLBOX_SUBMENU_PLUGINS'), 'index.php?option=com_twojtoolbox&view=plugins', $submenu == 'plugins');
		if(!$twojnews) JSubMenuHelper::addEntry(JText::_('COM_TWOJTOOLBOX_SUBMENU_NEWS'), 'index.php?option=com_twojtoolbox&view=news', $submenu == 'news');
	}
	
	public static function  perseVersion($version){
		preg_match ( '/([0-9]{1})([0-9]{1})([0-9]{2})(.*)/', $version , $matches );
		if( count($matches) == 5 ){
			return  $matches[1].'.'.$matches[2].'.'.(int)$matches[3].$matches[4];
		} else return $version;
	}
	
	public static function  versionList($itemType, $cyrVers){
		$plugin_dir = JPATH_SITE.'/components/com_twojtoolbox/plugins/'.$itemType;
		if( !JFolder::exists($plugin_dir) ) return JText::_('COM_TWOJTOOLBOX_ERROR_SELECTVERSION');
		$version_list = JFolder::folders($plugin_dir);
		$options = array();
		if ($version_list){
			foreach($version_list as $version_item){
				preg_match ( '/([0-9]*)\(([0-9]*)\)/', $version_item , $matches );
				if( count($matches) == 3 ){
					$version_item_clear = TwojToolboxHelper::perseVersion( $matches[1] ).' '.JText::_('COM_TWOJTOOLBOX_SELECTVERSION_COPYTEXT').' '.$matches[2];
				} else $version_item_clear =  TwojToolboxHelper::perseVersion( $version_item ) ;
				
				$options[] = JHtml::_('select.option', $version_item, $version_item_clear );
			}
		} else return JText::_('COM_TWOJTOOLBOX_ERROR_SELECTVERSION');
		return JHtml::_('select.genericlist', $options, 'twojtoolbox_selectversion_'.$itemType, 'id="twojtoolbox_selectversion_'.$itemType.'" size="1" class="twojtoolbox_selectversion"', 'value', 'text', $cyrVers);
	}
	
	public static function getActions(){
		$user	= JFactory::getUser();
		$result	= new JObject;
		$assetName = 'com_twojtoolbox';
		$actions = array( 'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete' );
		foreach ($actions as $action)  $result->set($action,	$user->authorise($action, $assetName));
		return $result;
	}
	
	public static function getNews(){
		if( JFactory::getApplication()->getUserState('com_twojtoolbox.options.twojnews', 0) || JFactory::getApplication()->getUserState('com_twojtoolbox.options.sad', 0)==-2 ) return '';
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('`message`');
		$query->where('`read` = 0');
		$query->from('#__twojtoolbox_news');
		$query->order('`date_in`');
		$db->setQuery($query);
		return   $db->loadResult();
	}
	
	public static function adminAddScript( $needScript, $typeScript = 'css'){
		$document = JFactory::getDocument();
		
		$addScript = JRequest::getVar('twoj_add_'.$typeScript.'_field', array(), '', 'array');
		if( count($addScript) ) $addScript = '2jbrs2'.implode('2jbrs2', $addScript); else $addScript = '';
		
		if( $typeScript=='css' ){
			if(TJTB_JVERSION==2) $needScript[] = '2jbrs2admin.v2';
					else $needScript[] = '2jbrs2admin.v3';
		}
		if( count($needScript) ) $needScript = '2jbrs2'.implode('2jbrs2', $needScript); else $needScript = '';
		
		if( $stringScript = $needScript.$addScript ){
			$urlScript = TwojToolBoxSiteHelper::scriptCompile( $stringScript, $typeScript );
			$twojcache = JFactory::getApplication()->getUserState('com_twojtoolbox.options.twojcache', 0);
			if( !$urlScript || !$twojcache ) $urlScript = JURI::root(true)."/index.php?option=com_twojtoolbox&format=raw&task=ajax.get".$typeScript."&need=".$stringScript."&name=2j.script.".$typeScript;
			if( $typeScript == 'css' ){
				$document->addStyleSheet($urlScript);
			} else {
				$document->addScript($urlScript);
			}
		}
	}
	
}
