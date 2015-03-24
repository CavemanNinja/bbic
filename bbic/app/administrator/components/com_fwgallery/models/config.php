<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2011 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelConfig extends JModelLegacy {
    function loadObj() {
    	$obj = new stdclass;
    	$obj->params = JComponentHelper :: getParams('com_fwgallery');
        return $obj;
    }

    function save() {
    	$params = JComponentHelper :: getParams('com_fwgallery');
		$data = (array)JArrayHelper :: getValue($_POST, 'config');

    	$fields = array(
			'use_own_bootstrap',
			'enable_stock',
			'allow_frontend_galleries_management',
			'currency_position',
			'paypal_mode',
			'voting_type',
			'im_just_shrink',
			'use_watermark',
			'use_voting',
			'public_voting',
			'display_total_galleries',
			'display_owner_gallery',
			'display_owner_image',
			'display_date_gallery',
			'display_gallery_sorting',
			'display_name_gallery',
			'display_name_image',
			'display_date_image',
			'display_image_views',
			'allow_image_download',
			'allow_print_button',
			'hide_bottom_image',
			'display_user_copyright',
			'hide_mignifier',
			'hide_single_image_view',
			'hide_iphone_app_promo',
			'hide_fw_copyright',
			'display_galleries_lightbox',
			'display_social_sharing',
			'display_image_tags'
		);
		foreach ($fields as $field) $data[$field] = JRequest :: getVar($field);

	   	$params->loadArray($data);
		$cache = JFactory :: getCache('_system', 'callback');
    	$cache->clean();

    	$params->set('gallery_color', trim($params->get('gallery_color'), '#'));

		if (JRequest :: getInt('delete_watermark') and $filename = $params->get('watermark_file')) {
			if (file_exists(FWG_STORAGE_PATH.$filename)) @unlink(FWG_STORAGE_PATH.$filename);
			$params->set('watermark_file', '');
		}
		
    	if ($file = JRequest :: getVar('watermark_file', '', 'files')
    	 and $name = JArrayHelper :: getValue($file, 'name')
    	  and !JArrayHelper :: getValue($file, 'error')
    	   and preg_match('/\.png|gif$/i', $name)
    	   	and move_uploaded_file(JArrayHelper :: getValue($file, 'tmp_name'), FWG_STORAGE_PATH.$name)) {
    		$params->set('watermark_file', $name);
    	}

    	$db = JFactory :: getDBO();
    	$db->setQuery('UPDATE #__extensions SET params = '.$db->quote($params->toString()).' WHERE `element` = \'com_fwgallery\' AND `type` = \'component\'');
    	return $db->query();
    }
	function loadImages() {
		$db = JFactory :: getDBO();
		$db->setQuery('SELECT f.id, p.user_id, f.filename, f.original_filename, p.name FROM #__fwg_projects AS p, #__fwg_files AS f WHERE f.project_id = p.id AND filename <> \'\'');
		return $db->loadObjectList();
	}
	function resize($list) {
		$qty_images = count($list);
		$next_id = 0;
		$last_id = JRequest :: getInt('last_id');
		$msg = JText :: _('FWG_NOTHING', true);
		$pos = 1;
		if ($list) {
			jimport('joomla.filesystem.file');
			$params = JComponentHelper :: getParams('com_fwgallery');
			foreach ($list as $i=>$image) {
				if ($image->id == $last_id) {
					$path = JPATH_SITE.'/images/com_fwgallery/files/'.$image->user_id.'/';
					if ($gd_image = GPMiniImg::loadImage($path, $image->original_filename)) {
						GPMiniImg::reduceImage($gd_image, $path, $image->filename, $params->get('im_max_w',800), $params->get('im_max_h',600));
						GPMiniImg::makeThumb($image->filename, $path, 'mid_', $params->get('im_mid_w',340), $params->get('im_mid_h',255), $params->get('im_just_shrink'));
						GPMiniImg::makeThumb($image->filename, $path, 'th_', $params->get('im_th_w',160), $params->get('im_th_h',120), $params->get('im_just_shrink'));
						$msg = JText :: _('FWG_OK', true);
					} else $msg = JText :: _('FWG_NOT_FOUND', true);

					if ($i + 1 != $qty_images) {
						$next_id = $list[$i+1]->id;
					}
					break;
				}
				$pos++;
			}
		}
		return (object)array('next_id'=>$next_id, 'pos'=>$pos);
	}
}
