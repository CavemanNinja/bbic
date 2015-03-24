<?php
/**
* @package     	2JToolBox
* @author       2JoomlaNet http://www.2joomla.net
* @ñopyright   	Copyright (c) 2008-2012 2Joomla.net All rights reserved
* @license      released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version      $Revision: 1.0.2 $
**/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');
?>
<form action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="twojtoolbox_element" class="form-validate form-horizontal">
	<div id="twoj_formEditElement">
		<div class="twoj_width_60 twoj_float_left" >
			<fieldset class="adminform">
				<legend><?php echo empty($this->item->id) ? JText::_('COM_TWOJTOOLBOX_ELEMENT_NEW_ELEMENT') : JText::sprintf('COM_TWOJTOOLBOX_ELEMENT_EDIT_ELEMENT', $this->item->id); ?></legend>
		
					<?php 
					foreach($this->form->getFieldset('details') as $field):
						echo '
							<div class="twoj_control-group  twoj_clear_left">
								<div class="twoj_control-label">'.$field->label.'</div><div class="twoj_controls">'.$field->input.'<div class="twoj_clear_left"></div></div>
							</div>
							';
					 endforeach; ?>
				<div class="twoj_clear"></div>
				<div><?php echo $this->form->getLabel('desc'); ?></div>
				<div class="twoj_clear"></div>
				<div><?php echo $this->form->getInput('desc'); ?></div>
			</fieldset>
		</div>
		<div class="twoj_width_40 twoj_float_right">
			<a href="#" id="twojtoolbox_image_preview_link"><div id="twojtoolbox_image_preview"></div></a>
			<?php if( count( $this->form->getFieldset('params') ) ){ ?>
			<?php echo  JHtml::_('sliders.start', 'element-slider'); ?>
			<?php echo JHtml::_('sliders.panel',JText::_('COM_TWOJTOOLBOX_ELEMENT_ELEMENTPARAMS'), 'publishing-details'); ?>
			<fieldset class="panelform">
					<?php foreach($this->form->getFieldset('params') as $field): ?>
						<?php echo '
							<div class="twoj_control-group  twoj_clear_left">
								<div class="twoj_control-label">'.$field->label.'</div><div class="twoj_controls">'.$field->input.'<div class="twoj_clear_left"></div></div>
							</div>
							';?>
					<?php endforeach; ?>
				<div class="twoj_clear"></div>
			</fieldset>	
			<?php echo JHtml::_('sliders.end'); ?>
			<?php } ?>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<div class="twoj_clear"></div>
</form>
<div id="twojtoolbox_filelist" class="twojtoolbox_hiddenblock"><div id="twojtoolbox_filelist_in"></div></div>
<?php echo TwojToolboxHTMLHelper::getVersion(); ?>

