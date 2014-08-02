<?php
/**
 * fwgallery y 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_komento' . DS . 'komento_plugins' . DS .'abstract.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_fwgallery' . DS . 'helpers' . DS .'helper.php');

// Load all required files by component
//require_once('COM_SAMPLE_DEPENDENCIES');

class KomentoComFWGallery extends KomentoExtension {
	public $component = 'com_fwgallery';
	public $_item;

	public $_map = array(
		'id' => 'id',
		'title' => 'name',
		'hits' => 'hits',
		'created_by' => 'user_id',
		'catid' => 'project_id',
		'permalink' => 'permalink_field'
	);

	public function load($cid) {
		static $instances = array();

		if(!isset($instances[$cid])) {
			$db = JFactory :: getDBO();
			$db->setQuery('SELECT id, name, hits, user_id, project_id FROM #__fwg_files WHERE id = '.(int)$cid);
			if (!$this->_item = $db->loadObject()) return false;
			$this->_item->permalink_field = $this->prepareLink('index.php?option=com_fwgallery&view=image&id='.$this->_item->id);
			$instances[$cid] = $this->_item;
		}

		$this->_item = $instances[$cid];

		return $this;
	}

	public function getContentIds($categories = '') {
		$articleIds = array();
		$where = array();
		if($categories) {
			if(!is_array($categories)) $categories = explode(',', $categories);
			JArrayHelper :: toInteger($categories);

			$where[] = 'project_id IN ('.implode(',', $categories).')';
		}
		$db = JFactory :: getDBO();
		$db->setQuery('SELECT id FROM #__fwg_files '.($where?('WHERE '.implode(' AND ', $where).' '):'').'ORDER BY id');
		return $db->loadResultArray();
	}

	public function getCategories() {
		$categories = array();
        $db = & JFactory :: getDBO();

        $db->setQuery('SELECT * FROM #__fwg_projects AS p ORDER BY parent, name');
        $children = array ();
        if ($mitems = $db->loadObjectList()) {
            // first pass - collect children
            foreach ($mitems as $v) {
                $pt = $v->parent;
                $list = @ $children[$pt] ? $children[$pt] : array ();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
		JHTML :: addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_fwgallery'.DS.'helpers');
        $categories = JHTML :: _('fwGalleryCategory.treerecurse', 0, '', array (), $children, 9999, 0, 0);

		return $categories;
	}

	// to determine if is listing view
	public function isListingView() {
		return JRequest::getCmd('view') == 'gallery';
	}

	// to determine if is entry view
	public function isEntryView() {
		return JRequest::getCmd('view') == 'image';
	}

	public function onExecute(&$article, $html, $view, $options = array()) {
		return $html;
	}
}
