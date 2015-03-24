<?php
/**
 * FW Gallery Frontend Manager 3.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

JHTML :: _('behavior.formvalidation');
JHTML :: _('behavior.colorpicker');

$editor = JFactory::getEditor();
$color = JFHelper :: getGalleryColor($this->obj->id);
?>
<div id="toolbar" class="toolbar">
	<table class="toolbar">
		<tbody>
			<tr>
				<td class="button">
					<a class="toolbar" href="javascript: submitbutton('save')">
						<span title="Save" class="icon-32-save fw-icon-32-save"></span>
						<?php echo JText :: _('Save'); ?>
					</a>
				</td>

				<td class="button">
					<a class="toolbar" href="javascript: submitbutton('cancel')">
						<span title="Close" class="icon-32-cancel fw-icon-32-cancel"></span>
						<?php echo JText :: _($this->obj->id?'Close':'Cancel'); ?>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="clr"></div>
</div>
<div class="clr"></div>
<form action="index.php?option=com_fwgallery&view=frontendmanager" method="post" name="adminForm" id="adminForm" class="form-validate">
    <table class="admintable">
        <tr>
            <td class="key">
                <label for="name">
                    <?php echo JText::_('GALLERY_NAME'); ?><span class="required">*</span> :
                </label>
            </td>
            <td>
                <input class="inputbox required" type="text" name="name" size="50" maxlength="100" value="<?php echo $this->escape($this->obj->name);?>" />
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo JText::_('Parent Gallery'); ?>:
            </td>
            <td>
                <?php echo JHTML :: _('select.genericlist', array_merge(array(
                	JHTML :: _('select.option', 0, JText :: _('FWG_TOP'), 'id', 'treename')
                ), (array)$this->projects), 'parent', '', 'id', 'treename', $this->obj->parent); ?>
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo JText::_('FWG_DATE'); ?>:
            </td>
            <td>
                <?php echo JHTML::_('calendar', substr($this->obj->created?$this->obj->created:date('Y-m-d'), 0, 10), 'created', 'created', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
            </td>
        </tr>
        <tr>
            <td class="key">
                <label for="color"><?php echo JText::_('Color'); ?>:</label>
            </td>
            <td id="color-row">
				<input id="color" name="color" type="text" size="13" value="<?php echo $color; ?>" class="input-colorpicker"/>
            </td>
        </tr>
        <tr>
            <td class="key">
                <label for="gid">
                    <?php echo JText::_('View access'); ?>:
                </label>
            </td>
            <td>
                <?php echo JHTML::_('select.genericlist', (array)$this->groups, 'gid', '', 'id', 'name', $this->obj->gid?$this->obj->gid:($this->groups?$this->groups[0]->id:29)); ?>
            </td>
        </tr>
<?php
/*$dispatcher = JDispatcher::getInstance();
JPluginHelper :: importPlugin('fwgallery');
$dispatcher->trigger('getFrontendGalleryForm', array($this->obj));*/
?>
        <tr>
            <td class="key">
                <label for="published1">
                    <?php echo JText::_('Published'); ?>:
                </label>
            </td>
            <td>
                <input type="radio" name="published" id="published1" value='1'<?php echo $this->obj->published?' checked':'' ?>/>&nbsp;<?php echo JText::_('YES'); ?>&nbsp;&nbsp;
                <input type="radio" name="published" id="published0" value='0'<?php echo !$this->obj->published?' checked':'' ?>/>&nbsp;<?php echo JText::_('NO'); ?>
            </td>
        </tr>

        <tr>
            <td class="key">
                <label for="descr">
                    <?php echo JText::_('Description'); ?>:
                </label>
            </td>
            <td>
                <?php echo $editor->display('descr',  $this->obj->descr, '100%', '350', '75', '20', false);?>
            </td>
        </tr>
    </table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->obj->id; ?>" />
	<input type="hidden" name="limitstart" value="<?php echo JRequest :: getInt('limitstart'); ?>" />
</form>
<span class="required">*</span> - <?php echo JText :: _('Required fields'); ?>
<script type="text/javascript">
var fwgRainbow;
window.addEvent('domready', function() {
	document.getElement('#myRainbowButton').addEvent('click', function(ev) {
		document.getElement('#myRainbow').fireEvent('click', ev);
	});
	document.getElement('#myRainbowClearButton').addEvent('click', function(ev) {
		document.getElement('#color').value = '';
		document.getElement('#color-row').setStyle('background-color', '');
	});
	document.getElement('#color').addEvent('keyup', function() {
		if (this.value.match(/^#[0-9a-fA-F]{3,6}$/)) {
			document.getElement('#color-row').setStyle('background-color', this.value);
			fwgRainbow.manualSet(this.value, 'hex');
		} else {
			document.getElement('#color-row').setStyle('background-color', '');
		}
	});
	fwgRainbow = new MooRainbow('myRainbow', {
		wheel: true,
		imgPath: '<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/',
		onChange: function(color) {
			document.getElement('#color').value = color.hex;
			document.getElement('#color-row').setStyle('background-color', color.hex);
		},
		onComplete: function(color) {
			document.getElement('#color').value = color.hex;
			document.getElement('#color-row').setStyle('background-color', color.hex);
		}
	});
<?php
if ($color) {
?>
	fwgRainbow.manualSet('<?php echo $color; ?>', 'hex');
<?php
}
?>
});
function submitbutton(task) {
	var form = document.getElement('#adminForm');
	if ((task == 'apply' || task == 'save') && !document.formvalidator.isValid(form)) {
		alert('<?php echo JText :: _('Not all required fields are filled', true); ?>');
	} else {
		form.task.value = task;
		form.submit();
	}
}
</script>