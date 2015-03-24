<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewGrid extends fwGalleryView {
    function display($tmpl=null) {
        $model = $this->getModel();

		$category_id = (int)JRequest :: getInt('id');
		$load_subgalleries = JRequest :: getInt('load_subgalleries');
		$category = $model->getObj($category_id);
		$this->setLayout('default_item');
		$this->assign('params',  JComponentHelper :: getParams('com_fwgallery'));
		$this->assign('mod_params', $model->loadModuleParams());

        $list = array(
			'descr' => ($this->mod_params->get('display_description') and $category->descr)?$category->descr:'',
			'items_total' => $model->getQty($category_id, $load_subgalleries),
			'styles' => array(),
        	'images' => array()
        );
		$this->schemes = array(
			array(2,1,1,1,1),
			array(1,1,1,1,2),
			array(1,1,2,1,1),
//			array(1,1,1,1,1),
//			array(2,2)
		);
		$this->current_scheme = rand(0, count($this->schemes) - 1);
		$this->current_position = 0;
		$width = $this->mod_params->get('width');
		if (!$width) $width = 200;

		$this->counter = 0;
        if ($images = (array)$model->getList($category_id, $load_subgalleries)) foreach ($images as $i=>$row) {
			if ($this->current_position == count($this->schemes[$this->current_scheme])) {
				$this->current_scheme = rand(0, count($this->schemes) - 1);
				$this->current_position = 0;
			}
			$this->row = $row;
			$this->height = $width * $this->schemes[$this->current_scheme][$this->current_position] + ($this->schemes[$this->current_scheme][$this->current_position] - 1) * 4;
			$this->width = $width * $this->schemes[$this->current_scheme][$this->current_position] + ($this->schemes[$this->current_scheme][$this->current_position] - 1) * 10;

			$list['styles'][] = 'width:'.$this->width.'px;height:'.$this->height.'px;';
			$list['images'][] = $this->loadTemplate();
			$this->counter++;
			$this->current_position++;
        }
        echo json_encode($list);
        die();
    }
}
