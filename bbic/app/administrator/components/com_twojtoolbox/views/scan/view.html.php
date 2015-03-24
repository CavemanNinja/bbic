<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.11 $
**/

defined('_JEXEC') or die('Restricted access');

class TwoJToolboxViewScan extends TwojJView{

	protected $form;
	protected $path;
	
	
	function display($tpl = null){
		$this->path  = JFactory::getApplication()->getUserState('com_twojtoolbox.edit.element.scan.path', 'images');;
		
		$this->form = $this->get('Form');
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		$this->addToolBar();
		TwojToolboxHelper::elementMenu('scan');
		parent::display($tpl);
		$this->setDocument();
	}

	protected function addToolBar(){
		$canDo = TwojToolboxHelper::getActions();
		JToolBarHelper::title(JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_SCAN'), 'twojtoolbox');
		if ($canDo->get('core.create')){
			JToolBarHelper::apply('scan.send', 'JTOOLBAR_APPLY');
		}
		
		if(TJTB_JVERSION==3){
			JToolBarHelper::custom('scan.general_options', 'checkbox-partial', 'checkbox-partial', 'COM_TWOJTOOLBOX_OPTIONDIALOG_TITLE', false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('plitem.cancel', 'list-view', 'list-view', 'COM_TWOJTOOLBOX_INSTANCESLISTING', false);
		}else{
			JToolBarHelper::custom('scan.general_options', 'stats.png', 'stats_f2.png', 'COM_TWOJTOOLBOX_OPTIONDIALOG_TITLE', false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('plitem.cancel', 'back.png', 'back_f2.png', 'COM_TWOJTOOLBOX_INSTANCESLISTING', false);
		}
	}

	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle( $document->getTitle().' - '.JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_SCAN'));
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var com_twojtoolbox_optiondialog_title = '".JText::_('COM_TWOJTOOLBOX_OPTIONDIALOG_TITLE', 1)."';
			var com_twojtoolbox_scan_saveimages_title = '".JText::_('COM_TWOJTOOLBOX_SCAN_SAVEIMAGES_TITLE', 1)."';
			var com_twojtoolbox_scan_folderup = '".JText::_('COM_TWOJTOOLBOX_SCAN_FOLDERUP', 1)."';
			var com_twojtoolbox_scan_cancel = '".JText::_('COM_TWOJTOOLBOX_SCAN_CANCEL', 1)."';
			var com_twojtoolbox_closedialog = '".JText::_('COM_TWOJTOOLBOX_CLOSEDIALOG', 1)."';
			var please_make_a_selection_from_the_list = '".JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST', 1)."';
		");
		TwojToolboxHelper::adminAddScript( array('init','ui.core','ui.position','ui.widget','ui.dialog','ui.button','scan','progress','preload'), 'js');
		TwojToolboxHelper::adminAddScript( array('admin.helper','admin','ui','admin.scan','progress'));
	}
}
