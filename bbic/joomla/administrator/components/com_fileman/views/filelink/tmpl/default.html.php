<?
/**
 * @package     FILEman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('_JEXEC') or die; ?>

<?= helper('bootstrap.load', array('class' => array('full_height'))); ?>

<ktml:content>

<script>
window.addEvent('domready', function() {
	var type = 'file',
		getFileLink = function() {
			var url = document.id('file-url').get('value'),
				str = '<a href="'+url+'" ',
				title = document.id('file-title').get('value'),
				text = document.id('file-text').get('value');
			if (!url || !text) {
				return false;
			}
			if (title) {
				str += 'title="'+title+'"';
			}
			if (!text) {
				text = url;
			}
			str += '>'+text+'</a>';

			return str;
		},
		getImageLink = function() {
			var src = document.id('image-url').get('value');
			if (!src) {
				return false;
			}
			var attrs = {};
			['align', 'alt', 'title'].each(function(id) {
				var value = document.id('image-'+id).get('value');
				if (value) {
					attrs[id] = value;
				}
			});

			var str = '<img src="'+src+'" ';
			var parts = [];
			Object.each(attrs, function(value, key) {
				parts.push(key+'="'+value+'"');
			});
			str += parts.join(' ')+' />';

			return str;
		};

	document.id('insert-link').addEvent('click', function(e) {
		e.stop();

		var link = type === 'file' ? getFileLink() : getImageLink();

		if (window.parent.jInsertEditorText && link) {
			window.parent.jInsertEditorText(link, Files.app.editor);
		}

		if (window.parent.SqueezeBox && link) {
			window.parent.SqueezeBox.close();
		}
	});

    document.id('insert-button-container').adopt(document.id('insert-form'));

	Files.app.grid.addEvent('clickImage', function(e) {
		var row = document.id(e.target).getParent('.files-node').retrieve('row');
		document.id('insert-form').setStyle('display', '');
		document.id('file-form').setStyle('display', 'none');
		document.id('image-form').setStyle('display', '');

		document.id('image-url').set('value', row.image);

		type = 'image';
	});
	Files.app.grid.addEvent('clickFile', function(e) {
		var row = document.id(e.target).getParent('.files-node').retrieve('row');
		document.id('insert-form').setStyle('display', '');
		document.id('file-form').setStyle('display', '');
		document.id('image-form').setStyle('display', 'none');

		document.id('file-url').set('value', row.baseurl+'/'+row.filepath);

		type = 'file';
	});

	if (window.parent.tinyMCE) {
		var text = window.parent.tinyMCE.activeEditor.selection.getContent({format:'raw'});
			if (text) {
			document.id('file-text').set('value', text);
			document.id('image-title').set('value', text);
		}
	}

	if(window.parent && window.parent != window && window.parent.SqueezeBox) {
		var modal = window.parent.SqueezeBox;

		document.id('insert-modal-cancel').addEvent('click', function(){
			modal.close();
		});
	} else {
		document.id('insert-modal-cancel').setStyle('display', 'none');
		document.getElement('.insert-or').setStyle('display', 'none');
	}
});
</script>

<form id="insert-form" style="display: none">
	<div id="file-form" style="display: none">
		<input type="hidden" id="file-url" value="" />
		<table class="properties" id="file-form">
			<tr>
				<td><label for="file-title"><?= translate('Link Title') ?></label></td>
			</tr>
			<tr>
				<td><input type="text" id="file-title" value="" /></td>
			</tr>
			<tr>
				<td><label for="file-text"><?= translate('Text') ?></label></td>
			</tr>
			<tr>
				<td><input type="text" id="file-text" value="" /></td>
			</tr>
		</table>
	</div>
	<div id="image-form" class="controls" style="display: none">
		<input type="hidden" id="image-url" value="" />
		<table class="properties" style="width:100%;">
			<tr>
				<td colspan="2"><label class="control-label" for="image-alt"><?= translate('Description') ?></label></td>
			</tr>
			<tr>
				<td colspan="2"><input type="text" id="image-alt" value="" class="input-block-level" /></td>
			</tr>
			<tr>
				<td colspan="2"><label for="image-title"><?= translate('Title') ?></label></td>
			</tr>
			<tr>
				<td colspan="2"><input type="text" id="image-title" value="" class="input-block-level" /></td>
			</tr>
			<tr>
				<td><label for="image-align"><?= translate('Align') ?></label></td>
			</tr>
			<tr>
				<td>
					<select size="1" id="image-align" title="Positioning of this image" class="input-block-level">
						<option value="" selected="selected"><?= translate('Not Set') ?></option>
						<option value="left"><?= translate('Left') ?></option>
						<option value="right"><?= translate('Right') ?></option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="buttons">
		<a id="insert-modal-cancel" href="#"><?= translate('Cancel') ?></a>
		<span class="insert-or"><?= translate('or') ?></span>
		<button type="button" id="insert-link" class="btn btn-primary"><?= translate('Insert') ?></button>
	</div>
</form>