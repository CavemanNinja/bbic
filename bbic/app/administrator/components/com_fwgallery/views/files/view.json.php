<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewFiles extends JViewLegacy {
    function display($tmpl=null) {
        $model = $this->getModel();
		
		switch ($this->getLayout()) {
			case 'batchupload' :
				$data = new stdclass;
				$data->inserted = $model->install();
				$data->msg = $model->getError();
				echo json_encode($data);
			break;
			default:
				JRequest :: setVar('limit', '0');
				JRequest :: setVar('limitstart', '0');
				$list = array();
				if ($data = $model->getFiles()) foreach ($data as $row) {
					$buff = new stdclass;
					$buff->id = $row->id;
					$buff->name = $row->name;
					$buff->created = $row->created;
					$buff->link = JURI :: root(false).JFHelper::getFileFilename($row);
					$buff->color = JFHelper :: getGalleryColor($row->project_id);
					$buff->_user_name = $row->_user_name;

					$list[] = $buff;
				}
				echo json_encode($list);
		}
        die();
    }
}
