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

<div id="mulitplefileuploader"><?php echo JText::_('COM_TWOJTOOLBOX_BATCHUPLOAD_SELECTFILES'); ?></div>

<form  enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&view=batchupload');?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container" class="twoj_formEditItem">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_category_id" value="<?php echo TwojToolboxHelper::cgid(); ?>" />
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
