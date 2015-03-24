<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.11 $
**/

defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js',false,true);

if(TJTB_JVERSION==3){
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	//JHtml::_('formbehavior.chosen', 'select');
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=scan'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="twoj_formEditItem">
		<div style="display: inline-block;"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_PATH_LABEL'); ?>:&nbsp;&nbsp;&nbsp;
			<span id="twoj_scan_rootFolder">{JOOMLA ROOT}</span>
			&nbsp;/&nbsp;
			<span id="twoj_scan_folderLabel"></span>
		</div> 
		<div class="twoj_clear"> </div>
		<br />
		<legend><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_FOLDER_LIST'); ?></legend>
		
		<input type="hidden" id="twoj_scan_folderList_default" name="twoj_scan_folderList_default" value="<?php echo $this->path; ?>" />
		<div id="twoj_scan_folderList"></div>
		<br />
		<legend><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_IMAGES_LIST'); ?></legend>
		<div id="twoj_scan_imagesList_no"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_NOIMAGES'); ?></div>
		<table class="adminlist table table-striped twoj_hiddenblock" id="twoj_scan_imagesList">
			<thead>
				<tr>
					<th width="160" class="twoj_center twoj_nowrap"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_HEADING_IMAGES'); ?></th>
					<th width="70" class="twoj_center"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_HEADING_IMAGES_RES'); ?></th>	
					<th width="20" class="twoj_center">
						<input id="twoj_scan_checkall" type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" />
					</th>	
					<th width="60"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_HEADING_TITLE'); ?></th>				
					<th><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_HEADING_DESC'); ?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<div class="twoj_clear"> </div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_category_id" value="<?php echo TwojToolboxHelper::cgid(); ?>" />
		
		<?php echo JHtml::_('form.token'); ?>
	</div>
	
	
</form>
<?php echo TwojToolboxHTMLHelper::getVersion(); ?>

<div id="twojtoolbox_general_options" class="twoj_hiddenblock">
<?php 
	foreach($this->form->getFieldset('left') as $field): 
		echo '<div class="twoj_control-group twoj_clear_left">
			<div class="twoj_control-label">'.$field->label.'</div><div class="twoj_controls">'.$field->input.'<div class="twoj_clear"></div></div>
		</div>';
	endforeach;
	foreach($this->form->getFieldset('right') as $field): 
		echo '<div class="twoj_control-group twoj_clear_left">
			<div class="twoj_control-label">'.$field->label.'</div><div class="twoj_controls">'.$field->input.'<div class="twoj_clear"></div></div>
		</div>';
	endforeach; ?>
</div>
<div id="twojtoolbox_scan_saveimages_dialog" class="twoj_hiddenblock">
	<div id="twoj_scan_saveimages_status_ok" class="twoj_scan_saveimages_status twoj_hiddenblock"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_SAVEIMAGES_STATUS_OK'); ?></div>
	<div id="twoj_scan_saveimages_status_error" class="twoj_scan_saveimages_status twoj_hiddenblock"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_SAVEIMAGES_STATUS_ERROR'); ?></div>
	<div id="twoj_scan_saveimages_status_process" class="twoj_scan_saveimages_status twoj_hiddenblock"><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_SAVEIMAGES_STATUS_PROCESS'); ?></div>
	<div id="progress_bar" class="ui-progress-bar ui-container">
		 <div class="ui-progress" style="width: 0%;"><span class="ui-label"></div>
	</div>
	<br />
	<div><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_SAVEIMAGES_LOG_TITLE'); ?></div>
	<div id="twoj_imagescopy_log"></div>
	<div><?php echo JText::_('COM_TWOJTOOLBOX_SCAN_SAVEIMAGES_COUNT'); ?><span id="twoj_imagescopy_count"></span></div>
</div>