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

JToolBarHelper::title(JText :: _('FWG_IMAGES'), 'fwgallery-images.png');
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::custom('batch', 'new', 'new', 'FWG_BATCH_UPLOAD', false);
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList(JText :: _('FWG_ARE_YOU_SURE'));

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
?>
<form action="index.php?option=com_fwgallery&amp;view=files" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label class="element-invisible" for="search"><?php echo JText :: _('FWG_SEARCH_BY_IMAGE_NAME') ?></label>
				<input id="search" type="text" name="search" placeholder="<?php echo $this->escape(JText :: _('FWG_SEARCH_BY_IMAGE_NAME')); ?>" value="<?php echo JRequest :: getString('search'); ?>" />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button type="submit" class="btn tip hasTooltip" onclick="with(this.form){task.value='';status.value='';category.value='';limitstart.value=0;submit();}" title="<?php echo $this->escape(JText :: _('FWG_SEARCH')); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn tip hasTooltip" onclick="with(this.form){search.value="";task.value='';status.value='';category.value='';limitstart.value=0;submit();}" title="<?php echo $this->escape(JText :: _('FWG_CLEAR')); ?>"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php echo JHTML :: _('fwGalleryCategory.getCategories', 'project_id', $this->project_id, 'onchange="this.form.limitstart.value=0;this.form.submit();"', false, JText :: _('FWG_SELECT_GALLERY')); ?>
			</div>
		</div>
	</div>

	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th style="width:20px;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
	            <th class="hidden-phone"><?php echo JText :: _('FWG_ID'); ?></th>
<?php
if (count($this->types) > 1) {
?>
	            <th><?php echo JText :: _('FWG_TYPE'); ?></th>
<?php
}
?>
	            <th><?php echo JText :: _('FWG_IMAGE_PREVIEW'); ?></th>
	            <th class="hidden-phone"><?php echo JText :: _('FWG_DATE'); ?></th>
	            <th class="hidden-phone"><?php echo JText :: _('FWG_NAME'); ?></th>
	            <th class="hidden-phone" style="width:110px !important;"><?php echo JText :: _('FWG_ORDER') . JHTML :: _('grid.order', $this->files); ?></th>
	            <th class="hidden-phone"><?php echo JText :: _('FWG_GALLERY'); ?></th>
	        </tr>
	    </thead>
	    <tbody>
<?php
if ($this->files) {
	$num = 0;
    foreach ($this->files as $file) {
?>
	        <tr class="row<?php echo $num%2; ?>">
	            <td><?php echo JHTML :: _('grid.id', $num, $file->id); ?></td>
	            <td class="center hidden-phone"><?php echo $file->id; ?></td>
<?php
		if (count($this->types) > 1) {
?>
	            <td class="center"><?php echo $file->_type_name; ?></td>
<?php
		}
?>
	            <td>
	            	<div class="row center">
		                <a href="index.php?option=com_fwgallery&amp;view=files&amp;task=edit&amp;cid[]=<?php echo $file->id; ?>">
		                    <img <?php echo $file->selected?'class="current_image" ':''; ?>src="<?php echo JURI::root(true).'/'.JFHelper :: getFileFilename($file, 'th'); ?>" style="padding: 6px;border: none;"/>
		                </a>
		            </div>
	            	<div class="row center">
		            	<div class="btn-group">
			                <?php echo JHTML :: _('fwgGrid.selected', $file, $num); ?>
			                <?php echo JHTML :: _('fwgGrid.published', $file, $num); ?>
<?php
		if ($file->type_id == 1 and JFHelper::isFileExists($file, 'th')) {
?>
							<a class="btn btn-micro hasTooltip" href="javascript:" onclick="return listItemTask('cb<?php echo $num; ?>','clockwise')" title="<?php echo JText :: _('FWG_ROTATE_CLOCKWISE'); ?>"><img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/arrow_rotate_clockwise.png" /></a>
							<a class="btn btn-micro hasTooltip" href="javascript:" onclick="return listItemTask('cb<?php echo $num; ?>','counterclockwise')" title="<?php echo JText :: _('FWG_ROTATE_COUNTERCLOCKWISE'); ?>"><img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/arrow_rotate_anticlockwise.png" /></a>
<?php
		}
?>
			            </div>
		            </div>
	            </td>
	            <td class="center hidden-phone">
	                <?php echo substr($file->created, 0, 10); ?>
	            </td>
	            <td>
	                <a href="index.php?option=com_fwgallery&amp;view=files&amp;task=edit&amp;cid[]=<?php echo $file->id; ?>">
	                    <?php echo $file->name; ?>
	                </a>
	            </td>
	            <td class="order hidden-phone">
	                <span><?php echo $this->pagination->orderUpIcon($num, $num?true:false, 'orderup', 'Move Up'); ?></span>
	                <span><?php echo $this->pagination->orderDownIcon($num, count($this->files), true, 'orderdown', 'Move Down'); ?></span>
	                <input type="text" name="order[]" size="5" value="<?php echo $file->ordering; ?>" class="inputbox text-area-order" style="text-align: center" />
	            </td>
	            <td class="hidden-phone">
	                <a href="index.php?option=com_fwgallery&amp;view=fwgallery&amp;task=edit&amp;cid[]=<?php echo $file->project_id; ?>"><?php echo $file->_project_name; ?></a>
	            </td>
	        </tr>
<?php
		$num++;
    }
} else {
?>
			<tr class="row0">
				<td colspan="11"><?php echo JText :: _('FWG_NO_IMAGES'); ?></td>
			</tr>
<?php
}
?>
	    </tbody>
	</table>
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('a.fwgallery-clockwise').each(function(el) {
		el.addEvent('click', function() {

		});
	})
});
</script>