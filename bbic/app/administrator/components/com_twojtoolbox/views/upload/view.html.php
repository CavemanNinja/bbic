<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('_JEXEC') or die('Restricted access');

class TwojToolboxViewUpload extends TwojJView{
	
	protected $form;
	
	public function display($tpl = null){
		$this->form = $this->get('Form');
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this->addToolBar();
		TwojToolboxHelper::elementMenu('upload');
		parent::display($tpl);
		$this->setDocument();
	}

	protected function addToolBar(){
		$canDo = TwojToolboxHelper::getActions();
		JToolBarHelper::title( JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_UPLOAD') , 'twojtoolbox');
		if ($canDo->get('core.create')){
			JToolBarHelper::apply('upload.send', 'JTOOLBAR_UPLOAD');
		}
		
		
		if(TJTB_JVERSION==3){
			JToolBarHelper::custom('upload.general_options', 'checkbox-partial', 'checkbox-partial', 'COM_TWOJTOOLBOX_OPTIONDIALOG_TITLE', false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('plitem.cancel', 'list-view', 'list-view', 'COM_TWOJTOOLBOX_INSTANCESLISTING', false);
		}else{
			JToolBarHelper::custom('upload.general_options', 'stats.png', 'stats_f2.png', 'COM_TWOJTOOLBOX_OPTIONDIALOG_TITLE', false);
			JToolBarHelper::divider();
			JToolBarHelper::custom('plitem.cancel', 'back.png', 'back_f2.png', 'COM_TWOJTOOLBOX_INSTANCESLISTING', false);
		}
	}
	
	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle( $document->getTitle().' - '.JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_UPLOAD'));
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var com_twojtoolbox_optiondialog_title = '".JText::_('COM_TWOJTOOLBOX_OPTIONDIALOG_TITLE', 1)."'
			var com_twojtoolbox_upload_needselect = '".JText::_('COM_TWOJTOOLBOX_UPLOAD_NEEDSELECT', 1)."'
		");
		TwojToolboxHelper::adminAddScript( array('init','ui.core','ui.position','ui.widget','ui.dialog','ui.button','upload'), 'js');
		TwojToolboxHelper::adminAddScript( array('admin.helper','admin','ui'));
	}
}
