<?php
/**
 * FW Gallery Frontend Manager 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="toolbar" class="toolbar">
	<table class="toolbar fw-gallery-tolbar">
		<tbody>
			<tr>
				<td class="button toolbar-fw-publish">
					<a class="toolbar" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to edit', true); ?>');}else{ submitbutton('publish'); }">
						<span title="Publish" class="icon-32-publish"></span>
						<?php echo JText :: _('Publish'); ?>
					</a>
				</td>

				<td class="button toolbar-fw-unpublish">
					<a class="toolbar" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to edit', true); ?>');}else{ submitbutton('unpublish'); }">
						<span title="Unpublish" class="icon-32-unpublish"></span>
						<?php echo JText :: _('Unpublish'); ?>
					</a>
				</td>

				<td class="button toolbar-fw-new">
					<a class="toolbar" href="javascript: submitbutton('add');">
						<span title="New" class="icon-32-new"></span>
						<?php echo JText :: _('New'); ?>
					</a>
				</td>

				<td class="button">
					<a class="toolbar toolbar-fw-edit" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to edit', true); ?>');}else{submitbutton('edit')}">
						<span title="Edit" class="icon-32-edit"></span>
						<?php echo JText :: _('Edit'); ?>
					</a>
				</td>

				<td class="button">
					<a class="toolbar toolbar-fw-delete" href="javascript:if(document.adminForm.boxchecked.value==0){alert('<?php echo JText :: _('Please make a selection from the list to delete', true); ?>');}else if (confirm('<?php echo JText :: _('ARE_YOU_SURE', true); ?>?')) {submitbutton('remove')}">
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
<form action="index.php?option=com_fwgallery&amp;view=frontendmanager" method="post" name="adminForm" id="adminForm">
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th style="width:20px;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
	            <th style="width:20px;">&nbsp;</th>
	            <th><?php echo JText::_('Name'); ?></th>
	            <th class="hidden-phone"><?php echo JText::_('User'); ?></th>
	            <th class="hidden-phone"><?php echo JText::_('View Access'); ?></th>
	            <th style="width:5%;"><?php echo JText::_('Published'); ?></th>
	            <th class="hidden-phone" style="width:5%;"><?php echo JText::_('Images Qty'); ?></th>
	        </tr>
	    </thead>
	    <tbody>
<?php
if ($this->list) {
    foreach ($this->list as $num=>$project) {
    	$color = JFHelper :: getGalleryColor($project->id);
?>
	        <tr class="row<?php echo $num%2; ?>">
	            <td><?php echo JHTML::_('grid.id', $num, $project->id, $project->user_id != $this->user->id); ?></td>
				<td><?php if ($color) { ?><div class="fwg-gallery-color" style="background-color:#<?php echo $color; ?>"></div><?php } ?></td>
	            <td>
<?php
		if ($project->user_id == $this->user->id) {
?>
	                <a href="index.php?option=com_fwgallery&amp;view=frontendmanager&amp;layout=edit_gallery&amp;cid[]=<?php echo $project->id.($this->pagination->limitstart?('&limitstart='.$this->pagination->limitstart):''); ?>">
	                    <?php echo $project->treename; ?>
	                </a>
<?php
		} else {
?>
                    <?php echo $project->treename; ?>
<?php
		}
?>
	            </td>
	            <td class="hidden-phone"><?php echo $project->_user_name; ?></td>
	            <td class="hidden-phone"><?php echo $project->_group_name?$project->_group_name:JText :: _('Guests'); ?></td>
	            <td style="text-align:center;">
<?php
		if ($project->user_id == $this->user->id) {
?>
	            	<a href="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=frontendmanager&layout=default_gallery&task='.($project->published?'un':'').'publish&cid[]='.$project->id); ?>">
	            		<img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/<?php if ($project->published) { ?>tick<?php } else { ?>publish_x<?php } ?>.png"/>
            		</a>
<?php
		} else {
?>
	                <img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/<?php if($project->published) { ?>tick<?php } else { ?>publish_x<?php } ?>.png" />
<?php
		}
?>
	            </td>
	            <td class="hidden-phone" style="text-align:center;"><?php echo $project->_qty; ?></td>
	        </tr>
<?php
    }
} else {
?>
			<tr class="row0">
				<td colspan="7"><?php echo JText :: _('No galleries'); ?></td>
			</tr>
<?php
}
?>
	    </tbody>
	</table>
	<div class="pagination">
		<?php echo $this->pagination->getListFooter(); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="limitstart" value="<?php echo $this->pagination->limitstart; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
<div class="clr"></div>
