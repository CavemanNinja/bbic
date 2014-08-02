<?php
/**
 * fwrealestatej30 y 3.1.0
 * @copyright (C) 2011 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JToolBarHelper::title(JText :: _('FWG_LANGUAGE_MANAGER'));

?>
<div id="fwrealestate">
	<form action="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=language'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span3">
				<?php echo JText :: _('FWG_TRANSLATE_TO'); ?>&nbsp;<?php echo JHTML :: _('select.genericlist', $this->languages, 'lang', 'onchange="this.form.task.value=\'\';this.form.submit();" style="float:none;"', 'id', 'name', $this->language); ?>&nbsp;&nbsp;&nbsp;
			</div>
			<div class="span3">
				<input type="radio" name="type" value="1" onchange="this.form.task.value='';this.form.submit();"<?php if ($this->type == 1) { ?> checked="checked"<?php } ?>/> <?php echo JText :: _('FWG_FRONTEND'); ?>&nbsp;&nbsp;&nbsp;
				<input type="radio" name="type" value="" onchange="this.form.task.value='';this.form.submit();"<?php if ($this->type != 1) { ?> checked="checked"<?php } ?>/> <?php echo JText :: _('FWG_BACKEND'); ?>&nbsp;&nbsp;&nbsp;
			</div>
			<div class="span3">
				<?php echo JText :: _('FWG_QUICK_SEARCH') ?>:<input type="text" name="search" value="<?php echo $this->escape($this->search); ?>" />&nbsp;
			</div>
			<div class="span3">
				<span class="btn-group"><input type="submit" class="btn btn-small btn-success" value="<?php echo JText :: _('FWG_SEARCH'); ?>" onclick="with(this.form) {task.value='';submit();}" /></span>&nbsp;
				<span class="btn-group"><input type="button" class="btn btn-small btn-danger" value="<?php echo JText :: _('FWG_SAVE'); ?>" onclick="with(this.form) {task.value='save';submit();}" /></span>
			</div>
		</div>

		<div class="row-fluid fwre-admin-hint">
			<?php echo JText :: _('FWG_LANGUAGE_MANAGER_ABOUT'); ?>
		</div>

		<table class="adminlist">
		    <thead>
		        <tr>
		            <th style="width:19%">en-GB</th>
		            <th style="width:1%"></th>
		            <th style="width:60%"><?php echo $this->languages[$this->language]->tag; ?></th>
		        </tr>
		    </thead>
		    <tbody>
<?php
$num = 0;
foreach ($this->data as $const=>$row) {
?>
		        <tr class="row<?php echo $num%2; ?>">
		        	<td><?php echo $row['src']; ?></td>
		        	<td><span class="label" title="<?php echo $this->escape($const); ?>"><?php echo JText :: _('FWG_TAG'); ?></span></td>
		        	<td><input type="text" style="width:100%;" name="lang_data[<?php echo $const; ?>]" value="<?php echo str_replace('"', '&quot;', $row['trg']); ?>" /></td>
				</tr>
<?php
}
?>
		    </tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />

	</form>
</div>