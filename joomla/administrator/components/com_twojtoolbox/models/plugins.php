<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class TwojToolboxModelPlugins extends JModelList{

	protected function getListQuery(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, title, install, `desc`, `desc_small`, price, type, v_install, v_server, v_active, adddate ');
		$query->order('install DESC, ordering DESC, adddate DESC, title');
		$query->from('#__twojtoolbox_plugins');
		return $query;
	}
	
	
	
	
	public function getInstall( $plugin_dir = '' ) {
		$user		= JFactory::getUser();
		if( !$user->authorise('core.create', 'com_twojtoolbox') ){
			echo  JText::_('JERROR_ALERTNOAUTHOR');
			return false;
		};
		
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.path');
		jimport('joomla.installer.helper');
		
		$userfile = JRequest::getVar('plugin_file', null, 'files', 'array');

		if (!(bool) ini_get('file_uploads') ) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLFILE');
			return false;
		}

		if (!extension_loaded('zlib')) {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLZLIB');
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
		
		$config		= JFactory::getConfig();
		$archivename	= $config->get('tmp_path').'/'.$userfile['name'];
		$tmp_src	= $userfile['tmp_name'];
		
		if( !JFile::upload($tmp_src, $archivename) ){
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_WARNINSTALLUPLOADERROR');
			return false;
		}
		
		$tmpdir = uniqid('install_');
		$extractdir = JPath::clean(dirname($archivename).'/'.$tmpdir);
		$archivename = JPath::clean($archivename);

		if( !JArchive::extract($archivename, $extractdir) ){
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_ARHIVE_ERROR');
			JFile::delete($archivename);
			return false;
		}
		
		if( TwojToolboxHelper::install_plugin( $extractdir , $archivename ) ){
			JFolder::delete($extractdir);
			JFile::delete($archivename);
			return 1;
		} else {
			echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_ERROR_DATABASE');
			return false;
		}
	}
	
}
