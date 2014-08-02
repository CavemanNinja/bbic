<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class TwojToolboxModelNews extends JModelList{

	protected function getListQuery(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, `date_in`, `message`, `read` ');
		$query->order('date_in DESC' );
		$query->from('#__twojtoolbox_news');
		return $query;
	}
	
	public function readAll() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->SET('`read` = 1 ');
		$query->UPDATE('#__twojtoolbox_news');
		$db->setQuery($query);
		$db->query();
	}
	
}
