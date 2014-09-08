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
<script src="media://lib_koowa/js/koowa.js" />
-->

<script>
jQuery(function($) {
    $('a.upload-method').click(function(e) {
        e.preventDefault();

        var $this = $(this);

        $this.parent().parent().find('li').removeClass('active');
        $this.parent().addClass('active');

        var type = this.get('data-type');

        $('#storage_type').val(type);
        $('.upload-method-box').css('display', 'none');
        $('#document-'+type+'-path-row').css('display', 'block');
    });

    $('.upload-method-box').css('display', 'none');
    $('a.upload-method').first().trigger('click');
});
</script>

<? if ($params->get('show_page_heading')): ?>
    <h1>
        <?= $params->get('page_heading'); ?>
    </h1>
<? endif; ?>

<div class="frontend-toolbar">
<?= @helper('com://admin/docman.template.helper.toolbar.render', array(
	'toolbar' => $this->getView()->getToolbar()
));?>
</div>

<form action="" method="post" class="-koowa-form" data-toolbar=".toolbar-list" enctype="multipart/form-data">
<div class="com_docman boxed">
<div class="row-fluid">
	<fieldset class="form-horizontal">
		<legend><?= @text('Details'); ?></legend>
		<div class="control-group">
			<label class="control-label"><?= @text('Title'); ?></label>
			<div class="controls">
				<input class="required" id="title_field" type="text" class="title" size="25" name="title" value="<?= $document->title; ?>" class="required" />
			</div>
			
		</div>
		<ul class="nav nav-tabs">
			<li>
				<a href="#" class="upload-method" data-type="file">
					<?= @text('Upload a file')?>
				</a>
			</li>
			<li>
				<a href="#" class="upload-method" data-type="remote">
					<?= @text('Submit a link')?>
				</a>
			</li>
		</ul>
		<input type="hidden" name="storage_type" id="storage_type" />
		<div class="control-group upload-method-box" id="document-remote-path-row" style="display: block">
                <label class="control-label"><?= @text('Submit a link'); ?></label>
                <div class="controls">
				<input class="validate-storage title" data-type="remote" id="storage_path_remote" type="text" size="25"
					name="storage_path_remote" value="<?= $document->storage_path; ?>" placeholder="http://" />
			</div>
		</div>
		<div class="control-group upload-method-box" id="document-file-path-row" style="display: block">
			<label class="control-label"><?= @text('Upload a file'); ?></label>
			<div class="controls">
				<div class="input-append">
					<input type="file" name="storage_path_file" />
				</div>
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><?= @text('Description'); ?></legend>
		<?= @editor(array(
			'name'    => 'description',
			'width'   => '100%', 'height' => '200',
			'rows'    => '20',
			'buttons' => null
		)); ?>
	</fieldset>
</div>
</div>

</form>