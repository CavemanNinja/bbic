<?php
/**
* @package 2JToolBox
* @Copyright (C) 2011 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.0 $
**/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_twojtoolboxInstallerScript{
	
	function install($parent){
		JFactory::getApplication()->setUserState('com_twojtoolbox.options.after_install', '1');
		$parent->getParent()->setRedirectURL('index.php?option=com_twojtoolbox');
	}

	function uninstall($parent){
		$installer = new JInstaller();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('type="plugin" AND element="twojtoolbox" AND folder="system"');
		$db->setQuery( (string) $query);
		$id_plugin = $db->loadResult();
		if($id_plugin) $installer->uninstall('plugin', $id_plugin);
		$query->clear();
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('type="plugin" AND element="twojtoolboxbutton" AND folder="editors-xtd"');
		$db->setQuery( (string) $query);
		$id_plugin = $db->loadResult();
		if($id_plugin) $installer->uninstall('plugin', $id_plugin);
		$query->clear();
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where('type="module" AND element="mod_twojtoolbox"');
		$db->setQuery( (string) $query);
		$id_module = $db->loadResult();
		if($id_module) $installer->uninstall('module', $id_module);
		echo '<p>' . JText::_('COM_TWOJTOOLBOX_UNINSTALL_TEXT') . '</p>';
	}

	function update($parent){
		JFactory::getApplication()->setUserState('com_twojtoolbox.options.after_install', '1');
		$parent->getParent()->setRedirectURL('index.php?option=com_twojtoolbox');
	}
	
	function preflight($type, $parent){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->delete('#__menu');
		$query->where('menutype="main" AND title="COM_TWOJTOOLBOX_MENU" AND type="component"');
		$db->setQuery( (string) $query);
		$db->query();
		
	}
	
	function postflight($type, $parent){
		$manifest = $parent->get("manifest");
		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->delete('#__twojtoolbox_config');
		$query->where('id=1');
		$db->setQuery( (string) $query);
		if( $db->loadResult() ){
			$db->setQuery('TRUNCATE TABLE `#__twojtoolbox_config`');
			$db->query();
		}
		$version_inner = (int) $manifest->version_inner;
		$db->setQuery('INSERT INTO `#__twojtoolbox_config` (`id`, `update`, `t`, `version`, `version_available`) VALUES (1, 0, 0, '.$version_inner.', '.$version_inner.');');
		$db->query();
		
		$plugin_components_path = dirname ( dirname(__FILE__));
		$plugin_path = $plugin_components_path.'/plugin';
		
		if( JFolder::exists( $plugin_path ) ){
			JLoader::register('TwojToolboxHelper', JPATH_SITE.'/administrator/components/com_twojtoolbox/helpers/twojtoolbox.php');
			if( TwojToolboxHelper::install_plugin( $plugin_path, '', 0 ) ){
				echo '<p>' . JText::_('COM_TWOJTOOLBOX_POSTFLIGHT_TEXT_OK') . '</p>'; 
				return ;
			}
		}
				
		//echo '<p>' . JText::_('COM_TWOJTOOLBOX_POSTFLIGHT_TEXT_ERROR') . '</p>';  
	}
}
