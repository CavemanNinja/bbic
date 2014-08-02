<?php
/**
 * FW Gallery Cascading Grid Module 1.0.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML :: stylesheet('components/com_fwgallery/assets/css/style.css');
JHTML :: stylesheet('modules/mod_fwgallery_cascading_grid/assets/css/styles.css');
JTable :: addIncludePath(JPATH_ADMINISTRATOR.'/components/com_fwgallery/tables/');
JModelLegacy :: addIncludePath(JPATH_SITE.'/components/com_fwgallery/models/');
if ($model = JModelLegacy :: getInstance('Grid', 'fwGalleryModel')) {
	require_once(JPATH_SITE.'/components/com_fwgallery/helpers/helper.php');
	$com_language = JFactory :: getLanguage();
	$com_language->load('com_fwgallery');

	$category_id = (int)$params->get('id');
	$load_subgalleries = (int)$params->get('load_subgalleries');
	
	$category = $model->getObj($category_id, $load_subgalleries);

	$subcategories = $load_subgalleries?$model->loadSubCategories($category_id, $load_subgalleries):array();

	$list = $model->getList($category_id, $load_subgalleries);
	$total = $model->getQty($category_id, $load_subgalleries);
	require(JModuleHelper::getLayoutPath('mod_fwgallery_cascading_grid', $tmpl));
}
