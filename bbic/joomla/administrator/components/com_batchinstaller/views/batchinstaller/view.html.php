<?php
/**
 * FW Installer 1.0.0 - Joomla! Property Manager
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

class batchinstallerViewbatchinstaller extends JViewLegacy {
	function display($tmpl=null) {
		$model = $this->getModel();
		$this->packages = $model->getPackages();
		parent :: display($tmpl);
	}
}