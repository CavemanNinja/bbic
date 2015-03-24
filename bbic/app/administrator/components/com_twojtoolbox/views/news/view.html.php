<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class TwoJToolboxViewNews extends TwojJView{
	protected $items;
	protected $pagination;
	
	function display($tpl = null){
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this->addToolBar();
		$this->setDocument();
		parent::display($tpl);
	}

	protected function addToolBar(){
		$canDo = TwojToolboxHelper::getActions();
		JToolBarHelper::title(JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_NEWSUPDATES'), 'twojtoolbox');
		JToolBarHelper::custom('news.checkin', 'checkin.png', 'checkin_f2.png', 'COM_TWOJTOOLBOX_NEWS_READALL', false);
		if ($canDo->get('core.admin')){
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_twojtoolbox');
		}
		JToolBarHelper::divider();
		JToolBarHelper::custom('plitems.about', 'help', 'help', 'COM_TWOJTOOLBOX_ABOUT', false);
	}

	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle( $document->getTitle().' - '.JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_NEWSUPDATES'));
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var com_twojtoolbox_main_header_about 	=  '".JText::_( 'COM_TWOJTOOLBOX_MAIN_HEADER_ABOUT', 1 )."';
			var show_about 							= 0;
			var after_install 						= 0;
			var twojtoolbox_news 					= '';
		");
		TwojToolboxHelper::adminAddScript( array('init','ui.core','ui.widget','ui.button','ui.position','ui.dialog','tip','news','about'), 'js');
		TwojToolboxHelper::adminAddScript(array('admin.helper','admin.color','admin','ui','tip'));
	}
}
