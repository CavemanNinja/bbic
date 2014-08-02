<?php
/**
 * FW Installer 1.0.0 - Joomla! Property Manager
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

class batchinstallerController extends JControllerLegacy {
    function install() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
		$msg = 'no install method';
        if (method_exists($model, 'install')) {
            $model->install();
        }
        $this->setRedirect('index.php?option=com_batchinstaller&view='.$view_name);
    }
}