<?php
/**
 * FW Installer 1.0.0 - Joomla! Property Manager
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.'/controller.php');

$controller = JControllerLegacy :: getInstance('batchinstaller');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
