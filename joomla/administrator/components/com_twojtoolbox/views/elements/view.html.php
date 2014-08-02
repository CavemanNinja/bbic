<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');

class TwoJToolboxViewElements extends TwojJView{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null){
		$this->categories	= $this->get('CategoryOrders');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		if (count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		$this->addToolBar();
		TwojToolboxHelper::elementMenu('elements');
		parent::display($tpl);
		$this->setDocument();
	}

	protected function addToolBar(){
		$canDo = TwojToolboxHelper::getActions();
		JToolBarHelper::title(JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ELEMENTS'), 'twojtoolbox');
		if ($canDo->get('core.create')){
			JToolBarHelper::addNew('element.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')){
			JToolBarHelper::editList('element.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')){	
			JToolBarHelper::deleteList('', 'elements.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('elements.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('elements.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}
		
		JToolBarHelper::divider();
		 if(TJTB_JVERSION==3)
			JToolBarHelper::custom('plitem.cancel', 'list-view', 'list-view', 'COM_TWOJTOOLBOX_INSTANCESLISTING', false);
		else
			JToolBarHelper::custom('plitem.cancel', 'back.png', 'back_f2.png', 'COM_TWOJTOOLBOX_INSTANCESLISTING', false);
	}

	protected function setDocument(){
		$document = JFactory::getDocument();
		$document->setTitle( $document->getTitle().' - '.JText::_('COM_TWOJTOOLBOX_MAIN_HEADER_ELEMENTS'));
		$document->addScriptDeclaration(
			TwojToolboxHTMLHelper::baseDialogOptions()."
			var com_twojtoolbox_deleteconfirm = '".JText::_( 'COM_TWOJTOOLBOX_DELETECONFIRM', 1 )."';
		");
		TwojToolboxHelper::adminAddScript( array('init','ui.core','ui.widget','ui.button','elements'), 'js');
		TwojToolboxHelper::adminAddScript( array('admin.helper','admin','ui') );
	}
	
	protected function getSortFields(){
		return array(
			'ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.name' => JText::_('COM_BANNERS_HEADING_NAME'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
