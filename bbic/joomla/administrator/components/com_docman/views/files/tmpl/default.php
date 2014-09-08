<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap', array('javascript' => array('dropdown'))); ?>

<script>
(function() {
var translations = {
	'Select files from your computer': <?= json_encode(@text('Select files from your computer')); ?>,
	'Uploaded successfully!': <?= json_encode(@text('Uploaded successfully!')); ?>,
	'Unknown error': <?= json_encode(@text('Unknown error')); ?>,
	'An error occurred: ': <?= json_encode(@text('An error occurred: ')); ?>,
	'An error occurred with status code: ': <?= json_encode(@text('An error occurred with status code: ')); ?>,
	'All Files': <?= json_encode(@text('All Files')); ?>,
	'An error occurred during request': <?= json_encode(@text('An error occurred during request')); ?>
};

Files._ = function(string) {
	if (translations[string]) {
		return translations[string];
	}

	return string; 
};
})();
</script>

<?= $app ?>
<script inline src="media://com_docman/js/files.js" type="text/javascript"></script>


<textarea style="display: none" id="documents_list">
<div>
<div class="preview extension-[%=metadata.extension%]">
[% var url = Files.app.createRoute({option: 'com_docman', view: 'file', format: 'raw', name: path}); %]
    [% if (typeof image !== 'undefined') {
        var width = metadata.image.width,
        height = metadata.image.height,
        ratio = 200 / (width > height ? width : height); %]
        <img src="[%=url%]" style="
             width: [%=Math.min(ratio*width, width)%]px;
             height: [%=Math.min(ratio*height, height)%]px
         " alt="[%=name%]" border="0" />
    [% } else { %]
        <img src="media://com_files/images/document-64.png" width="64" height="64" alt="[%=name%]" border="0" />
    [% } %]

    <div class="btn-toolbar">
        [% if (typeof image !== 'undefined') { %]
        <a class="btn btn-mini" href="[%=url%]" target="_blank">
            <i class="icon-eye-open"></i> <?= @text('View'); ?>
        </a>
        [% } else { %]
        <a class="btn btn-mini" href="[%=url%]" target="_blank" download="[%=name%]">
            <i class="icon-download"></i> <?= @text('Download'); ?>
        </a>
        [% } %]
    </div>
</div>
<hr />
<div class="details">
    <table class="table table-condensed parameters">
        <tbody>
            <tr>
                <td class="detail-label"><?= @text('Name'); ?></td>
                <td>[%=name%]</td>
            </tr>
            <tr>
                <td class="detail-label"><?= @text('Size'); ?></td>
                <td>[%=size.humanize()%]</td>
            </tr>
            <!--<tr>
                <td class="detail-label"><?= @text('Where'); ?></td>
                <td>[%path%]</td>
            </tr>-->
            <tr>
                <td class="detail-label"><?= @text('Modified'); ?></td>
                <td>[%=getModifiedDate(true)%]</td>
            </tr>
        </tbody>
    </table>
</div>
[% if (documents.length) { %]
<hr class="last" />
<h3><?= @text('Attached Documents') ?></h3>
<table class="table table-condensed table-bordered documents">
    <tbody>
        [% for (var i = 0; i < documents.length; i++) { var document = documents[i].data; %]
        <tr>
            <td>
                <a class="document-link" href="#" data-id="[%=document.id%]">
                    [%=document.title%]
                </a> <?= @text('in')?> <em>[%=document.category_title%]</em>
            </td>
        </tr>
        [% } %]
    </tbody>
</table>
[% } %]
</div>
</textarea>