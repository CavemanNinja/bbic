<?php
/**
 * FW Gallery Frontend Manager 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelFrontendmanager extends JModelLegacy {
    function getUserState($name, $def='', $type='cmd') {
        $app = JFactory::getApplication();
        $context = 'com_fwgallery.frontendmanager.';
        return $app->getUserStateFromRequest($context.$name, $name, $def, $type);
    }

    function _collectGalleriesWhere() {
    	$user = JFactory :: getUser();
        $where = array(
        	'((p.published = 1 AND p.is_public = 1) OR p.user_id = '.$user->id.')'
        );

        return $where?('WHERE '.implode(' AND ', $where)):'';
    }

    function getProjectsQty() {
        $db = JFactory::getDBO();
        $db->setQuery('SELECT COUNT(*) FROM #__fwg_projects AS p '.$this->_collectGalleriesWhere());
        return $db->loadResult();
    }

    function getGalleriesPagination() {
        $app = JFactory::getApplication();
        jimport('joomla.html.pagination');
        $pagination = new JPagination(
        	$this->getProjectsQty(),
        	$this->getUserState('limitstart', 0),
        	$this->getUserState('limit', $app->getCfg('list_limit'))
    	);
        return $pagination;
    }

    function getProject() {
        $project = $this->getTable('project');
        if (($ids = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($ids, 0)) or $id = JRequest::getInt('id', 0)) {
            $project->load($id);
        }
        return $project;
    }

    function getGalleriesProjects() {
        $db = JFactory::getDBO();
        $db->setQuery('
SELECT
    p.*,
    u.name AS _user_name,
	(SELECT g.title FROM #__usergroups AS g WHERE g.id = p.gid) AS _group_name,
	(SELECT COUNT(*) FROM #__fwg_files AS f WHERE f.project_id = p.id) AS _qty
FROM
    #__fwg_projects AS p
    LEFT JOIN #__users AS u ON u.id = p.user_id
'.$this->_collectGalleriesWhere().'
ORDER BY
	p.parent,
    p.ordering'
		);
		$list = array();
        if ($rows = $db->loadObjectList()) {
            $children = array();
            foreach ($rows as $v) {
                $pt = $v->parent;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push( $list, $v );
                $children[$pt] = $list;
            }
	        $app = JFactory::getApplication();
	        $limit = (int)$this->getUserState('limit', $app->getCfg('list_limit'));
	        $limitstart = (int)$this->getUserState('limitstart', 0);
	        $levellimit = (int)$this->getUserState('levellimit', 10);
            $list = JHTML::_('fwGalleryCategory.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ) );
            if ($limit) $list = array_slice($list, $limitstart, $limit);
        }
        return $list;
    }

	function saveorder() {
		$data = explode('_', JRequest :: getCmd('layout'));
		$type = JArrayHelper :: getValue($data, 1);
		if ($type == 'image') return $this->saveorderImages();
		else return $this->saveorderGalleries();
	}

    function saveorderGalleries() {
        $cid = (array)JRequest::getVar('cid');
        $order = (array)JRequest::getVar('order');

        if (count($cid) and count($cid) == count($order)) {
            $db = JFactory::getDBO();
            $project = $this->getTable('project');
            foreach ($cid as $num=>$id) {
                $db->setQuery('UPDATE #__fwg_projects SET ordering = '.(int)JArrayHelper::getValue($order, $num).' WHERE id = '.(int)$id);
                $db->query();
            }
            JArrayHelper :: toInteger($cid);
            $db->setQuery('SELECT DISTINCT parent FROM  #__fwg_projects WHERE id IN ('.implode(',',$cid).')');
            if ($parents = $db->loadResultArray()) foreach ($parents as $parent) $project->reorder('parent='.(int)$parent);
            return true;
        }
        return false;
    }

	function orderdown() {
		$data = explode('_', JRequest :: getCmd('layout'));
		$type = JArrayHelper :: getValue($data, 1);
		if ($type == 'image') return $this->orderdownImages();
		else return $this->orderdownGalleries();
	}

    function orderdownGalleries() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $project = $this->getTable('project');
            if ($project->load($id)) $project->move(1);
            return true;
        }
        return false;
    }

	function orderup() {
		$data = explode('_', JRequest :: getCmd('layout'));
		$type = JArrayHelper :: getValue($data, 1);
		if ($type == 'image') return $this->orderupImages();
		else return $this->orderupGalleries();
	}

    function orderupGalleries() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $project = $this->getTable('project');
            if ($project->load($id)) $project->move(-1);
            return true;
        }
        return false;
    }

	function save() {
		$data = explode('_', JRequest :: getCmd('layout'));
		$type = JArrayHelper :: getValue($data, 1);
		if ($type == 'image') return $this->saveImages();
		else return $this->saveGalleries();
	}

    function saveGalleries() {
        $project = $this->getTable('project');
        if ($id = JRequest::getInt('id') and !$project->load($id)) JRequest :: setVar('id', 0);

        if ($project->bind(JRequest::get('default', JREQUEST_ALLOWHTML)) and $project->check() and $project->store()) {
            $this->setError(JText::_('The gallery data').' '.JText::_('stored successfully'));
            return $project->id;
        } else
            $this->setError(JText::_('The gallery data').' '.JText::_('storing error').':'.$project->getError());
    }

    function getClients() {
        $db = JFactory::getDBO();
        $db->setQuery('SELECT u.id, u.name FROM #__users AS u ORDER BY u.name');
        return $db->loadObjectList();
    }

	function remove() {
		$data = explode('_', JRequest :: getCmd('layout'));
		$type = JArrayHelper :: getValue($data, 1);
		if ($type == 'image') return $this->removeImages();
		else return $this->removeGalleries();
	}

    function removeGalleries() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $project = $this->getTable('project');
            foreach ($cid as $id) $project->delete($id);
            $this->setError(JText::_('GALLERY_IES_REMOVED'));
            return true;
        } else $this->setError(JText::_('No gallery ID passed to remove'));
        return false;
    }

	function publish() {
		$data = explode('_', JRequest :: getCmd('layout'));
		$type = JArrayHelper :: getValue($data, 1);
		if ($type == 'image') return $this->publishImages();
		else return $this->publishGalleries();
	}

    function publishGalleries() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('project');
            $image->publish($cid, 1);
            $this->setError(JText::_('GALLERY_IES_PUBLISHED'));
            return true;
        } else $this->setError(JText::_('No gallery ID passed to publish'));
        return false;
    }

	function unpublish() {
		$data = explode('_', JRequest :: getCmd('layout'));
		$type = JArrayHelper :: getValue($data, 1);
		if ($type == 'image') return $this->unpublishImages();
		else return $this->unpublishGalleries();
	}

    function unpublishGalleries() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('project');
            $image->publish($cid, 0);
            $this->setError(JText::_('GALLERY_IES_UNPUBLISHED'));
            return true;
        } else $this->setError(JText::_('No gallery ID passed to publish'));
        return false;
    }

    function getCategories($id = 0) {
        $db = JFactory :: getDBO();
        $user = JFactory :: getUser();
        $db->setQuery('SELECT id, name, parent FROM #__fwg_projects WHERE'.($id?(' id <> '.$id.' AND '):'').' (user_id = '.(int)$user->id.' OR (is_public = 1 AND published = 1)) ORDER BY parent, name');
        $children = array ();
        if ($mitems = $db->loadObjectList()) {
            foreach ($mitems as $v) {
                $pt = $v->parent;
                $list = @ $children[$pt] ? $children[$pt] : array ();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        return JHTML :: _('fwGalleryCategory.treerecurse', 0, '', array(), $children, 9999, 0, 0);
    }

    /* images model */
    function saveorderImages() {
        $cid = JRequest::getVar('cid');
        $order = JRequest::getVar('order');

		$pids = array();
        if (is_array($cid) and is_array($order) and count($cid) and count($cid) == count($order)) {
            $db = JFactory :: getDBO();
            $user = JFactory :: getUser();
            $image = $this->getTable('file');
            foreach ($cid as $num=>$id) {
            	if ($image->load($id, $user->id)) {
	            	if (array_search($id, $pids) === false) {
	            		$pids[$id] = $image->project_id;
	            	}
	            	if ($image->ordering != ($odering = (int)JArrayHelper::getValue($order, $num))) {
	            		$image->ordering = $odering;
	            		$image->store();
	            	}
            	}
            }
            if ($pids) {
            	foreach ($pids as $image_id=>$project_id) {
	            	if ($image->load($image_id, $user->id)) {
			            $image->reorder('project_id = '.$project_id);
	            	}
            	}
            }
            return true;
        }
        return false;
    }
    function orderdownImages() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $image = $this->getTable('file');
            $user = JFactory :: getUser();
            if ($image->load($id, $user->id)) {
	            $image->move(1, 'project_id='.$image->project_id);
	            return true;
            }
        }
        return false;
    }
    function orderupImages() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $image = $this->getTable('file');
            $user = JFactory :: getUser();
            if ($image->load($id, $user->id)) {
	            $image->move(-1, 'project_id='.$image->project_id);
	            return true;
            }
        }
        return false;
    }
    function getImagesProjects() {
        $db = JFactory::getDBO();
        $db->setQuery('
SELECT
	p.id,
	p.parent,
	p.name,
	p.user_id
FROM
	#__fwg_projects AS p
	LEFT JOIN #__users AS u ON u.id = p.user_id
ORDER BY
	parent,
	name');
        $children = array ();
        if ($mitems = $db->loadObjectList()) {
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
	        $user = JFactory :: getUser();
            $file->load($id, $user->id);
        } else if ($project_id = JRequest :: getInt('project_id')) {
        	$file->project_id = $project_id;
        }
        return $file;
    }
    function _collectImagesWhere() {
        $user = JFactory :: getUser();
        $where = array('f.user_id = '.$user->id);

        if ($data = $this->getUserState('search', '', 'string') and is_string($data)) {
        	$db = JFactory :: getDBO();
            $where[] = "(f.name LIKE '%".$db->getEscaped($data)."%' OR f.filename LIKE '%".$db->getEscaped($data)."%')";
        }
        if ($data = $this->getUserState('project_id')) {
            $where[] = 'f.project_id = '.$data;
        }

        return $where?('WHERE '.implode(' AND ', $where)):'';
    }
    function getImagesQty() {
        $db = JFactory::getDBO();
        $db->setQuery('
SELECT
    COUNT(*)
FROM
    #__fwg_files AS f
    LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
'.$this->_collectImagesWhere());
        return $db->loadResult();
    }
    function getImagesPagination() {
        $app = JFactory::getApplication();
        jimport('joomla.html.pagination');
        return new JPagination(
        	$this->getImagesQty(),
    		JRequest :: getInt('limitstart'),
    		$this->getUserState('limit', $app->getCfg('list_limit'))
    	);
    }
    function getImages() {
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $db->setQuery('
SELECT
    f.*,
	t.name AS _type_name,
	t.plugin AS _plugin_name,
    p.name AS _project_name,
    p.user_id AS _user_id
FROM
    #__fwg_files AS f
    LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
	LEFT JOIN #__fwg_types AS t ON t.id = f.type_id
'.$this->_collectImagesWhere().'
ORDER BY
    p.name,
    f.ordering',
    		JRequest :: getInt('limitstart'),
    		$this->getUserState('limit', $app->getCfg('list_limit'))
		);

        return $db->loadObjectList();
    }
    function saveImages() {
        $image = $this->getTable('file');
    	$user = JFactory :: getUser();
        if ($id = JRequest::getInt('id') and !$image->load($id, $user->id)) {
        	JRequest :: setVar('id', 0);
        	JRequest :: setVar('user_id', $user->id);
        }

        if ($image->bind(JRequest::get('default', JREQUEST_ALLOWHTML)) and $image->check() and $image->store()) {
            $this->setError(JText::_('The image data').' '.JText::_('stored successfully'));
            return $image->id;
        } else
        	$this->setError(JText::_('The image data').' '.JText::_('storing error').':'.$image->getError());
    }
    function removeImages() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            $user = JFactory :: getUser();
            foreach ($cid as $id) {
            	if ($image->load($id, $user->id)) $image->delete($id);
            }
            $this->setError(JText::_('image removed'));
            return true;
        }
        $this->setError(JText::_('No image ID passed to remove'));
        return false;
    }
    function publishImages() {
        if ($cid = (array)JRequest::getVar('cid')) {
        	JArrayHelper :: toInteger($cid);
        	$db = JFactory :: getDBO();
        	$user = JFactory :: getUser();
        	$db->setQuery('UPDATE #__fwg_files SET published = 1 WHERE user_id = '.$user->id.' AND id IN ('.implode(',',$cid).')');
        	if ($db->query()) {
	        	$this->setError(JText::_('Image published'));
	            return true;
        	}
            return true;
        }
        $this->setError(JText::_('No image ID passed to publish'));
        return false;
    }
    function unpublishImages() {
        if ($cid = (array)JRequest::getVar('cid')) {
        	JArrayHelper :: toInteger($cid);
        	$db = JFactory :: getDBO();
        	$user = JFactory :: getUser();
        	$db->setQuery('UPDATE #__fwg_files SET published = 0 WHERE user_id = '.$user->id.' AND id IN ('.implode(',',$cid).')');
        	if ($db->query()) {
	        	$this->setError(JText::_('Image unpublished'));
	            return true;
        	}
        }
        $this->setError(JText::_('No image ID passed to unpublish'));
        return false;
    }
    function select() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            $user = JFactory :: getUser();
            return $image->select($cid, $user->id);
        }
        return false;
    }
    function unselect() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            $user = JFactory :: getUser();
            return $image->unselect($cid, $user->id);
        }
        return false;
    }
    function clockwise() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            $user = JFactory :: getUser();
            return $image->clockwise($cid, $user->id);
        }
        return false;
    }
    function counterClockwise() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('file');
            $user = JFactory :: getUser();
            return $image->counterClockwise($cid, $user->id);
        }
        return false;
    }
    function getImagesViewPlugins() {
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper :: importPlugin('fwgallery');
		return $dispatcher->trigger('fwGetSiteImagesForm', array());
    }
	function getPlugin() {
		if ($plugin = JRequest :: getString('plugin'))
			return JPluginHelper :: getPlugin('fwgallery', $plugin);
	}
	function processPlugin() {
		if ($plugin = $this->getPlugin() and JPluginHelper :: importPlugin('fwgallery', $plugin->name)) {
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger('fwProcess');
		}
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
