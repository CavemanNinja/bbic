<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');

class TwoJToolboxViewPlitems extends TwojJView{
	
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;
	protected $inst_plugins;
	protected $new_plugins;
	protected $need_update;
	
	function display($tpl = null) {
		
		$this->categories	= $this->get('CategoryOrders');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		$this->tbversion = TwojToolboxHelper::getTBVersion();
		
		$this->inst_plugins		= $this->get('InstalPlugins');
		$this->new_plugins	= $this->get('NewPlugins');
		$this->need_update		= $this->get('Update');
		$this->optionSad = (JFactory::getApplication()->getUserState('com_twojtoolbox.options.sad', 0) != -2);

		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this->addToolBar();
		parent::display($tpl);
		$this->setDocument();
	}


	protected function addToolBar(){
		JRequest::setVar('hidemainmenu', false);
		$canDo = TwojToolboxHelper::getActions();
		JToolBarHelper::title(JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ITEMS'), 'twojtoolbox');
		
		if ($canDo->get('core.create')){
			JToolBarHelper::addNew('plitem.new', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')){
			JToolBarHelper::editList('plitem.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')){
			JToolBarHelper::deleteList('', 'plitems.delete', 'JTOOLBAR_DELETE');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('plitems.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('plitems.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::custom('plitems.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}
		
		if ($canDo->get('core.admin')){
			JToolBarHelper::preferences('com_twojtoolbox');
		}
		JToolBarHelper::divider();
		JToolBarHelper::custom('plitems.about', 'help', 'help', 'COM_TWOJTOOLBOX_ABOUT', false);
	}

	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle( $document->getTitle().' - '.JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ITEMS'));
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var twojtoolbox_selecttypetitle 		= '".JText::_( 'COM_TWOJTOOLBOX_SELECTPLUGINSTITLE', 1)."';
			var twojtoolbox_selectdialog 			= '".JText::_( 'COM_TWOJTOOLBOX_SELECTDIALOG', 1)."';
			var com_twojtoolbox_deleteconfirm 		= '".JText::_( 'COM_TWOJTOOLBOX_DELETECONFIRM', 1 )."';
			var com_twojtoolbox_onleOne 			= '".$this->get('OnePlugin')."';
			var need_update 						= ".($this->need_update?'1':'0').";
			var after_install 						= '".JFactory::getApplication()->getUserState('com_twojtoolbox.options.after_install', '0')."';
			var com_twojtoolbox_main_header_about 	=  '".JText::_( 'COM_TWOJTOOLBOX_MAIN_HEADER_ABOUT', 1 )."';
			var com_twojtoolbox_main_header_about_after_install =  '".JText::_( 'COM_TWOJTOOLBOX_MAIN_HEADER_ABOUT_AFTER_INSTALL', 1 )."';
			var show_about 							= ".JRequest::getInt('show_about', 0).";
			var twojtoolbox_news 					= '".JText::_( TwojToolboxHelper::getNews(), 1 )."';
		");
		JFactory::getApplication()->setUserState('com_twojtoolbox.options.after_install', 0);
		TwojToolboxHelper::adminAddScript( array('init','ui.core','ui.widget','ui.button','ui.position','ui.dialog','tip','types','about'), 'js');
		TwojToolboxHelper::adminAddScript( array('admin.helper','admin.color','admin','ui','tip'));
	}
}
