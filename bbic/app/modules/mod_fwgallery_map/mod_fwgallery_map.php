<?php
/**
 * FW Gallery Map Module 1.0.0
 * @copyright (C) 2013 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

$path = JPATH_SITE.'/components/com_fwgallery/helpers/helper.php';
if (file_exists($path)) {
	require_once($path);

	$where = array(
		'f.published = 1',
		'f.project_id = p.id',
		'p.published = 1',
		'f.latitude <> 0',
		'f.longitude <> 0',
		'f.latitude IS NOT NULL',
		'f.longitude IS NOT NULL'
	);
	$db = JFactory::getDBO();
	$db->setQuery('
SELECT
    f.id,
	f.latitude,
	f.longitude,
	f.name,
	p.user_id AS _user_id
FROM
	#__fwg_files AS f,
	#__fwg_projects AS p
WHERE '.implode(' AND ', $where)
	);
	if ($list = $db->loadObjectList()) {
		JHTML :: stylesheet('modules/mod_fwgallery_map/assets/css/styles.css');
		$width = $params->get('width', 400);
		$height = $params->get('height', 300);
		require(JModuleHelper::getLayoutPath('mod_fwgallery_map'));
	}
}
?>