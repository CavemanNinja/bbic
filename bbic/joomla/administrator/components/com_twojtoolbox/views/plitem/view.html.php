<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('_JEXEC') or die('Restricted access');

class TwojToolboxViewPlitem extends TwojJView{

	protected $form;
	protected $item;
	protected $demos;

	public function display($tpl = null){
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->demos = $this->get('Demos');
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this->addToolBar();
		if(  $this->item->id ) TwojToolboxHelper::elementMenu('options', $this->item->id );
		parent::display($tpl);
		$this->setDocument();  //This correct position (need for demo field)
	}

	protected function addToolBar(){
		JRequest::setVar('hidemainmenu', true);
		$isNew = $this->item->id == 0;
		$canDo = TwojToolboxHelper::getActions();
		JToolBarHelper::title($isNew ? JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ITEMSOPTIONS_NEW') : JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ITEMSOPTIONS'), 'twojtoolbox');
		
		if ($isNew){
			if ($canDo->get('core.create')){
				JToolBarHelper::apply('plitem.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('plitem.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('plitem.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::divider();
			JToolBarHelper::cancel('plitem.cancel', 'JTOOLBAR_CANCEL');
		} else {
			if ($canDo->get('core.edit')){
				JToolBarHelper::apply('plitem.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('plitem.save', 'JTOOLBAR_SAVE');
				if ($canDo->get('core.create'))	{
					JToolBarHelper::custom('plitem.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create'))	{
				JToolBarHelper::custom('plitem.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			if ($canDo->get('core.edit')){
				JToolBarHelper::divider();
				JToolBarHelper::custom('plitem.refresh', 'refresh', 'refresh', 'COM_TWOJTOOLBOX_DEFAULT', false);
			}
			if( $isNew==false && $this->demos && count($this->demos) ){
				if(TJTB_JVERSION==3)
					JToolBarHelper::custom('plitem.demo', 'grid-view', 'grid-view', 'COM_TWOJTOOLBOX_ITEM_IMPORTDEMO', false);
				else
					JToolBarHelper::custom('plitem.demo', 'twojtoolbox_demo', 'twojtoolbox_demo', 'COM_TWOJTOOLBOX_ITEM_IMPORTDEMO', false);
				
			}

			 //icon-eye
			JToolBarHelper::divider();
			JToolBarHelper::cancel('plitem.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	protected function setDocument(){
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ITEMSOPTIONS_NEW') : JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ITEMSOPTIONS'));

		$demo_button = (int) ( $isNew==false && $this->demos && count($this->demos) );
		
		$openTab = 0;
		if( !$isNew ) $openTab = (int) JFactory::getApplication()->getUserState('com_twojtoolbox.edit.plitem.openTab', 0);
		JFactory::getApplication()->setUserState('com_twojtoolbox.edit.plitem.openTab', $openTab);
		
		$document->addStyleDeclaration('.twoj_controls label{display: inline-block !important;}');
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var com_twojtoolbox_closedialog 				= '".JText::_( 'COM_TWOJTOOLBOX_CLOSEDIALOG', 1)."';
			var com_twojtoolbox_refreshconfirm 				= '".JText::_('COM_TWOJTOOLBOX_REFRESHCONFIRM', 1)."';
			var com_twojtoolbox_apply 						= '".JText::_('COM_TWOJTOOLBOX_APPLY', 1)."';
			var com_twojtoolbox_save 						= '".JText::_('COM_TWOJTOOLBOX_SAVE', 1)."';
			var com_twojtoolbox_item_demo_button_enable 	= ".$demo_button.";
			var com_twojtoolbox_item_democonfirm 			= '".JText::_('COM_TWOJTOOLBOX_ITEM_DEMOCONFIRM', 1)."';
			var com_twojtoolbox_item_importdemodialog_title = '".JText::_('COM_TWOJTOOLBOX_ITEM_IMPORTDEMODIALOG_TITLE', 1)."';
			var com_twojtoolbox_item_importdemo 			= '".JText::_('COM_TWOJTOOLBOX_ITEM_IMPORTDEMO', 1)."';
			var com_twojtoolbox_item_demo_set 				= '".JText::_('COM_TWOJTOOLBOX_ITEM_DEMO_SET', 1)."';
			var com_twojtoolbox_item_demo_view 				= '".JText::_('COM_TWOJTOOLBOX_ITEM_DEMO_VIEW', 1)."';
			var com_twojtoolbox_item_plugin_type			= '".$this->get('Type')."';
			var com_twojtoolbox_item_openTab				= ".$openTab.";
		");
		TwojToolboxHelper::adminAddScript( array('init','color','ui.core','ui.position','ui.widget','ui.tabs','ui.dialog','ui.button','iconselect','csseditor','json','item','fields'), 'js');  //need item;fields - if other  not work hide tabs
		TwojToolboxHelper::adminAddScript( array('admin.helper','admin.color','admin.mv', 'admin.mb','admin','ui', 'fa'));
	}
}
