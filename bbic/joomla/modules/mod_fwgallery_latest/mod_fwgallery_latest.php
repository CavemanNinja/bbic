<?php
/**
 * FW Gallery Latest 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

$path = JPATH_SITE.'/components/com_fwgallery/';
if (file_exists($path)) {
	include_once($path.'helpers/helper.php');
	JFHelper :: loadStylesheet();
	JHTML :: stylesheet('modules/mod_fwgallery_latest/assets/css/styles.css');
	jimport('joomla.application.component.model');
	JModelLegacy :: addIncludePath($path.'models');
	if ($model = JModelLegacy :: getInstance('Gallery', 'fwGalleryModel')) {
		if (method_exists($model, 'loadModuleData')) {
			if($list = $model->loadModuleData($params)) {
				$prefix = $params->get('image_size', 'th');
				if ($prefix == 'big') $prefix = ''; else $prefix .= '_';
				require(JModuleHelper::getLayoutPath('mod_fwgallery_latest'));
			}
		} else echo JText :: _('To get working this module you need to update a FWGallery component');
	}
}


?>