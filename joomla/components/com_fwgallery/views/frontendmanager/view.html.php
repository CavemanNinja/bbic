<?php
/**
 * FW Gallery Frontend Manager 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewFrontendManager extends JViewLegacy {
    function display($tmpl=null) {
        $model = $this->getModel();

        $this->assign('user', JFactory :: getUser());
		if (!$this->user->id) {
			$app = JFactory :: getApplication();
			$uri = JURI :: getInstance();
			$app->redirect(JRoute :: _('index.php?option=com_users&view=login&return='.base64_encode($uri->toString()), false), JText :: _('Login first please'));
		}

		JHTML :: addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers');
		JHTML :: stylesheet('components/com_fwgallery/assets/css/styles.css');

		$data = explode('_', $this->getLayout());
		$layout = $data[0];
		$type = JArrayHelper :: getValue($data, 1);

		if ($layout != 'edit') $layout = 'default';
		if ($type != 'image') $type = 'gallery';

		$this->assign('params', JComponentHelper :: getParams('com_fwgallery'));
		if (!$this->params->get('allow_frontend_galleries_management')) $type = 'image';
		elseif ($type != 'image') $type = 'gallery';

		$this->assign('type', $type);
		$this->assign('layout', $layout);
		$this->setLayout('default');

		if ($layout == 'default') {
			/* view lists */
			if ($type == 'image') {
		        $this->assign('types', JFHelper :: loadTypes($published_only = true));
		        $this->assign('search', $model->getUserState('search', '', 'string'));
		        $this->assign('project_id', $model->getUserState('project_id'));
		        $this->assign('list', $model->getImages());
		        $this->assign('pagination', $model->getImagesPagination());

		        $this->assign('plugins', $model->getImagesViewPlugins());
			} else {
		        $this->assign('list', $model->getGalleriesProjects());
		        $this->assign('pagination', $model->getGalleriesPagination());
			}
		} else {
			/* view edit form */
			if ($type == 'image') {
		        $this->assign('types', JFHelper :: loadTypes($published_only = true));
		        $this->assign('projects', $model->getCategories());
		        if (!$this->projects) {
					$app = JFactory :: getApplication();
					$app->redirect(JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager', false), JText :: _('Create gallery first'));
		        }
				$this->assign('media', $model->getMedias());
		        $this->assign('obj', $model->getFile());
			} else {
		        $this->assign('obj', $model->getProject());
		        $this->assign('projects', $model->getCategories($this->obj->id));
		        $this->assign('groups', JFHelper :: getGroups());
			}
		}

        parent::display($tmpl);
    }
}
