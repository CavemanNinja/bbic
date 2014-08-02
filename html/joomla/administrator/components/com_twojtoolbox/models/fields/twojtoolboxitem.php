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


class JFormFieldTwojtoolboxItem extends JFormFieldList{

	protected $type = 'twojtoolboxitem';
	
	protected function getOptions(){
		$type_p = TwojToolboxHelper::plugin_info(TwojToolboxHelper::cgid());
		if($type_p) $type_p = $type_p->type;
			else return false;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from('#__twojtoolbox');
		//$query->orderby('install DESC, ordering DESC, adddate DESC, title');
		$query->where('type = '.$db->quote($type_p));
		$db->setQuery((string)$query);
		$items_list = $db->loadObjectList();
		$options = array();
		if ($items_list) foreach($items_list as $items_row) $options[] = JHtml::_('select.option', $items_row->id, $items_row->title );
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
