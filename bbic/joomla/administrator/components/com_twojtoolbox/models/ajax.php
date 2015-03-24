<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class TwojToolboxModelAjax extends TwojJModel{
	
	public function getSaveSelectResult(){
		$type_select = JRequest::getString('type_select', '');
		$version_select = JRequest::getString('version_select', '');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('type,title');
		$query->from('#__twojtoolbox_plugins');
		$query->order('title');
		$query->where('install = 1 ');
		$db->setQuery((string)$query);
		$elements = $db->loadObjectList();
		$options = array();
		if ($elements){
			foreach($elements as $element){
				$options[] = JHtml::_('select.option', $element->type, $element->title );
			}
		} else return JText::_('COM_TWOJTOOLBOX_ERROR_SELECTTYPE');
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TWOJTOOLBOX_SELECT')));
		return JHtml::_('select.genericlist', $options, 'twojtoolboxbutton_type', 'id="twojtoolboxbutton_type" size="1"', 'value', 'text');
	}
	
	
	public function getItems(){

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('type,title');
		$query->from('#__twojtoolbox_plugins');
		$query->order('title');
		$query->where('install = 1 ');
		$db->setQuery((string)$query);
		$elements = $db->loadObjectList();
		$options = array();
		if ($elements){
			foreach($elements as $element){
				$options[] = JHtml::_('select.option', $element->type, $element->title );
			}
		} else return JText::_('COM_TWOJTOOLBOX_ERROR_SELECTTYPE');
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TWOJTOOLBOX_SELECT')));
		return JHtml::_('select.genericlist', $options, 'twojtoolboxbutton_type', 'id="twojtoolboxbutton_type" size="1"', 'value', 'text');
	}
	
	
	
	
	public function getElements(){
		$type_select = JRequest::getString('type_select', '');
		$element_select = JRequest::getInt('element_select', 0);
		$name_syff = JRequest::getString('name_syff', 'request');
		if( !$type_select ) return JText::_('COM_TWOJTOOLBOX_ERROR_SELECTTYPE');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,title');
		$query->from('#__twojtoolbox');
		$query->where('state=1');
		$query->order('title');
		$query->where('type = '. $db->Quote($type_select));
		$db->setQuery((string)$query);
		$elements = $db->loadObjectList();
		$options = array();
		if ($elements){
			foreach($elements as $element){
				$options[] = JHtml::_('select.option', $element->id, $element->title );
			}
		} else return JText::_('COM_TWOJTOOLBOX_ERROR_SELECTTYPE');
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TWOJTOOLBOX_SELECT')));
		return '00allokmess00'.JHtml::_('select.genericlist', $options, 'jform['.$name_syff.'][id]', 'size="1"', 'value', 'text', $element_select);
	}
	
}
