<?php
/**
 * FW Gallery 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewGalleries extends fwGalleryView {
    function display($tmpl=null) {
        $model = $this->getModel();
        $user = JFactory::getUser();

        $this->assign('obj', $model->getObj());
        $this->assign('own', (bool)$user->id and ($user->id == $this->obj->id));
        $this->assign('list', $model->getList());
        $this->assign('pagination', $model->getPagination());
        $this->assign('title', $model->getTitle());
		$this->assign('params',  JComponentHelper :: getParams('com_fwgallery'));
        $this->assign('order', $model->getUserState('order', $this->params->get('ordering_galleries')));

		$galleries_a_row = $this->params->get('galleries_a_row', 3);
		if (!$galleries_a_row) $galleries_a_row = 3;
        $this->columns = (int)(12 /$galleries_a_row);

        if ($this->obj->id) {
            /* set correct breadcrump */
            $app = JFactory::getApplication();
            $pathway = $app->getPathway();
            $pathway->addItem('Galleries');
        }

        parent::display($tmpl);
    }
}
