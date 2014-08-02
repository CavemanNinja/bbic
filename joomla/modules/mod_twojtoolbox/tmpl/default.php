<?php
/**
* @package     2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/


defined('_JEXEC') or die;
if( !$params->get('twojInclude', 0) ) echo '<div class="moduletable'.$moduleclass_sfx.'">';
echo $module_html; 
if( !$params->get('twojInclude', 0) ) echo '</div>';?>