<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.database.table');
require_once JPATH_COMPONENT.'/helpers/twojtoolbox.php';
class TwojToolboxTableElement extends JTable{

	function __construct(&$db){
		parent::__construct('#__twojtoolbox_elements', 'id', $db);
	}

	public function bind($array, $ignore = ''){
		if (isset($array['params']) && is_array($array['params'] )  ){
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}
		return parent::bind($array, $ignore);
	}
	
	function check(){
		if (empty($this->ordering)) {
			$this->ordering = self::getNextOrder('`catid`=' . $this->_db->Quote($this->catid));
		}
		JFactory::getApplication()->setUserState('com_twojtoolbox.edit.element.catid', $this->catid);
		return true;
	}
	
	public function store($updateNulls = false){
		if( $this->img ){
			jimport('joomla.filesystem.file');
			jimport('joomla.utilities.arrayhelper');
			jimport('joomla.filesystem.folder');
			
			$image_filename = str_replace('\\', '/', $this->img);
			$image_filename = str_replace('\\\\', '/', $image_filename);
			$image = JPATH_SITE.'/media/com_twojtoolbox/'.$image_filename;
	
			if( JFile::exists( $image ) && function_exists('getimagesize') ){
				$size = getimagesize($image);
				if( $size && is_array($size) ){
					if( isset($size[0]) ) $this->width = (int) $size[0];
					if( isset($size[1]) ) $this->height = (int) $size[1];
				}
			}
		}
		// print_r($this);
		// die();
		return parent::store($updateNulls);
	}

	public function load($pk = null, $reset = true){	
		if (parent::load($pk, $reset)){
			$params = new JRegistry;
			$params->loadString($this->params);
			$this->params = $params;
			return true;
		} else {
			return false;
		}
	}
	
	public function publish($pks = null, $state = 1, $userId = 0){
		$k = $this->_tbl_key;
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;
		if (empty($pks)){
			if ($this->$k) {
				$pks = array($this->$k);
			} else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}
		$table = JTable::getInstance('Element','TwojToolboxTable');
		foreach ($pks as $pk){
			if(!$table->load($pk)){
				$this->setError($table->getError());
			}
			if($table->checked_out==0 || $table->checked_out==$userId){
				$table->state = $state;
				$table->checked_out=0;
				$table->checked_out_time=0;
				$table->check();
				if (!$table->store()){
					$this->setError($table->getError());
				}
			}
		}
		return count($this->getErrors())==0;
	}
	
	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_twojtoolbox.element.'.(int) $this->$k;
	}

	protected function _getAssetTitle(){
		return $this->title;
	}

	/* protected function _getAssetParentId( $table = null, $id = null ){
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_twojtoolbox');
		return $asset->id;
	} */
}
