<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelGrid extends JModelLegacy {
    function getUserState($name, $def='', $type='cmd') {
        $app = JFactory :: getApplication();
        $context = 'com_fwgallery.grid.';
        return $app->getUserStateFromRequest($context.$name, $name, $def, $type);
    }
    function getGalleryId($id = null, $load_subgalleries = true) {
		if (is_null($id)) {
			$id = (int)JRequest :: getInt('id');
			if (!$id) {
				$app = JFactory :: getApplication();
				$params = $app->getParams();
				$id = $params->get('id');
			}
		}
		if (!$id) $id = 0;

		static $data;
		if (!is_array($data)) $data = array();
		$key = (int)$id.'-'.(int)$load_subgalleries;
		if (!isset($data[$key])) {
			$result = array($id);

			$db = JFactory :: getDBO();
			$user = JFactory :: getUser();

			if ($load_subgalleries) {
				$db->setQuery('
SELECT
	id,
	parent
FROM
	#__fwg_projects AS p
WHERE
	published = 1
'.($user->get('isRoot')?'':('
	AND
	(
		p.gid = 0
		OR
		p.gid IS NULL
'.($user->id?('
		OR
		EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).') ORDER BY ug.lft DESC LIMIT 1)'):'').'
	)')));
				$gls = $db->loadObjectList();
				do {
					$id_found = false;
					foreach ($gls as $gl) {
						if (in_array($gl->parent, $result) and !in_array($gl->id, $result)) {
							$result[] = $gl->id;
							$id_found = true;
						}
					}
				} while ($id_found);
			}
			$data[$key] = $result;
		}
		return $data[$key];
    }
	function loadAllSubCategories($id = null, $load_subgalleries = true) {
		$model = JModel :: getInstance('Galleries', 'fwGalleryModel');
		return $model->getAllList($this->getGalleryId($id, $load_subgalleries));
    }
    function loadSubCategories($id = null, $load_subgalleries = true) {
        $db = JFactory :: getDBO();
        $user = JFactory :: getUser();
        $params = JComponentHelper :: getParams('com_fwgallery');

        $ids = $this->getGalleryId($id, $load_subgalleries);
        JArrayHelper :: toInteger($ids, '0');

        $db->setQuery('
SELECT
    p.*,
	p.user_id AS _user_id,
    u.name AS _user_name,
    CASE WHEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS filename,
	(SELECT COUNT(*) FROM #__fwg_projects AS pp WHERE pp.parent = p.id AND pp.published = 1) AS _subfolders
FROM
    #__fwg_projects AS p
    LEFT JOIN #__users AS u ON (u.id = p.user_id)
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
WHERE
	p.parent IN ('.implode(',', $ids).')
	AND
	p.published = 1
'.($user->get('isRoot')?'':('
	AND
	(
		p.gid = 0
		OR
		p.gid IS NULL
'.($user->id?('
		OR
		EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).') ORDER BY ug.lft DESC LIMIT 1)'):'').'
	)
')).'
'.$this->_getCategoryOrderClause(),
        	JRequest :: getInt('glimitstart'),
        	$this->getUserState('glimit', $params->get('galleries_rows', 4) * $params->get('galleries_a_row', 3))
		);
        return $db->loadObjectList();
    }

	function _getCategoryOrderClause() {
		$params = JComponentHelper :: getParams('com_fwgallery');
		switch ($this->getUserState('subcategory_order', $params->get('ordering_galleries'))) {
			case 'new' : $order = 'p.created DESC';
			break;
			case 'old' : $order = 'p.created';
			break;
			case 'name' : $order = 'p.name';
			break;
			default : $order = 'p.ordering';
		}
		return 'ORDER BY '.$order;
	}

    function getGQty($id = null, $load_subgalleries = true) {
        $db = JFactory :: getDBO();
        $user = JFactory :: getUser();
        $params = JComponentHelper :: getParams('com_fwgallery');

        $ids = $this->getGalleryId($id, $load_subgalleries);
        JArrayHelper :: toInteger($ids, '0');

        $db->setQuery('
SELECT
	COUNT(*)
FROM
    #__fwg_projects AS p
    LEFT JOIN #__users AS u ON (u.id = p.user_id)
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
WHERE
	p.parent IN ('.implode(',', $ids).')
	AND
	p.published = 1
'.($user->get('isRoot')?'':('
	AND
	(
		p.gid = 0
		OR
		p.gid IS NULL
'.($user->id?('
		OR
		EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).') ORDER BY ug.lft DESC LIMIT 1)'):'').'
	)')));
        return $db->loadResult();
    }

    function getGPagination() {
        $params = JComponentHelper :: getParams('com_fwgallery');
        jimport('joomla.html.pagination');
        include_once(JPATH_COMPONENT.'/helpers/gpagination.php');
        $pagination = new fwGPagination(
        	$this->getGQty(),
        	JRequest :: getInt('glimitstart'),
        	$this->getUserState('glimit', $params->get('galleries_rows', 4) * $params->get('galleries_a_row', 3))
    	);
        return $pagination;
    }

/** --- --- **/

	function _getWhereClause($id = null, $load_subgalleries = true) {
        $ids = $this->getGalleryId($id, $load_subgalleries);
        JArrayHelper :: toInteger($ids, '0');

        $user = JFactory :: getUser();

		$where = array(
			'f.published = 1',
			'p.published = 1',
			'f.project_id IN ('.implode(',', $ids).')'
		);
		if (!$user->get('isRoot')) {
			if ($user->id) {
				$where[] = '(p.gid = 0 OR p.gid IS NULL OR EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).')))';
			} else $where[] = '(p.gid = 0 OR p.gid IS NULL)';
		}
		return $where?('WHERE '.implode(' AND ', $where)):'';
	}

	function _getOrderClause() {
		$params = JComponentHelper :: getParams('com_fwgallery');
		switch ($this->getUserState('order', $params->get('ordering_images'))) {
			case 'new' : $order = 'f.created DESC';
			break;
			case 'old' : $order = 'f.created';
			break;
			case 'name' : $order = 'f.name';
			break;
			case 'voting' : $order = '_rating_value DESC';
			break;
			default : $order = 'f.ordering';
		}
		return 'ORDER BY '.$order;
	}

    function getObj($id = null, $load_subgalleries = true) {
        $project = $this->getTable('project');
        if ($id = $this->getGalleryId($id, $load_subgalleries) and !empty($id[0])) $project->load($id[0]);
        return $project;
    }

    function getQty($id = null, $load_subgalleries = true) {
        $db = JFactory :: getDBO();
        $db->setQuery('
SELECT
	COUNT(*)
FROM
	#__fwg_files AS f
	LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
'.$this->_getWhereClause($id, $load_subgalleries)
		);
        return $db->loadResult();
    }

    function getPagination() {
        $params = JComponentHelper :: getParams('com_fwgallery');
        jimport('joomla.html.pagination');
        $pagination = new JPagination(
        	$this->getQty(),
        	JRequest :: getInt('limitstart'),
        	$this->getUserState('limit', $params->get('images_rows', 4) * $params->get('images_a_row', 3))
    	);
        return $pagination;
    }

    function getList($id = null, $load_subgalleries = true) {
        $db = JFactory :: getDBO();
        $user = JFactory :: getUser();
        $params = JComponentHelper :: getParams('com_fwgallery');

        $db->setQuery('
SELECT
    f.*,
    p.user_id AS _user_id,
	p.color AS _color,
    u.name AS _user_name,
    p.name AS _project_name,
	t.name AS _type_name,
	t.plugin AS _plugin_name,
	CASE WHEN (SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) > 0 THEN (SELECT SUM(v.value) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0)))/(SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) ELSE 0 END AS _rating_value,
	(SELECT SUM(v.value) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) AS _rating_sum,
	(SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) AS _rating_count,
'.($user->id?(
'	(SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v WHERE v.file_id = f.id AND v.user_id = '.(int)$user->id.') AS _is_voted'
):(
	$params->get('public_voting')?(
'	(SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v WHERE v.file_id = f.id AND v.ipaddr = '.(int)ip2long(JFHelper :: getIP()).') AS _is_voted'
	):(
'	1 AS _is_voted'
	)
)).',
	\'\' AS _tags
FROM
    #__fwg_files AS f
	LEFT JOIN #__fwg_types AS t ON t.id = f.type_id
    LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
    LEFT JOIN #__users AS u ON u.id = f.user_id
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
'.$this->_getWhereClause($id, $load_subgalleries).'
'.$this->_getOrderClause(),
        	JRequest :: getInt('limitstart'),
        	15
		);
		$list = $db->loadObjectList('id');

		if ($list) {
			if ($params->get('display_image_tags')) {
				$ids = array_keys($list);
				$db->setQuery('SELECT t.id, t.name, ft.file_id FROM #__fwg_files_tags AS ft, #__fwg_tags AS t WHERE ft.tag_id = t.id AND ft.file_id IN ('.implode(',', $ids).') ORDER BY t.name');
				if ($buff = $db->loadObjectList()) foreach ($buff as $row) {
					if (!is_array($list[$row->file_id]->_tags)) $list[$row->file_id]->_tags = array();
					$list[$row->file_id]->_tags[] = $row->name;
				}
			}
/*			$dispatcher = JDispatcher::getInstance();
			JPluginHelper :: importPlugin('fwgallery');
			$dispatcher->trigger('handleFilesLoad', array(&$list));*/
		}
        return $list;
    }

    function vote() {
    	if (!($id = JRequest :: getInt('id'))) {
			$this->setError(JText :: _('FWG_NO_IMAGE_ID_PASSED'));
			return;
    	}
    	$user = JFactory :: getUser();
        $params = JComponentHelper :: getParams('com_fwgallery');
        $public_voting = $params->get('public_voting');
    	if ($user->id or $public_voting) {
    		$db = JFactory :: getDBO();
    		if ($user->id)
	    		$db->setQuery('SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote WHERE user_id = '.$user->id.' AND file_id = '.(int)$id);
	    	else
	    		$db->setQuery('SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote WHERE ipaddr = '.(int)ip2long(JFHelper :: getIP()).' AND file_id = '.(int)$id);

    		if ($db->loadResult()) {
    			$this->setError(JText :: _('FWG_YOU_HAVE_VOTED_ON_THIS_IMAGE'));
    		} else {
    			$value = max(0, min(5, (int)JRequest :: getInt('value')));
	    		if ($user->id)
	    			$db->setQuery('INSERT INTO #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote (user_id, file_id, value) VALUES ('.$user->id.', '.(int)$id.', '.$value.')');
	    		else
	    			$db->setQuery('INSERT INTO #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote (ipaddr, file_id, value) VALUES ('.(int)ip2long(JFHelper :: getIP()).', '.(int)$id.', '.$value.')');
    			if ($db->query()) {
	    			$this->setError(JText :: _('FWG_YOUR_VOTE_WAS_SUCCESFULLY_RECORDED'));
    			} else {
	    			$this->setError(JText :: _('FWG_THERE_WAS_AN_ERROR_WHILE_YOUR_VOTE_RECORDING'));
    			}
    		}
    	} else {
			$this->setError(JText :: _('FWG_YOU_ARE_NOT_LOGGED_IN'));
    	}
    }

    function getImage() {
    	$table = $this->getTable('file');
    	if ($id = JRequest :: getInt('id')) $table->load($id);
    	return $table;
    }

    function loadModuleData($params) {
    	$db = JFactory :: getDBO();
/* collect filtering clause */
		$where = array(
			'f.published = 1',
			'p.published = 1'
		);
    	if ($gallery_id = (int)$params->get('gallery_id')) $where[] = 'f.project_id = '.$gallery_id;

		$user = JFactory :: getUser();
		if (!$user->get('isRoot')) {
			if ($user->id) {
				$where[] = '(p.gid = 0 OR p.gid IS NULL OR EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).')))';
			} else $where[] = '(p.gid = 0 OR p.gid IS NULL)';
		}
/* collect ordering clause */
    	switch ($params->get('order')) {
    		case 'alpha' : $order = 'f.name';
    		break;
    		case 'popular' : $order = 'f.hits DESC';
    		break;
    		case 'rand' : $order = 'RAND()';
    		break;
    		case 'order' : $order = 'f.ordering';
    		break;
    		default : $order = 'f.created DESC';
    	}
/* load data */
    	$db->setQuery('
SELECT
    f.id,
	f.name,
	f.filename,
	f.project_id,
	f.created,
    p.user_id AS _user_id
FROM
    #__fwg_files AS f
	LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
WHERE
	'.implode(' AND ', $where).'
ORDER BY
	'.$order,
			0,
			$params->get('limit', 10)
		);
		return $db->loadObjectList();
    }
    function loadCategoriesPath($category_id = 0) {
		static $categories_above;
		if (!$categories_above) {
			$categories_above = array();
			if ($category_id) {
				$db = JFactory :: getDBO();
				$parent = new stdclass;
				$parent->parent = $category_id;
				do {
					$db->setQuery('SELECT id, parent, name FROM #__fwg_projects WHERE id = '.(int)$parent->parent);
					if ($parent = $db->loadObject()) $categories_above[] = $parent;
				} while ($parent);

				$categories_above = array_reverse($categories_above);
			}
		}
		return $categories_above;
    }
	function loadModuleParams() {
		$params = '';
		if ($id = JRequest :: getInt('module_id')) {
			$db = JFactory :: getDBO();
			$db->setQuery('SELECT params FROM #__modules WHERE id = '.(int)$id);
			$params = $db->loadResult();
		}
		return new JRegistry($params);
	}
}
