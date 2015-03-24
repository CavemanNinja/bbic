<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldTwojtoolboxelement extends JFormFieldList{

	protected $type = 'twojtoolboxelement';

	protected function getOptions()	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,title');
		$query->from('#__twojtoolbox');
		$query->where('state=1');
		$query->order('title');
		$db->setQuery((string)$query);
		$messages = $db->loadObjectList();
		$options = array();
		if ($messages){
			foreach($messages as $message){
				$options[] = JHtml::_('select.option', $message->id, $message->title );
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TWOJTOOLBOX_SELECT')));
		return $options;
	}
	
	
}
