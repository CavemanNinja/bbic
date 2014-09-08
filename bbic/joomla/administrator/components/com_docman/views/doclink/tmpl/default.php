<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.bootstrap'); ?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
<script src="media://com_docman/js/doclink.js" />
<script src="media://system/js/mootree.js" />
<script src="media://com_docman/js/categories-tree.js" />
-->

<script>
window.addEvent('domready', function(){
	Doclink._ = {
		'empty_folder_text': '<?= @text('There are no published documents in this category.') ?>',
		'insert_category': '<?= @text('Insert category link'); ?>',
		'insert_document': '<?= @text('Insert document link'); ?>',
		'insert_menu': '<?= @text('Insert menu link'); ?>'
	};
	Doclink.editor = '<?= $editor ?>';
	Doclink.tree_theme = 'media://com_docman/images/mootree.png';
	Doclink.initialize();

    //Workaround for templates that incorrectly wrap &tmpl=component views breaking the layout
    document.getElement('.docman-container.doclink').getParents().setStyles({width: 'auto', padding: 0, margin: 0});

}); 
</script>

<div class="docman-container doclink">

<div id="documents-sidebar" class="fltlft" style="float: left; overflow: scroll; overflow-x: auto; height: 385px; width: 40%">
	<div class="sidebar-inner">
        <h3><?= @text('Menu Items')?></h3>
		<ul class="sidebar-nav pages">
		<? foreach ($pages as $page):
            $target = ($page->params->get('document_title_link') === 'download' && $page->params->get('download_in_blank_page')) ? 'blank' : '';
        ?>
			<li data-title="<?= $page->title; ?>" data-id="<?= $page->id; ?>"
                data-view="<?= $page->query['view']; ?>"
                data-target="<?= $target; ?>"
            >
				<a href="#"><?= $page->title; ?></a>
				<div class="categories">
	    		<?= @template('com://admin/docman.view.doclink.tree', array('categories' => $page->categories, 'id' => 'page-categories-'.$page->id)); ?>
	    		</div>
			</li>
		<? endforeach; ?>
		</ul>
    </div>
</div>

<div class="fltlft" style="float: left; width: 60%; overflow-y: scroll; height: 385px; " id="files-container">
	<div class="table-container">
		<table class="table table-striped table-condensed" id="document_list">
			<thead>
				<tr>
					<th>
						<?= @text('Title'); ?>
					</th>
					<th>
						<?= @text('Created date'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr class="initial-row">
					<td style="text-align: center" colspan="3">
						<?= @text('Please first select a menu item and then a category from the sidebar.')?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
	
</div>

<form class="form-horizontal" style="padding: 10px 0 0 0" id="properties">
	<input type="hidden" id="url" value="" />
	<div class="control-group">
		<label class="control-label"><?= @text('Link caption')?></label>
		<div class="controls">
			<div class="input-append">
			<input type="text" id="caption" value="" class="span4 input-append" /><button type="button" id="insert-image" class="btn btn-primary input-append"><?= @text('Go') ?></button>
		</div>
		</div>
	</div>
	
</form>