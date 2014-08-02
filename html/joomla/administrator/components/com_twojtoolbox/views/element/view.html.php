<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('_JEXEC') or die('Restricted access');

class TwoJToolboxViewElement extends TwojJView{

	protected $form;
	protected $item;
	protected $itemC;

	public function display($tpl = null){
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->itemC =  $this->get('ItemC');
		
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		$this->addToolBar();
		parent::display($tpl);
		$this->setDocument();
	}


	protected function addToolBar(){
		JRequest::setVar('hidemainmenu', true);
		$isNew = $this->item->id == 0;
		$canDo = TwojToolboxHelper::getActions();
		$title = $isNew ? JText::_('COM_TWOJTOOLBOX_ELEMENT_TITLE_NEW') : JText::_('COM_TWOJTOOLBOX_ELEMENT_TITLE_EDIT');
		$title .= ' "'.$this->itemC->title.'"';
		JToolBarHelper::title( $title, 'twojtoolbox');
		if ($isNew){
			if ($canDo->get('core.create')){
				JToolBarHelper::apply('element.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('element.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('element.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('element.cancel', 'JTOOLBAR_CANCEL');
		} else {
			if ($canDo->get('core.edit')){
				JToolBarHelper::apply('element.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('element.save', 'JTOOLBAR_SAVE');

				if ($canDo->get('core.create')){
					JToolBarHelper::custom('element.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')){
				JToolBarHelper::custom('element.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::divider();
			JToolBarHelper::cancel('element.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	
	protected function setDocument(){
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle( $document->getTitle().' - '.($isNew ? JText::_('COM_TWOJTOOLBOX_ELEMENT_TITLE_NEW') : JText::_('COM_TWOJTOOLBOX_ELEMENT_TITLE_EDIT') ) . ' '.$this->itemC->title);
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var com_twojtoolbox_filelist_title 					= '".JText::_('COM_TWOJTOOLBOX_FILELIST_TITLE', 1) ."';
			var com_twojtoolbox_closedialog 					= '".JText::_('COM_TWOJTOOLBOX_CLOSEDIALOG', 1) ."';
			var com_twojtoolbox_filelist_opendialog 			= '".JText::_('COM_TWOJTOOLBOX_FILELIST_OPENDIALOG', 1) ."';
			var com_twojtoolbox_filelist_uploadbutton 			= '".JText::_('COM_TWOJTOOLBOX_FILELIST_UPLOADBUTTON', 1) ."';
			var com_twojtoolbox_filelist_selectbutton 			= '".JText::_('COM_TWOJTOOLBOX_FILELIST_SELECTBUTTON', 1) ."';
			var com_twojtoolbox_element_field_image_buton_label = '".JText::_('COM_TWOJTOOLBOX_ELEMENT_FIELD_IMAGE_BUTON_LABEL', 1) ."';
			var com_twojtoolbox_filelist_newfolder_text 		= '".JText::_('COM_TWOJTOOLBOX_FILELIST_NEWFOLDER_TEXT', 1) ."';
			var com_twojtoolbox_filelist_newfolder_errortext 	= '".JText::_('COM_TWOJTOOLBOX_FILELIST_NEWFOLDER_ERRORTEXT', 1) ."';
		");
		TwojToolboxHelper::adminAddScript( array('init', 'ui.core', 'ui.position', 'ui.widget', 'ui.dialog', 'ui.button', 'ajaxupload', 'scroll', 'element', 'color', 'fields'), 'js');
		TwojToolboxHelper::adminAddScript( array('admin.helper', 'filetree', 'admin', 'ui'));
	}
}
