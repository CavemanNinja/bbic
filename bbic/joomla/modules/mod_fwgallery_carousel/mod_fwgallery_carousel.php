<?php
/**
 * FW Gallery Carousel Module 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

$path = JPATH_SITE.'/components/com_fwgallery/helpers/helper.php';
if (file_exists($path)) {
	require_once($path);
	$comp_params = JComponentHelper :: getParams('com_fwgallery');

	$db = JFactory :: getDBO();
	$where = array(
		'f.published = 1',
		'p.published = 1'
	);
	if ($gallery_id = (int)$params->get('gallery_id')) $where[] = 'f.project_id = '.$gallery_id;

	$user = JFactory :: getUser();
	if ($user->id) {
		$where[] = '(p.gid = 0 OR p.gid IS NULL OR EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).')))';
	} else $where[] = '(p.gid = 0 OR p.gid IS NULL)';

	if ($images_id = $params->get('images_id')) {
		$images_id = explode(',', $images_id);
		JArrayHelper :: toInteger($images_id, 0);
		$where[] = 'f.id IN ('.implode(',', $images_id).')';
	}

/* collect ordering clause */
	switch ($params->get('order')) {
		case 'popular' : $order = 'f.hits DESC';
		break;
		case 'rand' : $order = 'RAND()';
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
	f.hits,
    p.user_id AS _user_id,
	p.name AS _gallery_name,
	CASE WHEN DATE_ADD(f.created, INTERVAL '.(int)$comp_params->get('new_days').' DAY) > NOW() THEN 1 ELSE 0 END AS _is_new,
	(SELECT u.name FROM #__users AS u WHERE u.id = f.user_id) AS _user_name
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
	if ($list = $db->loadObjectList()) {
		JHTML :: _('behavior.framework', true);
		JHTML :: script('components/com_fwgallery/assets/js/cerabox.min.js');
		JFHelper :: loadStylesheet();
		JHTML :: stylesheet('modules/mod_fwgallery_carousel/assets/css/styles.css');
		if ($lang->isRTL()) JHTML :: stylesheet('modules/mod_fwgallery_carousel/assets/css/style_rtl.css');

		$prefix = '';
		$height = $width = $total_height = $total_width = $total_qty = 0;
		switch ($params->get('image_size', 'th')) {
			case 'big' :
				$prefix = '';
				$height = $comp_params->get('im_max_h', 600) + 4;
				$width = $comp_params->get('im_max_w', 800) + 4;
				break;
			case 'mid' :
				$prefix = 'mid_';
				$height = $comp_params->get('im_mid_h', 255) + 4;
				$width = $comp_params->get('im_mid_w', 340) + 4;
				break;
			default :
				$prefix = 'th_';
				$height = $comp_params->get('im_th_h', 120) + 4;
				$width = $comp_params->get('im_th_w', 160) + 4;
		}
		foreach ($list as $i=>$row) if (file_exists(JPATH_SITE.'/images/com_fwgallery/files/'.$row->_user_id.'/'.$prefix.$row->filename)) {
			$total_width += $width + 6;
			$total_height += $height;
			$total_qty++;
		}
		$id = rand();
		$display_iconnew = $params->get('display_iconnew');
		$tmpl = '';
		$tmpl .= $params->get('scroll_buttons', '')?'scroll_':'';
		$tmpl .= $params->get('layout', 'horizontal');

		require(JModuleHelper::getLayoutPath('mod_fwgallery_carousel', $tmpl));
	}
}
?>