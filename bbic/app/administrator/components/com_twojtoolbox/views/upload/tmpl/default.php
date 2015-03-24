<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.10 $
**/

defined('_JEXEC') or die('Restricted access'); 
JHtml::_('behavior.tooltip');
if(TJTB_JVERSION==3){
	JHtml::_('behavior.multiselect');
	JHtml::_('dropdown.init');
	//JHtml::_('formbehavior.chosen', 'select');
}
?>
<form  enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=upload');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="twoj_formEditItem">
		<table class="adminlist table table-striped" id="articleList">
			<thead>
				<tr>
					<th width="160"><?php echo JText::_('COM_TWOJTOOLBOX_UPLOAD_SELECTFILE'); ?></th>
					<th width="310"><?php echo JText::_('COM_TWOJTOOLBOX_UPLOAD_INPUTNAME'); ?></th>
					<th><?php echo JText::_('COM_TWOJTOOLBOX_UPLOAD_INPUTDESC'); ?></th>
				</tr>
			</thead>
			<tbody id="twojtoolbox_body">
				<tr class="row0 twojtoolbox_lastadded">
					<td><input type="file" name="filename_up[0]" size="40" class="inputbox"></td>
					<td><input type="text" name="filename[0]" size="40" class="inputbox"></td>
					<td><textarea name="filedesc[0]" class="inputbox twoj_width_90"></textarea></td>
				</tr>
			</tbody>
		</table>	
		<div class="clr"></div>
		<input type="hidden" name="task" value="" />
		
		<input type="hidden" name="jform[state]" value="" />
		<input type="hidden" name="jform[foldernew]" value="" />
		<input type="hidden" name="jform[folderlist]" value="" />
		<input type="hidden" name="jform[language]" value="" />
		
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php echo TwojToolboxHTMLHelper::getVersion(); ?>
<div id="twojtoolbox_general_options" class="twoj_hiddenblock">
<?php 
	foreach($this->form->getFieldset('left') as $field): 
		echo '<div class="twoj_control-group twoj_clear_left">
			<div class="twoj_control-label">'.$field->label.'</div>
			<div class="twoj_controls">'.$field->input.'<div class="twoj_clear"></div></div>
		</div>';
	endforeach;
	foreach($this->form->getFieldset('right') as $field): 
		echo '<div class="twoj_control-group twoj_clear_left">
			<div class="twoj_control-label">'.$field->label.'</div>
			<div class="twoj_controls">'.$field->input.'<div class="twoj_clear"></div></div>
		</div>';
	endforeach; 
?>
</div>
