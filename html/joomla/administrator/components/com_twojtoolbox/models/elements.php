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
class TwojToolboxModelElements extends JModelList
{ 
	
	public function __construct($config = array()){
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'a.type',
				'title', 'a.title',
				'state', 'a.state',
				'ordering',
				'language', 'a.language',
				'catid', 'category_title',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'state'
			);
		}
		parent::__construct($config);
	}
	
	function &getCategoryOrders(){
		if (!isset($this->cache['categoryorders'])) {
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$query->select('MAX(ordering) as `max`, catid');
			$query->select('catid');
			$query->from('#__twojtoolbox_elements');
			$query->where(' catid= '.TwojToolboxHelper::cgid( ));
			$query->group('catid');
			$db->setQuery($query);
			$this->cache['categoryorders'] = $db->loadAssocList('catid', 0);
		}
		return $this->cache['categoryorders'];
	}
	
	protected function getListQuery(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		//$query->select('id,title,params');
		
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS id, a.title AS title,'.
				'a.checked_out AS checked_out,'.
				'a.checked_out_time AS checked_out_time, a.catid AS catid,' .
				'a.state AS state, a.ordering AS ordering,'.
				'a.language'
			)
		);
		
		
		$query->from('#__twojtoolbox_elements AS a');
		
		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');
		
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
		
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__twojtoolbox AS c ON c.id = a.catid');
		
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}
		
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.')');
			}
		}
		
		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		$categoryId = TwojToolboxHelper::cgid( ); 
		
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $categoryId);
		}
	
		return $query;
	}
	
	protected function getStoreId($id = ''){
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.language');
		return parent::getStoreId($id);
	}
	
	
	public function getTable($type = 'Element', $prefix = 'TWOJToolBoxTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function populateState($ordering = null, $direction = null){
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_twojtoolbox');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('title', 'asc');
	}
}
