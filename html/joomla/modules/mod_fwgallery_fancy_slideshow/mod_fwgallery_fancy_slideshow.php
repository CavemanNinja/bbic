<?php
/**
 * FW Gallery Fancy Slideshow 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

$path = JPATH_SITE.'/components/com_fwgallery/';
if (file_exists($path)) {
	JHTML :: stylesheet('modules/mod_fwgallery_fancy_slideshow/assets/css/styles.css');
	include_once($path.'helpers/helper.php');
	jimport('joomla.application.component.model');
	JModelLegacy :: addIncludePath($path.'models');
	if ($model = JModelLegacy :: getInstance('Gallery', 'fwGalleryModel')) {
		if (method_exists($model, 'loadModuleData')) {
			if($list = $model->loadModuleData($params)) {
				if (!$params->get('limit')) $params->set('limit', count($list));
				$comp_params = JComponentHelper :: getParams('com_fwgallery');
				switch ($params->get('image_size', 'th')) {
					case 'big' :
						$prefix = '';
						$height = $comp_params->get('im_max_h', 600);
						$width = $comp_params->get('im_max_w', 800);
						break;
					case 'mid' :
						$prefix = 'mid_';
						$height = $comp_params->get('im_mid_h', 240);
						$width = $comp_params->get('im_mid_w', 320);
						break;
					default :
						$prefix = 'th_';
						$height = $comp_params->get('im_th_h', 120);
						$width = $comp_params->get('im_th_w', 160);
				}
				$id = rand();
				$pause = $params->get('pause', 3) * 1000;
				if (!$pause) $pause = 2000;
				require(JModuleHelper::getLayoutPath('mod_fwgallery_fancy_slideshow'));
			}
		} else echo JText :: _('To get working this module you need to update a FWGallery component');
	}
}
?>