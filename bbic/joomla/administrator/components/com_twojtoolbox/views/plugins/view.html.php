<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('_JEXEC') or die('Restricted access');

class TwoJToolboxViewPlugins extends TwojJView{
	protected $items;
	protected $pagination;

	function display($tpl = null){
		$this->items = $this->get('Items');	
		$this->pagination = $this->get('Pagination');
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		foreach($this->items as $i => $item):
			$item->v_install_p =  $item->v_install ? TwojToolboxHelper::perseVersion( $item->v_install ) : '-';
			$item->v_server_p =  $item->v_server ? TwojToolboxHelper::perseVersion( $item->v_server ) : '-';
			$item->status = 'ok';
			if( $item->install){
				if(((time() - $item->adddate)/86400)<14 ) $item->status = 'installed';
				if( $item->v_install < $item->v_server ) $item->status = 'update'; 
			} else {
				if( ( (time() - $item->adddate)/86400)<14 ) $item->status = 'new';
			}
			if ($item->install) $item->version_list = TwojToolboxHelper::versionList( $item->type, $item->v_active );
		endforeach;
	
		$this->update_complete = JRequest::getInt('update_complete', 0);
		$this->opendialog = JRequest::getString('opendialog', '');
	
		$this->addToolBar();
		$this->setDocument();
		parent::display($tpl);
	}

	protected function addToolBar(){
		$canDo = TwojToolboxHelper::getActions();
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title(JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_PLUGINS'), 'twojtoolbox');
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::addNew('plugins.add', 'COM_TWOJTOOLBOX_PLUGINS_INSTALL');
		}
		if(!$this->update_complete) JToolBarHelper::custom('plugins.export', 'refresh.png', 'refresh.png', 'COM_TWOJTOOLBOX_PLUGINS_CHECKVERSION', false);
		if ($canDo->get('core.admin')){
			JToolBarHelper::deleteList('', 'plugins.delete', 'COM_TWOJTOOLBOX_PLUGINS_UNINSTALL', true);
		}
		if ($canDo->get('core.admin')){
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_twojtoolbox');
		}
		JToolBarHelper::divider();
		JToolBarHelper::custom('plitems.about', 'help', 'help', 'COM_TWOJTOOLBOX_ABOUT', false);
	}

	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle( $document->getTitle().' - '.JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_PLUGINS'));
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var com_2jtoolbox_plugins_button_upload 				= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_BUTTON_UPLOAD', 1)."';
			var com_2jtoolbox_plugins_button_more 					= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_BUTTON_MORE', 1)."';
			var com_twojtoolbox_plugins_plugininfo_title 			= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGININFO_TITLE', 1)."';
			var com_twojtoolbox_plugins_plugininfo_info 			= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGININFO_INFO', 1)."';
			var com_twojtoolbox_plugins_plugininfo_save 			= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGININFO_SAVE', 1)."';
			var com_twojtoolbox_plugins_updateproduct_error			= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_UPDATEPRODUCT_ERROR', 1)."';
			var com_twojtoolbox_plugins_plugindelete_title			= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGINDELETE_TITLE', 1)."';
			var com_twojtoolbox_plugins_plugindelete_needselect		= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_PLUGINDELETE_NEEDSELECT', 1)."';
			var com_twojtoolbox_plugindelete_button_deleteplugin	= '".JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_BUTTON_DELETEPLUGIN', 1)."';
			var com_twojtoolbox_plugindelete_textdelete				= '".JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_TEXTDELETE', 1)."';
			var com_twojtoolbox_plugindelete_textdelete_complete	= '".JText::_('COM_TWOJTOOLBOX_PLUGINDELETE_TEXTDELETE_COMPLETE', 1)."';
			var com_twojtoolbox_plugins_title_uploadnew 			= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_TITLE_UPLOADNEW', 1)."';
			var twojtoolbox_uploaddialog 							= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_UPLOAD_BUTTON', 1)."';
			var twojtoolbox_desc_title 								= '".JText::_('COM_TWOJTOOLBOX_PLUGINS_DESC_TITLE', 1)."';
			var twojtoolbox_opendialog								= '".JText::_($this->opendialog, 1)."';
			var com_twojtoolbox_main_header_about 					=  '".JText::_( 'COM_TWOJTOOLBOX_MAIN_HEADER_ABOUT', 1 )."';
			var after_install	 									= 0;
			var show_about 											= 0;
			var twojtoolbox_news 									= '".JText::_( TwojToolboxHelper::getNews(), 1 )."';
		");
		TwojToolboxHelper::adminAddScript( array('init','ui.core','ui.position','ui.widget','ui.dialog','ui.button','about','plugins','ajaxupload','tip'), 'js');
		TwojToolboxHelper::adminAddScript( array('admin.helper','admin','ui','tip'));
	}
}
