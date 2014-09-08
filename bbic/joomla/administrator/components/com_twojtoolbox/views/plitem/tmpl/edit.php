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
	JHtml::_('behavior.formvalidation');
	//JHtml::_('formbehavior.chosen', '.twoj_controls > select'); 
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_twojtoolbox&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="twojtoolbox_item" class="form-validate form-horizontal">
	<div class="twoj_formEditItem">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_TWOJTOOLBOX_ITEM_DETAILS' ); ?></legend>
			<?php echo $this->form->getLabel('name'); ?>
			<div id="twojtoolbox_labeltype"><?php echo $this->form->getLabel('type'); ?></div>
				<?php 
				foreach($this->form->getFieldset('details') as $field): 
					if($field->label==''){
						echo '<div>'.$field->input.'</div>';
					} else { echo '
						<div class="twoj_control-group  twoj_clear_left">
							<div class="twoj_control-label">'.$field->label.'</div><div class="twoj_controls">'.$field->input.'<div class="twoj_clear_left"></div></div>
						</div>
						';
					} 
				endforeach; ?>
			
			<div class="twoj_clear"></div>
		<?php 
			$tabBodu = '';
			$tabHeader = '';
			$fieldsets = $this->form->getFieldsets() ;
			if(count($fieldsets) >  2 ){
				$i = 0;
				foreach ( $fieldsets as $fieldset ) {
					if( $i > 1){
						$tabHeader .= '<li><a href="#'.$fieldset->name.'">'.JText::_($fieldset->label).'</a></li>';
						$tabBodu .= '<div id="'.$fieldset->name.'"><fieldset class="panelform">';
								foreach($this->form->getFieldset($fieldset->name) as $field):
										$label_html = $field->label;
										$input_html = $field->input;
										if( isset($field->hide) && $field->hide==1 ){
											$hide_li = ' style="display: none;"';
										} else {
											$hide_li = ''; 
										}
										if( (string) $field->fullhide!=1 ){
											if( isset($field->borderBottom) && $field->borderBottom==1 ){
												$tabBodu .= '
												<div class="twoj_control-group twoj_clear_left twoj_border_bottom ">
													<div class="twoj_control-label"'.$hide_li.'>'.$label_html.'</div>
													<div class="twoj_clear"></div>
												</div>';
											} else {
												$tabBodu .= '
												<div class="twoj_control-group twoj_clear_left">
													<div class="twoj_control-label"'.$hide_li.'>'.$label_html.'</div>
													<div class="twoj_controls">'.$input_html.'<div class="twoj_clear"></div></div>
												</div>';
											}
										}
								endforeach;
						$tabBodu .= '</fieldset></div>';
					}
					$i++;
				}
			}	
		echo '<div id="twoj_itemTabs"><ul>'.$tabHeader.'</ul>'.$tabBodu.'</div>'; ?>	
		</fieldset>
	</div>
	<div class="twoj_clear"></div>
	
	<div class="twojtoolbox_demo_section_wrap twoj_hiddenblock">
		<button id="plugin_demo_button" onclick="twojdemosection_click(); return false" type="submit" style="display: none;">
		<?php echo JText::_( 'COM_TWOJTOOLBOX_ITEM_PREVIEWDEMO' ); ?>
		</button>
		<br />
		<?php echo JText::_( 'COM_TWOJTOOLBOX_ITEM_PREVIEWDEMO_DESC' ); ?>
		<div id="plugin_demo_section" style="display: none;">
			<div id="plugin_demo_frame"></div>
		</div>
	</div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="default" id="resettodefault" value="" />
	<input type="hidden" name="democode" id="democode" value="" />
	<input type="hidden" id="com_twojtoolbox_item_openTab" name="com_twojtoolbox_item_openTab" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="twojtoolbox_itemdialog_importdemo"  class="twojtoolbox_hiddenblock">
<?php if( $this->demos && count($this->demos) ){
	foreach ($this->demos as $demo) {
		echo '<div class="twojtoolbox_demo_div" id="'.$demo->code.'">
				<img src="'.$demo->img.'" class="twojtoolbox_demo_img" title="'.JText::_($demo->title, 1).'" alt ="'.JText::_($demo->title, 1).'" />
			</div>';
	}
}?>
</div>
<?php echo TwojToolboxHTMLHelper::getVersion(); ?>

