<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('bootstrap.tooltip');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

JToolBarHelper :: title(JText :: _('FWG_GALLERIES'), 'fwgallery-galleries.png');
JToolBarHelper :: publish();
JToolBarHelper :: unpublish();
JToolBarHelper :: addNew();
JToolBarHelper :: editList();
JToolBarHelper :: deleteList(JText :: _('FWG_ARE_YOU_SURE'));
?>
<form action="index.php?option=com_fwgallery&amp;view=fwgallery" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="btn-toolbar">
			<div class="btn-group pull-right hidden-phone">
				<select name="client" id="client" class="input-medium" onchange="document.adminForm.limitstart.value=0;document.adminForm.submit();">
					<option value=""><?php echo JText :: _('FWG_USER');?></option>
					<?php echo JHtml :: _('select.options', $this->clients, 'id', 'name', $this->client); ?>
				</select>
			</div>
		</div>
	</div>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th style="width:20px;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
	            <th style="width:20px;">&nbsp;</th>
	            <th class="hidden-phone"><?php echo JText :: _('FWG_DATE'); ?></th>
	            <th><?php echo JText :: _('FWG_NAME'); ?></th>
	            <th class="hidden-phone"><?php echo JText :: _('FWG_USER'); ?></th>
	            <th class="hidden-phone"><?php echo JText :: _('FWG_VIEW_ACCESS'); ?></th>
	            <th class="hidden-phone" style="width:110px !important;"><?php echo JText :: _('FWG_ORDER') . JHTML :: _('grid.order', $this->projects); ?></th>
	            <th style="width:5%;"><?php echo JText :: _('FWG_PUBLISHED'); ?></th>
	            <th style="width:5%;"><?php echo JText :: _('FWG_IMAGES_QTY'); ?></th>
	        </tr>
	    </thead>
	    <tbody>
<?php
if ($this->projects) {
    foreach ($this->projects as $num=>$project) {
    	$color = JFHelper :: getGalleryColor($project->id);
?>
	        <tr class="row<?php echo $num%2; ?>">
	            <td><?php echo JHTML :: _('grid.id', $num, $project->id); ?></td>
				<td><?php if ($color) { ?><div class="fwg-gallery-color" style="background-color:#<?php echo $color; ?>"></div><?php } ?></td>
	            <td class="hidden-phone center">
	                <?php echo substr($project->created, 0, 10); ?>
	            </td>
	            <td>
	                <a href="index.php?option=com_fwgallery&amp;view=fwgallery&amp;task=edit&amp;cid[]=<?php echo $project->id; ?>">
	                    <?php echo $project->treename; ?>
	                </a>
	            </td>
	            <td class="hidden-phone"><?php echo $project->_user_name; ?></td>
	            <td class="hidden-phone"><?php echo $project->_group_name?$project->_group_name:JText :: _('FWG_PUBLIC'); ?></td>
	            <td class="order hidden-phone">
	                <span><?php echo $this->pagination->orderUpIcon($num, $num?true:false, 'orderup', 'Move Up'); ?></span>
	                <span><?php echo $this->pagination->orderDownIcon($num, count($this->projects), true, 'orderdown', 'Move Down'); ?></span>
	                <input type="text" name="order[]" size="5" value="<?php echo $project->ordering; ?>" class="inputbox text-area-order" style="text-align: center" />
	            </td>
	            <td class="center">
	                <?php echo JHTML :: _('fwgGrid.published', $project, $num); ?>
	            </td>
	            <td class="center"><?php echo $project->_qty; ?></td>
	        </tr>
<?php
    }
} else {
?>
			<tr class="row0">
				<td colspan="7"><?php echo JText :: _('FWG_NO_GALLERIES'); ?></td>
			</tr>
<?php
}
?>
	    </tbody>
	</table>
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
<script type="text/javascript">
window.addEvent('domready', function() {
    $$('input[name=type]').addEvent('change', function() {
        var frm = document.adminForm;
        frm.task.task = '';
        frm.submit();
    });
});
</script>