<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelImage extends JModelLegacy {
    function getUserState($name, $def='', $type='cmd') {
        $mainframe = JFactory :: getApplication();
        $context = 'com_fwgallery.image.'.(int)$this->getImageId().'.';
        return $mainframe->getUserStateFromRequest($context.$name, $name, $def, $type);
    }

    function getImageId() {
    	$id = (int)JRequest :: getInt('id');
    	if (!$id) {
			$menu = JMenu :: getInstance('site');
			if ($item = $menu->getActive()) {
	    		$id = $item->params->get('id');
			}
    	}
    	return $id;
    }

    function getObj($id = null) {
        $obj = $this->getTable('file');
        if (is_null($id)) {
        	$id = $this->getImageId();
        	$is_image_hit = true;
        } else $is_image_hit = false;
        if ($id and $obj->load($id)) {
        	if (!$obj->_is_gallery_published) $obj->id = 0;
        	elseif ($is_image_hit) $obj->hit();
        }
        return $obj;
    }

    function getNextImage($image) {
    	return JArrayHelper :: getValue($this->_getGalleryImages($image), 1);
    }
    function getPreviousImage($image) {
    	return JArrayHelper :: getValue($this->_getGalleryImages($image), 0);
    }
    function _getGalleryImages($current_image) {
    	static $list = null;
    	if (!$list) {
    		$galleryModel = JModelLegacy :: getInstance('gallery', 'fwGalleryModel');
			$params = JComponentHelper :: getParams('com_fwgallery');
    		$db = JFactory :: getDBO();
			$db->setQuery('
SELECT
	f.id,
	f.name,
    f.filename,
	t.name AS _type_name,
	t.plugin AS _plugin_name,
	CASE WHEN (SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) > 0 THEN (SELECT SUM(v.value) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0)))/(SELECT COUNT(*) FROM #__fwg_files_'.($params->get('voting_type')?'thumbs_':'').'vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) ELSE 0 END AS _rating_value,
	(SELECT user_id FROM #__fwg_projects AS p WHERE p.id = f.project_id) AS _user_id
FROM
    #__fwg_files AS f
	LEFT JOIN #__fwg_projects AS p ON p.id = f.project_id
	LEFT JOIN #__fwg_types AS t ON t.id = f.type_id
WHERE
	p.published = 1
	AND
    f.published = 1
    AND
    f.project_id = '.(int)$current_image->project_id.'
'.$galleryModel->_getOrderClause());
			$list = array();
    		if ($images = $db->loadObjectList()) {
    			$qty = count($images);
    			if ($qty > 1) {
	    			/* look for current image position */
	    			foreach ($images as $pos => $image) {
	    				if ($image->id == $current_image->id) {
	    					if (!$pos) {
	    						$list[] = $images[$qty - 1];
	    						if ($qty > 1) $list[] = $images[1];
	    					} elseif ($pos == $qty - 1) {
	    						if ($qty > 1) $list[] = $images[$pos - 1];
	    						$list[] = $images[0];
	    					} else {
	    						$list[] = $images[$pos - 1];
	    						$list[] = $images[$pos + 1];
	    					}
							break;
	    				}
	    			}
    			}
    		}
    	}
    	return $list;
    }
    function loadCategoriesPath($category_id = 0) {
    	$model = JModelLegacy :: getInstance('gallery', 'fwGalleryModel');
    	return $model->loadCategoriesPath($category_id);
    }
	function getPlugin() {
		if ($plugin = JRequest :: getString('plugin'))
			return JPluginHelper :: getPlugin('fwgallery', $plugin);
	}
	function processPlugin() {
		if ($plugin = $this->getPlugin() and JPluginHelper :: importPlugin('fwgallery', $plugin->name)) {
/*			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger('fwProcess');*/
		}
	}
	function save() {
		$user = JFactory :: getUser();
		if (!$user->id) {
			$app = JFactory :: getApplication();
			$app->login(JRequest :: get(), array(
				'silent' => true
			));
		}
		$user = JFactory :: getUser();
		if ($user->id) {
	        $image = $this->getTable('file');
	        if ($id = JRequest::getInt('id') and !$image->load($id)) JRequest :: setVar('id', 0);

	        if ($image->bind(JRequest::get('default', JREQUEST_ALLOWHTML)) and $image->check() and $image->store()) {
	            $this->setError('FWG_STORED_SUCCESSFULLY');
	            return $image->id;
	        } else
	        	$this->setError($image->getError());
		} else
			$this->setError('FWG_CANT_LOGIN');
	}
	function delete() {
		$user = JFactory :: getUser();
		if (!$user->id) {
			$app = JFactory :: getApplication();
			$app->login(JRequest :: get(), array(
				'silent' => true
			));
		}
		$user = JFactory :: getUser();
		$result = false;
		if ($user->id) {
			$id = JRequest :: getInt('id');
			$advanced_user = $user->authorise('core.login.admin')?1:0;
			$image = $this->getTable('file');
			if ($image->load($id)) {
				if ($advanced_user) {
					$result = $image->delete($id);
					$this->setError($result?'FWG_SUCCESS':$image->getError());
				} else {
					if ($image->user_id == $user->id) {
						$result = $image->delete($id);
						$this->setError($result?'FWG_SUCCESS':$image->getError());
					} else $this->setError('FWG_NOT_YOURS');
				}
			} else $this->setError('FWG_NOT_FOUND');
		} else $this->setError('FWG_CANT_LOGIN');
		return $result;
	}
	function testGallery() {
		$result = new stdclass;
		$filename = JPATH_ADMINISTRATOR.'/components/com_fwgallery/fwgallery.xml';
		if (file_exists($filename) and $buff = file_get_contents($filename)) {
			if (preg_match('#<version>([^<]*)</version>#', $buff, $match)) {
				$result->code = 2;
				$result->version = $match[1];
			} else {
				$result->code = 1;
				$result->msg = JText :: _('FW Gallery version not found');
			}
		} else {
			$result->code = 0;
			$result->msg = JText :: _('FW Gallery config file not found');
		}

		return $result;
	}
	function testUser() {
		$result = new stdclass;
		$result->code = 0;
		$result->advanced_user = 0;
		$result->msg = '';
		if ($pass = JRequest :: getString('password')) {
			if ($username = JRequest :: getString('username')) {
				$result->code = 2;
				$db = JFactory :: getDBO();
				$db->setQuery('SELECT `id`, `password` FROM #__users WHERE username = '.$db->quote($username));
				if ($obj = $db->loadObject()) {
					jimport('joomla.user.helper');
					if (JUserHelper::verifyPassword($pass, $obj->password)) {
						$result->code = 5;
						$user = JFactory :: getUser($obj->id);
						$result->advanced_user = $user->authorise('core.login.admin')?1:0;
						$result->msg = JText :: _('User ok');
					} else {
						$result->code = 4;
						$result->msg = JText :: _('Password do not match');
					}
				} else {
					$result->code = 3;
					$result->msg = JText :: _('User not found');
				}
			} else {
				$result->code = 1;
				$result->msg = JText :: _('Username not passed');
			}
		} else {
			$result->code = 0;
			$result->msg = JText :: _('Password not passed');
		}
		return $result;
	}
	function getPlugins() {
		$user = JFactory :: getUser();
		if (!$user->id) {
			$app = JFactory :: getApplication();
			$app->login(JRequest :: get(), array(
				'silent' => true
			));
		}
		$user = JFactory :: getUser();
		$result = false;
		if ($user->id) {
			$advanced_user = $user->authorise('core.login.admin')?1:0;
			if ($advanced_user) {
				$db = JFactory :: getDBO();
				$db->setQuery('SELECT \'\' AS name, element, folder AS `type`, enabled AS published, \'\' AS `version` FROM #__extensions WHERE `type` = \'plugin\' AND (`folder` = \'fwgallery\' OR `name` LIKE \'%FW Gallery%\') ORDER BY ordering');
				if ($plugins = $db->loadObjectList()) foreach ($plugins as $i=>$plugin) {
					$filename = JPATH_PLUGINS.'/'.$plugin->type.'/'.$plugin->element.'/'.$plugin->element.'.xml';
					if (file_exists($filename)) {
						$file = file_get_contents($filename);
						if (preg_match('#<name>(.*?)</name>#i', $file, $m)) $plugins[$i]->name = $m[1];
						if (preg_match('#<version>(.*?)</version>#i', $file, $m)) $plugins[$i]->version = $m[1];
					}
					return $plugins;
				}
			} else $this->setError('FWG_NO_ACCESS');
		} else $this->setError('FWG_CANT_LOGIN');
		return $result;
	}
	function buy() {
		if ($file_stock_id = JRequest :: getInt('file_stock_id')) {
			$db = JFactory :: getDBO();
			$db->setQuery('SELECT fp.*, f.name FROM #__fwg_file_prices AS fp, #__fwg_files AS f WHERE f.id = fp.file_id AND fp.id = '.(int)$file_stock_id);
			if ($obj = $db->loadObject()) {
				$params = JComponentHelper :: getParams('com_fwgallery');
				if ($email = $params->get('paypal_email')) {
					$curr = $params->get('paypal_currency', 'USD');
					if (!$curr) $curr = 'USD';
					$descr = 'Image %s, ID: %s, width: %spx, height: %spx';

					$link = 'https://www.'.(($params->get('paypal_mode'))?'':'sandbox.').'paypal.com/cgi-bin/webscr';
					$link .= '?cmd=_xclick&no_shipping=1&no_note=1&tax=0&currency_code='.$curr.'&charset=UTF%2d8&amount='.$obj->price;
					$link .= '&business='.urlencode($email).'&item_name='.urlencode(sprintf($descr, $obj->name, $obj->file_id, $obj->width, $obj->height));
					return $link;
				} else $this->setError(JText :: _('FWG_PAYPAL_EMAIL_NOT_SET'));
			} else $this->setError(JText :: _('FWG_IMAGE_NOT_FOUND'));
		} else $this->setError(JText :: _('FWG_NO_IMAGE_PRICE_ID'));
	}
	function getImage() {
		if ($id = (int)JRequest :: getInt('id')) {
			$t_path = JPATH_CACHE.'/fwgallery/images/';
			$height = (int)JRequest :: getInt('h');
			$width = (int)JRequest :: getInt('w');
			$hash = $id.'_'.md5($id.',height:'.$height.',width:'.$width);
			if (!file_exists($t_path.$hash)) {
				$filename = '';
				$s_path = JPATH_SITE.'/images/com_fwgallery/files/';

				$db = JFactory :: getDBO();
				$db->setQuery('SELECT (SELECT p.user_id FROM #__fwg_projects AS p WHERE p.id = f.project_id) AS user_id, filename FROM #__fwg_files AS f WHERE id = '.$id);
				if ($obj = $db->loadObject()) {
					$s_path .= $obj->user_id.'/';
					$filename = $obj->filename;
				}

				if (!($filename and file_exists($s_path.$filename))) {
					$s_path = JPATH_SITE.'/components/com_fwgallery/assets/images/';
					$filename = 'no_image.jpg';
				}

				if ($filename and file_exists($s_path.$filename)) {
					jimport('joomla.filesystem.file');
					if (!file_exists($t_path)) JFile :: write($t_path.'index.html', $buff = '<html><head><title></title></head><body></body></html>');
					$t_filename = $hash;

					if (!file_exists($t_path.$t_filename)) {

						if (!$height and !$width) {
							JFile :: copy($s_path.$filename, $t_path.$t_filename);
						} else {
							$ext = strtolower(JFile :: getExt($filename));
							$image = null;
							switch ($ext) {
								case 'gif' : $image = imagecreatefromgif($s_path.$filename);
								break;
								case 'jpeg' :
								case 'jpg' : $image = imagecreatefromjpeg($s_path.$filename);
								break;
								case 'png' : $image = imagecreatefrompng($s_path.$filename);
								break;
							}

							if ($image) {
								$org_height = imagesy($image);
								$org_width = imagesx($image);

								if (!$width) $width = $height * $org_width / $org_height;
								elseif (!$height) $height = $width * $org_height / $org_width;
								$trg_ratio = $width/$height;
								$org_ratio = $org_width/$org_height;
								if (JRequest :: getInt('js')) {
									$x_offset = 0;
									$y_offset = 0;
									$s_width = $org_width;
									$s_height = $org_height;

									if ($trg_ratio < $org_ratio) { /* width larger, so srink by width */
										$height = round($width / $org_ratio);
									} else { /* height larger or eq */
										$width = round($height * $org_ratio);
									}
								} else {
									if ($org_ratio < $trg_ratio) { /*cut vertical top & shrink */
										$s_width = $org_width;
										$s_height = (int)($org_width / $trg_ratio);

										$x_offset = 0;
										$y_offset = (int)(($org_height-$s_height)/5);
									} elseif ($org_ratio > $trg_ratio) { /* cut horisontal middle & shrink */
										$s_width = (int)($org_height * $trg_ratio);
										$s_height = $org_height;

										$x_offset = (int)(($org_width-$s_width)/2);
										$y_offset = 0;
									} else { /* images fully proportional - just shrink */
										$s_width = $org_width;
										$s_height = $org_height;

										$x_offset = 0;
										$y_offset = 0;
									}
								}
								$dst = imagecreatetruecolor($width, $height);

								$ct = imagecolortransparent($image);
								if ($ct >= 0) {
									$color_tran = @imagecolorsforindex($image, $ct);
									$ct2 = imagecolorexact($dst, $color_tran['red'], $color_tran['green'], $color_tran['blue']);
									imagefill($dst, 0, 0, $ct2);
								}

								imagecopyresampled(
									$dst,
									$image,
									0,
									0,
									$x_offset,
									$y_offset,
									$width,
									$height,
									$s_width,
									$s_height
								);

								ob_start();
								switch ($ext) {
									case 'gif' : imagegif($dst);
									break;
									case 'jpeg' :
									case 'jpg' : imagejpeg($dst);
									break;
									case 'png' : imagepng($dst);
									break;
								}
								imagedestroy($image);
								imagedestroy($dst);
								unset($image);
								unset($dst);
								JFile :: write($t_path.$t_filename, $buff = ob_get_clean());
							}
						}
						/* watermark */
						$params = JComponentHelper :: getParams('com_fwgallery');
						$wmfile = null;
						if ((!$height or $height > 400 or !$width or $width > 300) and $params->get('use_watermark') and file_exists($t_path.$t_filename)) {
							if ($watermark = $params->get('watermark_file') and $path = FWG_STORAGE_PATH.$watermark and file_exists($path)) {
								switch (JFile :: getExt($watermark)) {
									case 'png' :
										$wmfile = imagecreatefrompng($path);
									break;
									case 'gif' :
										$wmfile = imagecreatefromgif($path);
									break;

								}
							}
						} elseif ($watermark_text = $params->get('watermark_text')) {
							/* calculate text size */
							$font_path = JPATH_SITE.'/components/com_fwgallery/assets/fonts/chesterfield.ttf';

							$bbox = imagettfbbox(36, 0, $font_path, $watermark_text);
							if ($bbox[0] < -1) $box_width = abs($bbox[2]) + abs($bbox[0]) - 1;
							else $box_width = abs($bbox[2] - $bbox[0]);
							if ($bbox[3] > 0) $box_height = abs($bbox[7] - $bbox[1]) - 1;
							else $box_height = abs($bbox[7]) - abs($bbox[1]);

							if ($wmfile = imagecreatetruecolor($box_width, $box_height + 2)) {
								$colorTransparent = imagecolortransparent($wmfile);
								imagefill($wmfile, 0, 0, $colorTransparent);

								$black = imagecolorallocate($wmfile, 0, 0, 0);
								imagettftext($wmfile, 36, 0, 0, abs($bbox[7]), $black, $font_path, $watermark_text);
							}
						}
						if ($wmfile) {
							$ext = strtolower(JFile :: getExt($filename));
							$image = null;
							switch ($ext) {
								case 'gif' : $image = imagecreatefromgif($t_path.$t_filename);
								break;
								case 'jpeg' :
								case 'jpg' : $image = imagecreatefromjpeg($t_path.$t_filename);
								break;
								case 'png' : $image = imagecreatefrompng($t_path.$t_filename);
								break;
							}
							if ($image) {
								$watermark_width = imagesx($wmfile);
								$watermark_height = imagesy($wmfile);
								$watermark_pos = $params->get('watermark_position', 'left_bottom');

								$org_height = imagesy($image);
								$org_width = imagesx($image);

								if (!$width and !$height) {
									$width = $org_width;
									$height = $org_height;
								} elseif (!$width) $width = $height * $org_width / $org_height;
								elseif (!$height) $height = $width * $org_height / $org_width;

								$dest_x = $dest_y = 0;

								switch ($watermark_pos) {
									case  'center' :
										$dest_x = round(($width - $watermark_width)/2);
										$dest_y = round(($height - $watermark_height)/2);
									break;
									case  'left_top' :
										$dest_x = 5;
										$dest_y = 5;
									break;
									case  'right_top' :
										$dest_x = $width - $watermark_width - 5;
										$dest_y = 5;
									break;
									case  'left_bottom' :
										$dest_x = 5;
										$dest_y = $height - $watermark_height - 5;
									break;
									default :
										$dest_x = $width - $watermark_width - 5;
										$dest_y = $height - $watermark_height - 5;
								}
								imagecopy($image, $wmfile, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);

								ob_start();
								switch ($ext) {
									case 'gif' : imagegif($image);
									break;
									case 'jpeg' :
									case 'jpg' : imagejpeg($image);
									break;
									case 'png' : imagepng($image);
									break;
								}
								JFile :: write($t_path.$t_filename, $buff = ob_get_clean());

								imagedestroy($image);
								unset($image);
							}
							imagedestroy($wmfile);
							unset($wmfile);
						}
					}
				}
			}
			if (file_exists($t_path.$hash)) return $t_path.$hash;
		}
	}
}
