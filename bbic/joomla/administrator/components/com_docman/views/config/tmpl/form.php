<? defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap'); ?>
<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.jquery'); ?>
<?= @helper('behavior.modal'); ?>
<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.validator'); ?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
<script src="media://com_docman/js/jquery.tagsinput.js" />
-->

<script>

if(Form && Form.Validator) {
	Form.Validator.add('validate-storage-path', {
		errorMsg: <?= json_encode(@text('Folder names can only contain letters, numbers, dash or underscore')); ?>,
		test: function(field){
			return field.get('value').test(/^[0-9A-Za-z_\-\/]+$/);
		}
	});
}

jQuery(function($) {
	SqueezeBox.assign(document.id('open-advanced-permissions'), {
        handler: 'iframe',
        size: {x: 875, y: 550},
        onClose: function() {}
	});

	var tag_list = $('#allowed_extensions').tagsInput({
    		removeWithBackspace: false,
    		width: '100%',
    		height: '100%',
            animate: true,
    		defaultText: '<?= @text('Add another extension...'); ?>'
    	}),
        filetypes = <?= json_encode($filetypes); ?>,
		labels = {
    		'audio': <?= json_encode(@text('Audio files')); ?>,
    		'archive': <?= json_encode(@text('Archive files')); ?>,
    		'document': <?= json_encode(@text('Documents')); ?>,
    		'image': <?= json_encode(@text('Images')); ?>,
    		'video': <?= json_encode(@text('Video files')); ?>
    	},
		list = $('#extension_groups'),
		group = $('#config-group li')[0];

	$.each(filetypes, function(key, value) {
		var label = labels[key],
			el = $(group).clone();

		el.find('span').text(label);
		el.find('a').data('extensions', value);
		list.append(el);
	});
	
	list.on('click', 'a', function(e) {
		e.preventDefault();

		var el = $(this),
		    method = (el.hasClass('add') ? 'add' : 'remove') + 'Tag',
		    extensions = el.data('extensions');

        $.each(extensions, function(i, extension) {
        	tag_list[method](extension, {unique: true, mark_input: false});
        });
	});

	var evt = function() {
		var value   = $('#maximum_size').val()*1048576,
			max     = <?= json_encode($upload_max_filesize); ?>;

		$('<input type="hidden" name="maximum_size" />').val(Math.min(value, max)).appendTo($(this.form));
	};

	// TODO: refactor once Koowa.js is refactored
	$$('.-koowa-form').addEvent('before.apply', evt).addEvent('before.save', evt);
});
</script>
			
<ul id="config-group" class="hide">
    <li>
        <span></span>
        <div class="btn-group">
            <a href="#" class="btn btn-mini btn-group add"><i class="icon-plus"></i></a>
            <a href="#" class="btn btn-mini btn-group remove"><i class="icon-minus"></i></a>
        </div>
    <li>
</ul>

<form action="" method="post" class="-koowa-form docman-container" style="padding: 20px;" data-toolbar=".toolbar-list">
<div class="row-fluid">
<div class="span8">
	<fieldset class="form-horizontal">
		<legend><?= @text('General Settings') ?></legend>
		<div class="control-group">
			<label for="document_path" class="control-label"><?= @text('Store files in') ?></label>
			<div class="controls">
				<input class="validate-storage-path" size="30" type="text" id="document_path" name="document_path" value="<?= $config->document_path ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?= @text('Allow file uploads up to');?></label>
			<div class="controls">
				<div class="input-append">
					<input type="text" id="maximum_size" value="<?= floor($config->maximum_size/1048576); ?>" />
					<span class="add-on"><?= @text('MB'); ?></span>
				</div>
				<span class="help-block"><?= @text('Your server has a maximum upload limit of <strong>%size%</strong> megabytes', array(
				    '%size%' => floor($upload_max_filesize/1048576))); ?></span>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label"><?= @text('Cache image thumbnails');?></label>
			<div class="controls">
				<?= @helper('select.booleanlist', array('name' => 'thumbnails', 'selected' => $config->thumbnails)); ?>
			</div>
		</div>
		<div class="control-group">

	<div class="row-fluid">
		<div class="span10">
		<label class="control-label"><?= @text('Allow these extensions for uploaded files');?></label>

			<div class="controls">
				<input type="text" name="allowed_extensions" id="allowed_extensions" value="<?= implode(',', $config->allowed_extensions); ?>" />
			</div>
		</div>
		<div class="span2">
			<h5><?= @text('Select from presets'); ?></h5>
			<ul id="extension_groups"></ul>
		</div>
	</div>

		</div>
	</fieldset>
</div>
<div class="span4">
	<fieldset class="form-horizontal">
		<legend><?= @text('Basic Permissions') ?></legend>
		<div class="control-group">
			<label class="control-label"><?= @text('Enable "Register or login to download" mode');?></label>
			<?= @helper('select.booleanlist', array('name' => 'login_to_download', 'wrap' => true, 'selected' => $config->login_to_download)); ?>
		</div>
	</fieldset>
	<fieldset class="form-horizontal">
		<legend><?= @text('Advanced Permissions') ?></legend>
			<div style="padding-top: 10px">
				<a href="<?= @route('option=com_docman&view=config&layout=acl&tmpl=component'); ?>" class="btn" id="open-advanced-permissions">
					<?= @text('Set advanced permissions')?>
				</a>
				<span class="help-block"><?= @text('If you would like to restrict actions like downloading a document, editing a category based on the user groups, you can use the Advanced Permissions screen.'); ?></span>
			</div>
	</fieldset>
</div>
</div>
</form>
