<?php
/**
* @package 2JToolBox 2JGallery
* @Copyright (C) 2012 2Joomla.net
* @ All rights reserved
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class com_2jgalleryInstallerScript{

	public $name_component = '2jgallery';
	
	function install($parent){}

	function uninstall($parent){}

	function update($parent){}
	
	function preflight($type, $parent){
		$component_path = dirname(__FILE__);
		$framework_path = $component_path.'/framework';
		$plugin_path = $component_path.'/plugin';
		
		$framework_helper = JPATH_SITE.'/administrator/components/com_twojtoolbox/helpers/twojtoolbox.php';
		
		$install_flag = false;
		if(JFolder::exists($framework_path) ){
			jimport('joomla.installer.installer');
			$installer = new JInstaller();
			$install_flag = $installer->install($framework_path);
		}elseif( JFolder::exists($plugin_path) && JFile::exists($framework_helper) ){
			JLoader::register('TwojToolboxHelper', $framework_helper);
			if( class_exists('TwojToolboxHelper') ){
				if(TwojToolboxHelper::getTBVersion(1) >= 1007){
					$install_flag = TwojToolboxHelper::install_plugin( $plugin_path, '', 0, 1 );
				} else 
					Jerror::raiseWarning(null, JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_TOOLBOXVERSION'));
			}
		}

		if($install_flag){
			$parent->getParent()->setRedirectURL('index.php?option=com_'.$this->name_component);
			return ;
		}
		Jerror::raiseWarning(null, JText::_("Installation error. Please <a href='http://www.2joomla.net' target='_blank'>contact support</a> or try to re-download extension. You can download it from <a href='http://www.2joomla.net/products' target='_blank'>2JoomlaNet Memberplace Section</a> If you have any questions contact <a href='http://www.2joomla.net' target='_blank'>2JoomlaNet Team</a>"));
		return false;
	}
	
	function postflight($type, $parent){}
}
