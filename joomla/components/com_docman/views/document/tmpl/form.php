<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.jquery'); ?>
<?= @helper('behavior.bootstrap', array('namespace' => false)); ?>
<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.validator'); ?>

<!--
<script src="media://com_docman/js/document.js" />
<script src="media://lib_koowa/js/koowa.js" />
-->

<? if ($document->isNew()): ?>
<script>
window.addEvent('domready', function() {
    var controller = document.id('document-form').retrieve('controller');

    controller.addEvent('validate', function() {
        if (!jQuery('#docman_category_id').select2('val')) {
            alert('<?= @text('Please select a category for your document'); ?>');
            return false;
        }

        return true;
    });
});

</script>
<? endif; ?>


<? if ($menu->params->get('upload_folder')):
    $folder = trim($menu->params->get('upload_folder'), '/');
?>
<script>
// Prepend the upload_folder parameter to the path when it changes
jQuery(function($) {
    $('#storage_path_file').bind('change', function(e) {
        this.value = '<?= $folder ?>'+'/'+this.value;
    });
});
</script>
<? endif; ?>

<div class="frontend-toolbar">
<?= @helper('com://admin/docman.template.helper.toolbar.render', array(
	'toolbar' => $this->getView()->getToolbar()
));?>
</div>

<form action="<?= @route($document, 'layout=form'); ?>" method="post" id="document-form" class="-koowa-form" data-toolbar=".toolbar-list">
<div class="com_docman boxed" style="padding: 20px;">
	<fieldset class="form-horizontal">
		<legend><?= @text('Details'); ?></legend>
		<div class="control-group">
			<label class="control-label"><?= @text('Title'); ?></label>
			<div class="controls">
				<input class="required" id="title_field" type="text" class="title" size="25" name="title" value="<?= $document->title; ?>" class="required" maxlength="255" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?= @text('Category'); ?></label>
			<div class="controls">
				<?= @helper('listbox.categories', array(
						'identifier' => 'com://site/docman.model.categories',
				        'required' => true,
						'check_access' => true,
						'name' => 'docman_category_id',
                        'attribs' => array(
                            'id' => 'docman_category_id',
                            'class' => 'required select2-listbox'
                        ),
						'selected' => $document->docman_category_id,
						'filter' => array(
                            'parent_id' => isset($root_category) ? $root_category : null,
                            'include_self' => true,
						    'page' => $state->page,
						    'access' => JFactory::getUser()->getAuthorisedViewLevels()
						)
				))?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?= @text('File Type'); ?></label>
			<div class="controls">
				<?= @helper('listbox.storage_types', array(
					'name'       => 'storage_type',
					'attribs'    => array('id' => 'storage_type'),
					'selected'   => $document->storage_type,
				))?>
			</div>
		</div>
		<?
			$remote_value = $document->storage_type == 'remote' ? $document->storage_path : '';
			$file_value = $document->storage_type == 'file' ? $document->storage_path : '';
		?>
		<div class="control-group" id="document-remote-path-row" style="display: none">
			<label class="control-label"><?= @text('URL of Document'); ?></label>
			<div class="controls">
				<input class="validate-storage validate-stream-wrapper " data-type="remote" id="storage_path_remote" type="text" size="25"
					maxlength="512"
                    data-streams='<?= json_encode($document->getStreamWrappers()); ?>'
					name="storage_path_remote" value="<?= $remote_value; ?>" />
			</div>
		</div>
		<div class="control-group" id="document-file-path-row" style="display: none">
			<label class="control-label"><?= @text('File path'); ?></label>
			<div class="controls">
				<div class="input-append">
				<?= @helper('modal.select', array(
					'name'  => 'storage_path_file',
				    'id'  => 'storage_path_file',
					'value' => $file_value,
					'link'  => JRoute::_('index.php?option=com_docman&view=files&layout=select&tmpl=component&Itemid='.$state->page[0]),
					'link_selector' => 'modal-button',
					'attribs' => array('class' => 'validate-storage', 'data-type' => 'file')
				)) ?>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><?= @text('Description'); ?></legend>
		<?= @editor(array(
			'name'    => 'description',
		    'id'      => 'description',
			'width'   => '100%', 'height' => '200',
			'rows'    => '20'
		)); ?>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?= @text('Metadata options'); ?></legend>
        <div class="control-group">
            <label class="control-label"><?= @text('Image'); ?></label>
            <div class="controls">
                <?= @helper('behavior.thumbnail', array(
                'automatic_thumbnail' => $document->image === 'generated/'.md5($document->id).'.png',
                'automatic_thumbnail_image' => 'generated/'.md5($document->id).'.png',
                'value' => $document->image,
                'name'  => 'image',
                'id'  => 'image',
            )) ?>
            </div>
        </div>
		<div class="control-group">
			<label class="control-label"><?= @text('Icon'); ?></label>
			<div class="controls">
				<?= @helper('modal.icon', array(
					'name'  => 'params[icon]',
				    'id' => 'params_icon',
					'value' => $document->params->icon ? $document->params->icon : 'default.png',
					'link'  => @route('option=com_docman&view=files&layout=select_icon&tmpl=component&container=docman-icons&types[]=image'),
					'link_selector' => 'modal-button'
				))?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name"><?= @text('Alias') ?></label>
			<div class="controls">
				<input type="text" name="slug" size="32" maxlength="255" value="<?= $document->slug ?>" />
			</div>
		</div>
		<? if ($document->canPerform('manage') || $document->created_by == $state->current_user): ?>
		<div class="control-group">
		    <label class="control-label" for="enabled"><?= @text('Status'); ?></label>
        	<div class="controls">
        	    <?= @helper('select.booleanlist', array(
	        	    	'name' => 'enabled', 
	        	    	'selected' => $document->enabled,
	        	    	'true' => 'Published',
	        	    	'false' => 'Unpublished'
	        	    )); ?>
        	</div>
		</div>
        <div class="control-group">
            <label class="control-label"><?= @text('Start publishing on'); ?></label>
            <div class="controls">
                <?= @helper('behavior.calendar', array(
                    'name' => 'publish_on',
                    'id' => 'publish_on',
                    'value' => $document->publish_on,
                    'format' => '%Y-%m-%d %H:%M:%S',
                    'filter' => 'user_utc'
                ))?>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"><?= @text('Stop publishing on'); ?></label>
            <div class="controls">
                <?= @helper('behavior.calendar', array(
                    'name' => 'unpublish_on',
                    'id' => 'unpublish_on',
                    'value' => $document->unpublish_on,
                    'format' => '%Y-%m-%d %H:%M:%S',
                    'filter' => 'user_utc'
                ))?>
            </div>
        </div>
            <div class="control-group">
                <label class="control-label"><?= @text('Date'); ?></label>
                <div class="controls">
                    <?= @helper('behavior.calendar', array(
                        'name' => 'created_on',
                        'id' => 'created_on',
                        'value' => $document->created_on,
                        'format' => '%Y-%m-%d %H:%M:%S',
                        'filter' => 'user_utc'
                    ))?>
                </div>
            </div>
        <? endif; ?>
        <? if ($document->canPerform('manage')): ?>
		<div class="control-group">
			<label class="control-label"><?= @text('Owner'); ?></label>
			<div class="controls">
				<?= @helper('listbox.users', array(
					'name' => 'created_by',
					'selected' => $document->created_by ? $document->created_by : $user->id,
					'deselect' => false,
            		'attribs' => array('class' => 'select2-listbox'),
            		'behaviors' => array('select2' => array('element' => '.select2-listbox'))
                )) ?>
			</div>
		</div>
        <? endif; ?>
		<? if ($document->modified_by): ?>
		<div class="control-group">
			<label class="control-label"><?= @text('Modified by'); ?></label>
			<div class="controls">
				<span class="help-info">
				<?= JFactory::getUser($document->modified_by)->name; ?>
				<?= @text('on') ?>
				<?= @date(array('date' => $document->modified_on)); ?>
				</span>
			</div>
		</div>
		<? endif; ?>
		
	</fieldset>
</div>

</form>