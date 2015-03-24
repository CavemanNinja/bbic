<?php 
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/
defined('_JEXEC') or die('Restricted Access');

$result_install = $this->result_install;
if($result_install){ ?>00allokmess00
<h3><?php echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_SUCCESSFULLYUPLOADED'); ?></h3>
<?php } else {  ?>
<br />
<div align="justify" style="font-size: 10px;"><?php echo  JText::_('COM_TWOJTOOLBOX_MSG_INSTALL_ERROR');  ?></div>
 <?php } ?>