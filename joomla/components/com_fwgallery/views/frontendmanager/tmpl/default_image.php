<?php
/**
 * FW Gallery Frontend Manager 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!file_exists(FWG_STORAGE_PATH) and !is_writable(JPATH_SITE.'/images')) {
?>
<p style="color:#f00;"><?php echo JText :: sprintf('Images folder %s is not writable!', JPATH_SITE.'/images') ?></p>
<?php
}
if (file_exists(FWG_STORAGE_PATH) and !is_writable(FWG_STORAGE_PATH)) {
?>
<p style="color:#f00;"><?php echo JText :: sprintf('Images folder %s is not writable!', FWG_STORAGE_PATH) ?></p>
<?php
}
if ($this->plugins) foreach ($this->plugins as $plugin) {
?>
<fieldset><?php echo $plugin; ?></fieldset>
<?php
}
?>
<div id="toolbar" class="toolbar">
	<table class="toolbar">
		<tbody>
			<tr>
				<td id="toolbar-publish" class="button">
					<a class="toolbar" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to edit', true); ?>');}else{ submitbutton('publish'); }">
						<span title="Publish" class="icon-32-publish"></span>
						<?php echo JText :: _('Publish'); ?>
					</a>
				</td>

				<td id="toolbar-unpublish" class="button">
					<a class="toolbar" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to edit', true); ?>');}else{ submitbutton('unpublish'); }">
						<span title="Unpublish" class="icon-32-unpublish"></span>
						<?php echo JText :: _('Unpublish'); ?>
					</a>
				</td>

				<td id="toolbar-new" class="button">
					<a class="toolbar" href="javascript: submitbutton('add');">
						<span title="New" class="icon-32-new"></span>
						<?php echo JText :: _('New'); ?>
					</a>
				</td>

				<td id="toolbar-edit" class="button">
					<a class="toolbar" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to edit', true); ?>');}else{submitbutton('edit')}">
						<span title="Edit" class="icon-32-edit"></span>
						<?php echo JText :: _('Edit'); ?>
					</a>
				</td>

				<td id="toolbar-delete" class="button">
					<a class="toolbar" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to delete', true); ?>');}else if (confirm('<?php echo JText :: _('ARE_YOU_SURE', true); ?>?')) {submitbutton('remove')}">
						<span title="Delete" class="icon-32-delete"></span>
						<?php echo JText :: _('Delete'); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="clr"></div>
</div>
<div class="clr"></div>
<form action="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=default_image'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<?php echo JText :: _('Search by image name') ?>:<br/><input type="text" name="search" style="width:150px;" value="<?php echo $this->escape($this->search); ?>" />&nbsp;<input type="submit" class="inputbox" value="<?php echo JText :: _('Search'); ?>" onclick="this.form.limitstart.value=0;" />&nbsp;&nbsp;&nbsp;
		</div>
		<div class="span6">
		    <?php echo JText :: _('Select gallery'); ?>:<br/><?php echo JHTML :: _('fwGalleryCategory.getCategories', 'project_id', $this->project_id, 'style="width:200px;" onchange="this.form.limitstart.value=0;this.form.submit();"', false, JText :: _('All'), false, false); ?>&nbsp;&nbsp;&nbsp;
		</div>
	</div>
	<div class="row-fluid">
		<table class="table table-striped">
		    <thead>
		        <tr>
		            <th style="width:20px;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
<?php
if (count($this->types) > 1) {
?>
	            	<th class="hidden-phone"><?php echo JText :: _('Type'); ?></th>
<?php
}
?>
		            <th><?php echo JText :: _('Image preview'); ?></th>
		            <th style="width:20px;"></th>
		            <th style="width:20px;"><?php echo JText :: _('Default'); ?></th>
		            <th class="hidden-phone"><?php echo JText :: _('Name'); ?></th>
		            <th class="hidden-phone" style="width:100px;"><?php echo JText :: _('Order'); ?>&nbsp;<a href="javascript:saveorder(<?php echo count($this->list) - 1; ?>, 'saveorder')"><img src="<?php echo JURI :: root(); ?>/components/com_fwgallery/assets/images/filesave.png" /></a></th>
		            <th class="hidden-phone"><?php echo JText :: _('Gallery'); ?></th>
		            <th style="width:100px;"><?php echo JText :: _('Publish'); ?></th>
		        </tr>
		    </thead>
		    <tbody>
<?php
if ($this->list) {
    foreach ($this->list as $num=>$file) {
        $link = JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=edit_image&cid[]='.$file->id.($this->pagination->limitstart?('&limitstart='.$this->pagination->limitstart):''));
?>
		        <tr class="row<?php echo $num%2; ?>">
		            <td><?php echo JHTML :: _('grid.id', $num, $file->id); ?></td>
<?php
		if (count($this->types) > 1) {
?>
	            	<td class="hidden-phone" style="text-align:center;"><?php echo $file->_type_name; ?></td>
<?php
		}
?>
		            <td>
		                <a href="<?php echo $link; ?>">
		                    <img <?php echo $file->selected?'class="current_image" ':''; ?>src="<?php echo JURI::root(true).'/'.JFHelper :: getFileFilename($file, 'th'); ?>" style="padding: 6px;border: none;"/>
		                </a>
		            </td>
		            <td style="text-align:center;">
<?php
		if ($file->type_id == 1 and JFHelper::isFileExists($file, 'th')) {
?>
						<a href="javascript:" onclick="return listItemTask('cb<?php echo $num; ?>','clockwise')" title="<?php echo JText :: _('Rotate clockwise'); ?>"><img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/arrow_rotate_clockwise.png" /></a>
						<a href="javascript:" onclick="return listItemTask('cb<?php echo $num; ?>','counterclockwise')" title="<?php echo JText :: _('Rotate counterclockwise'); ?>"><img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/arrow_rotate_anticlockwise.png" /></a>
<?php
		}
?>
		            </td>
		            <td style="text-align:center;">
		                <?php echo JHTML :: _('fwgGrid.selected', $file, $num); ?>
		            </td>
		            <td class="hidden-phone">
		                <a href="<?php echo $link; ?>">
		                    <?php echo $file->name; ?>
		                </a>
		            </td>
		            <td class="hidden-phone order">
		                <input type="text" name="order[]" size="5" value="<?php echo $file->ordering; ?>" class="inputbox" style="text-align: center" />
		                <span><?php echo str_replace('images/uparrow.png', 'components/com_fwgallery/assets/images/uparrow.png', $this->pagination->orderUpIcon($num, $num?true:false, 'orderup', 'Move Up')); ?></span>
		                <span><?php echo str_replace('images/downarrow.png', 'components/com_fwgallery/assets/images/downarrow.png', $this->pagination->orderDownIcon($num, count($this->list), true, 'orderdown', 'Move Down')); ?></span>
		            </td>
		            <td class="hidden-phone">
<?php
		if ($this->user->id == $file->_user_id) {
?>
	                	<a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=edit_gallery&cid[]='.$file->project_id); ?>"><?php echo $file->_project_name; ?></a>
<?php
		} else {
?>
	                	<?php echo $file->_project_name; ?>
<?php
		}
?>
		            </td>
		            <td style="text-align:center;">
		            	<a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=default_image&task='.($file->published?'un':'').'publish&cid[]='.$file->id); ?>">
		            		<img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/<?php if ($file->published) { ?>tick<?php } else { ?>publish_x<?php } ?>.png"/>
	            		</a>
		            </td>
		        </tr>
<?php
    }
} else {
?>
				<tr class="row0">
					<td colspan="<?php if (count($this->types) > 1) { ?>9<?php } else { ?>8<?php } ?>"><?php echo JText :: _('FWG_No_images'); ?></td>
				</tr>
<?php
}
?>
		    </tbody>
		</table>
	</div>
	<div class="pagination">
		<?php echo $this->pagination->getListFooter(); ?>
	</div>
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="limitstart" value="<?php echo $this->pagination->limitstart; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
<div class="clr"></div>
<script type="text/javascript">
function listItemTask( id, task ) {
    var f = document.adminForm;
    cb = eval( 'f.' + id );
    if (cb) {
        for (i = 0; true; i++) {
            cbx = eval('f.cb'+i);
            if (!cbx) break;
            cbx.checked = false;
        }
        cb.checked = true;
        f.boxchecked.value = 1;
        submitbutton(task);
    }
    return false;
}
function submitbutton(task) {
	var form = document.adminForm;
	form.task.value = task;
	form.submit();
}
</script>