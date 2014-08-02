<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap', array('namespace' => false)); ?>
<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.jquery'); ?>

<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.validator'); ?>
<? JHtml::_('behavior.calendar'); ?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
<script src="media://com_docman/js/document.js" />
-->

<script>
jQuery(function($) {
	// Remove and add inherit option to access list based on the parent category
	var access    = $('#access'),
		category  = $('#docman_category_id'),
        inherit   = access.children('option').first(),
        access_box = $('.current-access span').first(),
        change_category = function(e) {
        	change_access();
    	},
    	change_access = function() {
			var value = access.val(),
				catid = category.val();

			if (value == -1 && catid) {
				$.getJSON('?option=com_docman&view=category&format=json&id='+catid, function(data) {
					$('.current-access').css('display', 'block');
					access_box.text(data.data.access_title);
				});
			}
			else {
				$('.current-access').css('display', 'none');
				access_box.text('');
			}		
		};
    	
    change_category({val: category.val()});
    
    category.on('change', change_category);
    access.on('change', change_access);
});
</script>

<form action="" method="post" class="-koowa-form" data-toolbar=".toolbar-list">
<div class="com_docman boxed" style="padding: 20px;">
<div class="row-fluid">
<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?= @text('Details'); ?></legend>
		<div class="control-group">
			<label class="control-label"><?= @text('Title'); ?></label>
			<div class="controls">
				<input class="required" id="title_field" type="text" class="title" size="25" name="title" maxlength="255" value="<?= $document->title; ?>" class="required" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?= @text('Category'); ?></label>
			<div class="controls">
				<?= @helper('listbox.categories', array(
						'check_access' => true,
				        'deselect' => false,
				        'required' => true,
						'name' => 'docman_category_id',
						'attribs' => array('id' => 'docman_category_id'),
						'selected' => $document->docman_category_id
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
				<input class="validate-storage validate-stream-wrapper title" data-type="remote" id="storage_path_remote" type="text" size="25"
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
				    'id' => 'storage_path_file',
					'value' => $file_value,
					'link'  => @route('option=com_docman&view=files&layout=select&tmpl=component'),
					'link_selector' => 'modal-button',
					'attribs' => array('class' => 'validate-storage', 'data-type' => 'file')
				))?>
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><?= @text('Description'); ?></legend>
		<?= @editor(array(
			'name' => 'description',
            'id'   => 'description',
			'width' => '100%',
			'height' => '341',
			'cols' => '100',
			'rows' => '20',
			'buttons' => array('pagebreak')
		)); ?>
	</fieldset>
</div>
<div class="span6">
	<fieldset class="form-horizontal">
		<legend><?= @text('Metadata Options'); ?></legend>
		<div class="control-group">
			<label class="control-label" for="name"><?= @text('Alias') ?></label>
			<div class="controls">
				<input type="text" name="slug" size="32" maxlength="255" value="<?= $document->slug ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?= @text('Access'); ?></label>
			<div class="controls">
				<?= @helper('listbox.access', array(
					'name' => 'access',
			        'inheritable' => true,
					'selected' => $document->access_raw
				))?>
				
    			<div style="clear: both"></div>
    			<? 
    			$has_value = !$document->isNew() && $document->access_raw == -1;
    			$default = '<span>'
 					. ($has_value ? $document->access_title : '')
 					.'</span>';
    			?>
    			<p class="help-block current-access">
    				<?= @text('Calculated as %access%', array('%access%' => $default)); ?>
				</p>
			</div>
		</div>
		<div class="control-group">
				<label class="control-label" for="enabled"><?= @text('Status'); ?></label>
					<div class="controls">
							<?= @helper('select.booleanlist', array(
									'name' => 'enabled',
									'selected' => $document->enabled,
									'true' => @text('Published'),
									'false' => @text('Unpublished')
								)); ?>
					</div>
		</div>
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
			<label class="control-label"><?= @text('Owner'); ?></label>
			<div class="controls">
				<?= @helper('listbox.users', array(
					'name' => 'created_by',
					'selected' => $document->created_by ? $document->created_by : JFactory::getUser()->id,
					'deselect' => false,
            		'attribs' => array('class' => 'select2-listbox'),
            		'behaviors' => array('select2' => array('element' => '.select2-listbox'))
                )) ?>
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
		<? if ($document->isAclable() && $document->getPermissions()->canAdmin()): ?>
        <script>
            jQuery(function($){
                $('#advanced-permissions-toggle').on('click', function(e){
                    e.preventDefault();
                    $('#advanced-permissions').toggleClass('hide');
                });
            });
        </script>
		<div class="control-group">
			<label class="control-label"><?= @text('Advanced Permissions');?></label>
			<div class="controls">
                <a class="btn" id="advanced-permissions-toggle" href="#advanced-permissions">
                    <?= @text('Show document permissions')?>
                </a>
			</div>
		</div>
		<? endif; ?>
	</fieldset>
</div>
</div>
</div>

<? if ($document->isAclable() && $document->getPermissions()->canAdmin()): ?>
<div class="well hide" id="advanced-permissions" style="width-100">
<?= @helper('access.rules', array('section' => 'document', 'asset' => $document->getAssetName())); ?>
</div>
<? endif; ?>

</form>