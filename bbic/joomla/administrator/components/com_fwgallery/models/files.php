<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelFiles extends JModelLegacy {
    function getUserState($name, $def='', $type='cmd') {
        $app = JFactory::getApplication();
        return $app->getUserStateFromRequest('com_fwgallery.files.'.$name, $name, $def, $type);
    }
    function saveorder() {
        $cid = JRequest::getVar('cid');
        $order = JRequest::getVar('order');

		$pids = array();
        if (is_array($cid) and is_array($order) and count($cid) and count($cid) == count($order)) {
            $db = JFactory::getDBO();
            $image = $this->getTable('file');
            foreach ($cid as $num=>$id) {
            	$image->load($id);
            	if (array_search($id, $pids) === false) {
            		$pids[$id] = $image->project_id;
            	}
            	if ($image->ordering != ($odering = (int)JArrayHelper::getValue($order, $num))) {
            		$image->ordering = $odering;
            		$image->store();
            	}
            }
            if ($pids) {
            	foreach ($pids as $image_id=>$project_id) {
            		$image->load($image_id);
		            $image->reorder('project_id = '.$project_id);
            	}
            }
            return true;
        }
        return false;
    }
    function orderdown() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $image = $this->getTable('file');
            $image->load($id);
            $image->move(1, 'project_id='.$image->project_id);
            return true;
        }
        return false;
    }
    function orderup() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $image = $this->getTable('file');
            $image->load($id);
            $image->move(-1, 'project_id='.$image->project_id);
            return true;
        }
        return false;
    }
    function getProjects() {
        $db = JFactory::getDBO();
        $db->setQuery('
SELECT
	p.id,
	p.parent,
	p.name
FROM
	#__fwg_projects AS p
	LEFT JOIN #__users AS u ON u.id = p.user_id
ORDER BY
	parent,
	name');
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
        return JHTML :: _('fwGalleryCategory.treerecurse', 0, '', array (), $children, 9999, 0, 0);
    }
    function getFile() {
        $file = $this->getTable('file');
        if (($ids = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($ids, 0)) or $id = JRequest::getInt('id', 0)) {
            $file->load($id);
        } else if ($project_id = JRequest :: getInt('project_id')) {
        	$file->project_id = $project_id;
        }
        return $file;
    }
    function _collectWhere() {
        $where = array();

        if ($data = $this->getUserState('search', '', 'string') and is_string($data)) {
        	$db = JFactory :: getDBO();
            $where[] = '(f.name LIKE \'%'.$db->escape($data).'%\' OR f.filename LIKE \'%'.$db->escape($data).'%\')';
        }
        if ($data = $this->getUserState('project_id')) {
            $where[] = 'f.project_id = '.$data;
        }
        return $where?('WHERE '.implode(' AND ', $where)):'';
    }
    function getFilesQty() {
        $db = JFactory::getDBO();
        $db->setQuery('
SELECT
    COUNT(*)
FROM
    #__fwg_files AS f
    LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
'.$this->_collectWhere());
        return $db->loadResult();
    }
    function getPagination() {
        $app = JFactory::getApplication();
        jimport('joomla.html.pagination');
        return new JPagination(
        	$this->getFilesQty(),
    		$this->getUserState('limitstart', 0),
    		$this->getUserState('limit', $app->getCfg('list_limit'))
    	);
    }
    function getFiles() {
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $db->setQuery('
SELECT
    f.*,
	t.name AS _type_name,
	t.plugin AS _plugin_name,
    u.name AS _user_name,
    p.name AS _project_name,
    p.user_id AS _user_id
FROM
    #__fwg_files AS f
    LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
	LEFT JOIN #__fwg_types AS t ON t.id = f.type_id
    LEFT JOIN #__users AS u ON u.id = f.user_id
'.$this->_collectWhere().'
ORDER BY
    p.name,
    f.ordering',
    		$this->getUserState('limitstart', 0),
    		$this->getUserState('limit', $app->getCfg('list_limit'))
		);

		if ($list = $db->loadObjectList('id')) {
			foreach ($list as $i=>$row) $list[$i]->_stock = array();
			$ids = array_keys($list);
			$db = JFactory :: getDBO();
			$db->setQuery('SELECT * FROM #__fwg_file_prices WHERE file_id IN ('.implode(',', $ids).') ORDER BY price');
			if ($buff = $db->loadObjectList()) foreach ($buff as $row) {
				$list[$row->file_id]->_stock[] = $row;
			}
			return $list;
		}
    }
    function save() {
        $image = $this->getTable('file');
        if ($id = JRequest::getInt('id') and !$image->load($id)) JRequest :: setVar('id', 0);

        if ($image->bind(JRequest::get('default', JREQUEST_ALLOWHTML)) and $image->check() and $image->store()) {
            $this->setError(JText::_('FWG_THE_IMAGE_DATA').' '.JText::_('FWG_STORED_SUCCESSFULLY'));
            return $image->id;
        } else
        	$this->setError(JText::_('FWG_THE_IMAGE_DATA').' '.JText::_('FWG_STORING_ERROR').':'.$image->getError());
    }
    function remove() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            foreach ($cid as $id) {
                $image->delete($id);
            }
            $this->setError(JText::_('FWG_IMAGE_S__REMOVED'));
            return true;
        }
        $this->setError(JText::_('FWG_NO_IMAGE_S__ID_PASSED_TO_REMOVE'));
        return false;
    }
    function publish() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            $image->publish($cid, 1);
            $this->setError(JText::_('FWG_IMAGE_S__PUBLISHED'));
            return true;
        }
        $this->setError(JText::_('FWG_NO_IMAGE_S__ID_PASSED_TO_PUBLISH'));
        return false;
    }
    function unpublish() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            $image->publish($cid, 0);
            $this->setError(JText::_('FWG_IMAGE_S__UNPUBLISHED'));
            return true;
        }
        $this->setError(JText::_('FWG_NO_IMAGE_S__ID_PASSED_TO_UNPUBLISH'));
        return false;
    }
    function select() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            return $image->select($cid);
        }
        return false;
    }
    function unselect() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            return $image->unselect($cid);
        }
        return false;
    }
    function getClients() {
        $db = JFactory::getDBO();
        $db->setQuery('SELECT u.id, u.name FROM #__users AS u ORDER BY u.name');
        return $db->loadObjectList();
    }
    function clockwise() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            return $image->clockwise($cid);
        }
        return false;
    }
    function counterClockwise() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            return $image->counterClockwise($cid);
        }
        return false;
    }
	function install() {
		$success = $errors = 0;
		if ($files = JRequest :: getVar('images', array(), 'FILES', 'ARRAY') and !empty($files['name'])) {
			jimport('joomla.filesystem.file');
			JRequest :: setVar('id', 0);
			foreach ($files['name'] as $i => $name) if (!$files['error'][$i]) {
				$file = array(
					'name' => $name,
					'type' => $files['type'][$i],
					'tmp_name' => $files['tmp_name'][$i],
					'error' => $files['error'][$i],
					'size' => $files['size'][$i]
				);
				JRequest :: setVar('name', JFile :: stripExt($name));
				$_FILES['filename'] = $file;
				$image = $this->getTable('file');
				if ($image->bind(JRequest :: get()) and $image->check() and $image->store()) {
					$success++;
				} else {
					$errors++;
				}
			}
		}
		$this->setError(JText :: sprintf('FWRE_BATCH_UPLOAD_RESULT', $success, $errors));
		return $success;
	}
	function getMedias() {
		$medias = array();
		$types = array('flv'=>'flv','youtube'=>'youtube','vimeo'=>'vimeo','blip.tv'=>'blip.tv', 'mov'=>'mov', 'mp4'=>'mp4', 'divx'=>'divx', 'avi'=>'avi');
		foreach ($types as $key=>$media) {
			$medias[] = JHTML :: _('select.option', $key, $media, 'id', 'name');
		}
		return $medias;
	}
}
