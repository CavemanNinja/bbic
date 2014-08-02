<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewImage extends JViewLegacy {
	function display($tmpl=null) {
		$model = $this->getModel();
		switch ($this->getLayout()) {
			case 'image' :
				if ($filename = $model->getImage() and $fp = fopen($filename, "rb")) {
					$info = getimagesize($filename);
					if (!headers_sent()) {
						header("Cache-Control: max-age=1209600");
						header("Content-type: {$info['mime']}");
					}
					fpassthru($fp);
					fclose($fp);
				}
			break;
		}
		die();
	}
}
