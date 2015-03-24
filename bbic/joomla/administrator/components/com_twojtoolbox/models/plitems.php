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

class TwojToolboxModelPlitems extends JModelList{
	
	public function __construct($config = array()){
		if (empty($config['filter_fields'])){
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'type', 'a.type', 'category_type',
				'state', 'a.state'
			);
		}
		parent::__construct($config);
	}
	
	
	
	
	protected function populateState($ordering = null, $direction = null){
		$app = JFactory::getApplication();

		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$categoryType = $this->getUserStateFromRequest($this->context.'.filter.category_type', 'filter_category_type');
		$this->setState('filter.category_type', $categoryType);

		parent::populateState('title', 'asc');
	}
	
	
	protected function getStoreId($id = ''){
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.category_type');
		return parent::getStoreId($id);
	}
	
	protected function getListQuery(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title as title, '.
				'a.type,'.
				'plu.title AS category_type,'.
				'plu.multi,'.
				'plu.images,'.
				'a.checked_out AS checked_out,'.
				'a.checked_out_time AS checked_out_time,'.
				'a.state AS state'
			)
		);
				
		$query->from('#__twojtoolbox AS a');
		
		$query->innerJoin('#__twojtoolbox_plugins AS plu USING (type) ');// ON plu.type = ite.type
		
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
		
		$query->where('plu.install = 1');
		
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}
		
		$category_type = trim($this->getState('filter.category_type'));
		if ($category_type!='') {
			$query->where('a.type = '.$db->Quote( $category_type ));
		}
		
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.title LIKE '.$search.')');
			}
		}
		
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn	= $this->state->get('list.direction', 'ASC');
		
		if ($orderCol == 'category_type') {
			$orderCol = 'category_type '.$orderDirn.', title';
		}
		if ($orderCol == 'state') {
			$orderCol = 'state '.$orderDirn.', title';
		}
		if(TJTB_JVERSION==3){
			$query->order($db->escape($orderCol.' '.$orderDirn));
		} else {
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		}
		//echo $query;
		return $query;
	}
	
	public function getOnePlugin(){
		$store = 'twojtoolbox::pluginlist::getoneplugin';
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}
		$this->cache[$store] = 0;
		$sad = (int) JFactory::getApplication()->getUserState('com_twojtoolbox.options.sad', 0);
		
		//$inst_plug = $this->getInstalPlugins();
		//$new_plug = $this->getNewPlugins();
		//if( count($inst_plug) = 1 && count($new_plug) = 0  )
		
		if($sad==-1 || $sad==-2 ){
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('type');
			$query->from('#__twojtoolbox_plugins');
			$query->where('install=1');
			$db->setQuery($query);
			$ret_l = $db->loadColumn(0);
			if(is_array($ret_l) && count($ret_l)==1){
				$this->cache[$store] = $ret_l[0];
			}
		}
		return $this->cache[$store];
	}
	
	public function getUpdate( ){	
		$store = 'twojtoolbox::pluginlist::needupdate';
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}
		$query = $this->_db->getQuery(true);
		$query->select('`update`');
		$query->where('`id`=1');
		$query->from('#__twojtoolbox_config');
		$this->_db->setQuery($query);
		$update_res =   $this->_db->loadResult();
		if ( (time() - $update_res ) > (60*60*24*7) ) {
			$update_res = 1;
		} else $update_res = 0;
		$this->cache[$store] = $update_res; 
		return $this->cache[$store];
	}
	
	protected function _getPlugins( $install_type = 0 ){	
		$store = 'twojtoolbox::pluginlist::'.$install_type;
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}
		$query = $this->_db->getQuery(true);
		$query->select('id,title,type,install'.($install_type?',v_active':''));
		$query->where('install='.$install_type);
		$query->from('#__twojtoolbox_plugins');
		$this->_db->setQuery($query);
		$items =   $this->_db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		$this->cache[$store] = $items; 
		return $this->cache[$store];
	}
	
	public function getInstalPlugins( ){ 	return $this->_getPlugins(1); }
	public function getNewPlugins(){ 		return $this->_getPlugins(0); }
	
}
