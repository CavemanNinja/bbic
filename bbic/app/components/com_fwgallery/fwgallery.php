<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML::addIncludePath(JPATH_COMPONENT_SITE.'/helpers');
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');

require_once(JPATH_COMPONENT.'/helpers/helper.php');
require_once(JPATH_COMPONENT.'/controller.php');

JFHelper :: loadStylesheet();
$controller = JControllerLegacy :: getInstance('fwGallery');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
