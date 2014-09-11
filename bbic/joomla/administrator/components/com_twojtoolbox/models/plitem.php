<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/


defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');  
jimport('joomla.application.component.modeladmin');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class TwojToolboxModelPlitem extends JModelAdmin{

	protected $plugininfo = null;
	protected $selecttype = null;
	protected $demos 	= array();
	
	public function getType(){
		if(!$this->plugininfo) return false;
		return $this->plugininfo->type;
	}
	
	public function getDemos( $get_param_for = '' ){
		
		if(!$this->plugininfo) return false;
		$store = 'twojtoolbox::plugininfo::demos'.$this->plugininfo->type;
		
		if (!empty($this->cache[$store])) {
			if(!$get_param_for) return $this->cache[$store];
		} else {
			$this->demos = array();
			$demos_file = JPATH_COMPONENT_SITE.'/plugins/'.$this->plugininfo->type.'/'.$this->plugininfo->v_active.'/demo.xml';
			if(JFile::exists($demos_file)){
				$xml =JFactory::getXML($demos_file);
				if($xml && isset($xml->demos) && isset($xml->demos->demo) && count($xml->demos->demo) ){
					foreach ($xml->demos->demo as $demo) {
						$temp_obj = new JObject;
						$temp_obj->title	= (string) $demo['title'];
						$temp_obj->code		= (string) $demo['code'];
						$temp_obj->img		=  JURI::root().'components/com_twojtoolbox/plugins/'.$this->plugininfo->type.'/'.$this->plugininfo->v_active.'/demos/'.$temp_obj->code.'.png';
						$temp_obj->param	= (string) $demo['param'];
						$this->demos[] 		= $temp_obj;
					}
				}
			}
		}
		if($get_param_for){
			for($i=0;$i<count($this->demos);$i++) 
				if($this->demos[$i]->code==$get_param_for){
					return $this->demos[$i]->param;
				}
			return false;
		}
		if(count($this->demos)) $this->cache[$store] = $this->demos;
			else $this->cache[$store] = false;
		return $this->cache[$store];
	}
	
	public function delete(&$pks){
		$db = JFactory::getDBO();
		$typeArrow = array();
		foreach ($pks as $i => $pk) {
			$query = $db->getQuery(true);
			$query->select('id, type')->from('#__twojtoolbox')->where('id = '.(int) $pk);
			$db->setQuery( (string) $query);
			$typeArrow = $typeArrow + $db->loadAssocList('id');			
		}
		//print_r($typeArrow);
		$rez = parent::delete($pks);
		if($rez){
			$model =& TwojJModel::getInstance('Element', 'TwojToolboxModel');
			
			foreach ($pks as $i => $pk) {
				// Delete menu items
				$query = $db->getQuery(true);
				$query->delete('#__twojtoolbox_menu')->where('id = '.(int) $pk);
				$db->setQuery( (string) $query);
				if(TJTB_JVERSION==2) $db->execute();
					else $db->query();
				
				$query = $db->getQuery(true);
				$query->delete('#__twojtoolbox_data')->where('pluginid = '.(int) $pk);
				$db->setQuery( (string) $query);
				if(TJTB_JVERSION==2) $db->execute();
					else $db->query();
					
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__twojtoolbox_elements');
				$query->where('catid = '.(int) $pk);
				$db->setQuery( (string) $query);
				$del_elements = $db->loadColumn(0);
				if(count($del_elements))$model->delete($del_elements);
				
				if(isset( $typeArrow[(int) $pk] ) && $typeDeleteRow = $typeArrow[(int) $pk]){
					$query = $db->getQuery(true);
					$query->select('dat.json');
					$query->where('dat.`plugintype` = '. $db->quote($typeArrow[(int) $pk]['type']) );
					$query->from('#__twojtoolbox_data AS dat');
					$db->setQuery( (string) $query);
					
			//echo (string) $query."<br />";
					
					if( $jsonInput = $db->loadResult() ){
			//echo $jsonInput;
						$jsonArray = json_decode($jsonInput, true);
			//echo "<br />";
			//print_r($jsonArray);
						if( $jsonArray !== NULL ){
							$jsonArray = $this->checkPatch($jsonArray,  (int) $pk);
							if( $jsonArray && is_array($jsonArray) ){
			//echo "<br />";
			//print_r($jsonArray);
								$jsonString = json_encode($jsonArray);
								if( $jsonString !== NULL ){
									$row = JTable::getInstance('Data', 'TwojToolboxTable'); 
									$row->loadPluginType('gallery');
									$row->json = $jsonString;
									$jsonInput = $jsonString;
									$row->check();
									$row->store();
								}
							}
						}
					}  else echo "error  json";
					//echo "<br />".$jsonInput;
					
				} else echo "error type";
			}
		}
		//die();
		return $rez;
	}
	
	
	public function checkPatch($elementArray, $elementNeed){
		for($i=0;$i<count($elementArray);$i++){
			if(isset($elementArray[$i]['id']) ){
				if( $elementArray[$i]['id'] == $elementNeed ){
					if( isset($elementArray[$i]['children']) ){
						 $tempArray = $elementArray[$i]['children'];
						 /* unset($elementArray[$i]); */
						array_splice($elementArray, $i, 1);
						 $elementArray = $elementArray + $tempArray;
					} else {
						/*  unset($elementArray[$i]); */
						 array_splice($elementArray, $i, 1);
					}
					 return $elementArray;
				}
				if( isset($elementArray[$i]['children']) ){
					$newArray = $this->checkPatch($elementArray[$i]['children'], $elementNeed);
					if( $newArray && is_array($newArray) ){
						$elementArray[$i]['children'] = $newArray;
						return $elementArray; 
					}
				}
			}
		}
		return 0;
	}
	
	public function getTable($type = 'Plitem', $prefix = 'TwojToolboxTable', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true){
	
		$id = JRequest::getInt('id', 0);
		$this->selecttype = JRequest::getString('selecttype', '');
		
		if( !$id && !$this->selecttype ){
			$jform = JRequest::getVar('jform', array(), 'post', 'array');
			if(isset($jform['type'])) $this->selecttype = $jform['type'];
		}
		if( !$id && !$this->selecttype ){
			$data = JFactory::getApplication()->getUserState('com_twojtoolbox.edit.plitem.data', array());
			if(count($data) && isset($data['type']) && $data['type']){
				$this->selecttype = $data['type'];
			}
		}
		if( !$id && !$this->selecttype ){
			$save2new_type = JFactory::getApplication()->getUserState('com_twojtoolbox.edit.plitem.save2new.type', '');
			if( $save2new_type){
				$this->selecttype = $save2new_type;
			}
		}
		
		if( $id > 0 ){
			TwojToolboxHelper::cgid($id);
			$this->plugininfo = TwojToolboxHelper::plugin_info($id);
		} elseif( $this->selecttype ){
			$this->plugininfo = TwojToolboxHelper::plugin_info( $this->selecttype, 1 );
		}	else {
			$app = JFactory::getApplication();
			$app->redirect('index.php?option=com_twojtoolbox', JText::_( 'COM_TWOJTOOLBOX_PLEASESELECITEMS' ));
		}		
			
		if( $this->plugininfo){
			$xml_p = JPATH_COMPONENT_SITE.'/plugins/'.$this->plugininfo->type.'/'.$this->plugininfo->v_active.'/item_options.xml';
			
			if(JFile::exists($xml_p)){
				$form = $this->loadForm('com_twojtoolbox.plitem', $xml_p, array('control' => 'jform', 'load_data' => $loadData));
			} else {
				JFactory::getApplication()->redirect( 'index.php?option=com_twojtoolbox', JText::_( 'COM_TWOJTOOLBOX_ERROR_OPTIONLIST' ), 'error' );
				return false;
			}
			
			$form->addFieldPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/fields');
			JFormHelper::loadFieldClass('menuitem');
			
			$field_p = JPATH_COMPONENT_SITE.'/plugins/'.$this->plugininfo->type.'/'.$this->plugininfo->v_active.'/fields';
			if(JFolder::exists($field_p)) $form->addFieldPath($field_p);
			
		}
		if (empty($form)){
			return false;
		}
		return $form;
	}
	
	protected function loadFormData(){
		$data = JFactory::getApplication()->getUserState('com_twojtoolbox.edit.plitem.data', array());
		
		if (empty($data)){
			$data = $this->getItem();
			if ($this->getState('plitem.id') == 0) {
				$data->set('type', $this->selecttype);
			}
		} else {
			if(isset($data['id']) && $data['id']) TwojToolboxHelper::cgid( $data['id'] ); 
		}
		if( JFactory::getApplication()->getUserState('com_twojtoolbox.edit.plitem.default', 0) == 1 ){
			JFactory::getApplication()->setUserState('com_twojtoolbox.edit.plitem.default', 0);
			JFactory::getApplication()->enqueueMessage( JText::_('COM_TWOJTOOLBOX_ITEM_DEFAULT_APPLY_OK') );
			$data->set('params', array());
		}
		$democode = JFactory::getApplication()->getUserState('com_twojtoolbox.edit.plitem.democode', '');
		if( $democode ){
			JFactory::getApplication()->setUserState('com_twojtoolbox.edit.plitem.democode', '');
			$new_param = $this->getDemos($democode);
			if($new_param){
				$parameter = new JRegistry;
				$parameter->loadString($new_param);
				$data->set('params', $parameter->toArray());
				JFactory::getApplication()->enqueueMessage( JText::_('COM_TWOJTOOLBOX_ITEM_DEMO_APPLY_OK') );
			}
		}
		return $data;
	}
	
	public function save($data){
		if( $return_value =  parent::save($data) ){
			$table	= $this->getTable();
			$id_in		= (int)$this->getState('plitem.id');
			if ($id_in > 0) {
			
				$app = JFactory::getApplication();
				if ($app->input->get('task') == 'save2copy'){
					if( $oldId = (int) $app->input->get('id') ){
						$row = JTable::getInstance('Data', 'TwojToolboxTable'); 
						$row->loadPluginId($oldId);  
						$row->id = '';
						$row->pluginid = $id_in;
						$row->check();
						$row->store();
					}
				}
				
				$table	= $this->getTable(); 
				$table->load($id_in);
				if($table->id){
					$db		= $this->getDbo();
					$query	= $db->getQuery(true);
					$query->delete();
					$query->from('#__twojtoolbox_menu');
					$query->where('id = '.(int) $table->id);
					$db->setQuery((string)$query);
					$db->query();
					if (!$db->query()){
						$this->setError($db->getErrorMsg());
						return false;
					}
			
					if ( !empty($data['itemid']) && is_array($data['itemid']) && count($data['itemid']) ){
						$tuples = array();
						foreach ($data['itemid'] as &$pk){
							$tuples[] = '('.(int)$table->id.','.(int) $pk.')';
						}
						$db->setQuery(
							'INSERT INTO #__twojtoolbox_menu (id, itemid) VALUES '.
							implode(',', $tuples)
						);
						if (!$db->query()){
							$this->setError($db->getErrorMsg());
							return false;
						}
					}
				}
			}
			
			
		}
		return $return_value;
	}
	
	public function getItem($pk = null){
		$return_value = parent::getItem($pk);
		$pk	= (int) $return_value->id;
		$db	= $this->getDbo();
		$db->setQuery( 'SELECT itemid FROM #__twojtoolbox_menu WHERE id = '.$pk );
		$return_value->itemid = $db->loadColumn();
		if (!$pk){
			$return_value->itemid = -1;
		}
		return $return_value;
	}
	
}
