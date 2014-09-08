<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.jquery'); ?>
<?= @helper('behavior.bootstrap', array('javascript' => array('affix'))); ?>
<?= @helper('behavior.mootools'); ?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
<script src="media://system/js/mootree.js" />
<script src="media://com_docman/js/categories-tree.js" />
<script src="media://com_docman/js/toolbar.js" />
<script src="media://com_docman/js/sidebar.js" />
<script src="media://com_docman/js/chromatable.js" />
-->

<script>
jQuery(function($){
    <? if (version_compare(JVERSION, '3.0', 'ge')): ?>
    //Quick j3 sidebar layout fix
    $('#submenu').prependTo('#documents-sidebar .sidebar-inner').addClass('docman-main-nav');
    <? endif ?>

    new DOCman.CategoriesTree({
        div: 'categories-tree',
        adopt: 'categories-tree-html',
		theme: 'media://com_docman/images/mootree.png',
		category: <?= $state->category ? $state->category : 0?>,
		root: {
		    text: <?= json_encode(@text('All Categories')); ?>,
			id: 0,
		    data: {
			    id: ''
		    }
	    },
		onClick: function(node) {
			var uri = new URI();
			window.location = uri.setData('category', node.data.id).toString();
		},
		onAdopt: function(id) {
            node = this.get(this.options.category);

            if (node) {
                this.select(node, true);
                node.toggle(false, true);
            } else {
                this.root.select(true);
            }

            //Scroll to selected category in the sidebar, so the user is aware that there's an active category
            try {
                var fx = new Fx.Scroll(document.id('categories-tree')),
                    selected = document.id('categories-tree').getElement('.mooTree_selected'),
                    what = 'toElementEdge' in fx ? 'toElementEdge' : 'toElement';
                fx[what](selected);
            } catch(Ex) {}
		}
    });
    var callback = false;
    if(jQuery('.navbar').length) {
        callback = function(){
            this.options.offset.top = this.options.offset.bottom = 0;
            if(jQuery('.navbar-fixed-top').css('position') === 'fixed') {
                this.options.offset.top += jQuery('.navbar.navbar-fixed-top').outerHeight();
            }
            //Hacky but thanks to how the subhead js logic in j!3.0 works we need to do it this way
            if(jQuery('.subhead-collapse .subhead').length === 1) {
                var test = jQuery('<div/>', {'class': 'subhead subhead-fixed'}).appendTo(document.body);
                if(test.css('position') === 'fixed') {
                    this.options.offset.top += jQuery('.subhead-collapse .subhead').outerHeight();
                }
                test.remove();
            }
            if(jQuery('.navbar-fixed-bottom').is(':visible')) {
                this.options.offset.bottom += jQuery('.navbar-fixed-bottom').outerHeight();
            }
            //console.warn('booooo', jQuery('.navbar.navbar-fixed.top'), jQuery('.navbar.navbar-fixed.top').css('position'));
        }
    }
    new Koowa.Sidebar({
        sidebar: '#documents-sidebar',
        target: '#categories-tree',
        affix: true,
        minHeight: 100,
        offset: {
            callback: callback
        }
    });

    /*//@TODO Chromatable support!
    var table = document.getElement('.docman-container .-koowa-grid > .table'),
        height = window.getHeight(),
        coord = document.getElement('.docman-container').getCoordinates();
    var offset = document.html.scrollHeight - coord.bottom;
    height = Math.max(height - table.getCoordinates().top - offset -2, 400);
    table.chromatable({height: height});
    */

    document.id('search_clear').addEvent('click', function(e){
        var day_range = document.id('day_range');
        new Element('input', {name: 'day_range', type: 'hidden'}).inject(this.form, 'top');
        day_range.value = '';
        document.id('search_date').value = '';
    });
});
</script>

<? if (count($categories) === 0): ?>
<div class="alert alert-info alert-block">
    <p><?= @text('It seems like you don\'t have any categories yet. You first need to create one in the <a href="%link%">category manager</a> before adding documents.', array(
        '%link%' => @route('option=com_docman&view=categories') 
    )); ?>
    </p>
    <p>
        <a class="btn" href="<?= @route('option=com_docman&view=categories') ?>">
            <i class="icon-folder-open"></i>
            <?= @text('Go to Category Manager')?>
        </a>
    </p>
</div>
<script>
jQuery(function($){
    $('#toolbar-new a').addClass('disabled').bind('click', function(){ return false; });
});
</script>
<? endif; ?>

<div class="docman-container">
<div id="documents-sidebar" class="fltlft" style="width: 260px">
	<div class="sidebar-inner">
        <h3><?= @text('Favorites'); ?></h3>
        <ul class="sidebar-nav favorites">
            <li class="<?= $state->created_by == $user->id ? 'active' : ''; ?>">
                <a href="<?= @route($state->created_by == 0 ? '&sort=&direction=&created_by=&created_by='.$user->id : '&sort=&direction=&created_by=&created_by=') ?>">
                    <?= @text('My Documents') ?>
                </a>
            </li>
            <li class="<?= $state->sort === 'created_on' && $state->direction === 'desc' ? 'active' : ''; ?>">
                <a href="<?= @route($state->sort === 'created_on' && $state->direction === 'desc' ? '&sort=&direction=&created_by=' : '&sort=created_on&direction=desc&created_by=') ?>">
                    <?= @text('Recently Added') ?>
                </a>
            </li>
            <li class="<?= $state->sort === 'modified_on' && $state->direction === 'desc' ? 'active' : ''; ?>">
                <a href="<?= @route($state->sort === 'modified_on' && $state->direction === 'desc' ? '&sort=&direction=&created_by=' : '&sort=modified_on&direction=desc&created_by=') ?>">
                    <?= @text('Recently Edited') ?>
                </a>
            </li>
        </ul>
		<h3><?= @text('Categories'); ?></h3>
	    <div id="categories-tree"></div>
	    <?= @template('com://admin/docman.view.documents.tree', array('documents' => $documents, 'id' => 'categories-tree-html')); ?>
        <div class="documents-filters">
            <form action="" method="get" class="-koowa-grid" data-toolbar=".toolbar-list">
                <h3><?= @text('Find documents by date') ?></h3>
                <div class="controls find-by-date form-inline">
                    <label for="day_range"><?= @text('Within') ?></label>
                    <div style="display: inline-block">
                    <?= @helper('listbox.day_range') ?>
                        <div style="display: inline-block">
                            <label for="search_date"><?= @text('of') ?></label>
                            <?= @helper('behavior.calendar', array(
                                'name' => 'search_date',
                                'id'   => 'search_date',
                                'format' => '%Y-%m-%d',
                                'value' => $state->search_date,
                                'attribs' => array('placeholder' => date('Y-m-d'))
                            )) ?>
                        </div>
                    </div>
                </div>
                <div class="controls">
                    <button id="search_submit" class="btn btn-primary"><i class="icon-search icon-white"></i><!--<?= @text('Find')?>-->&zwnj;</button>
                    <button id="search_clear" class="btn"><?= @text('Clear'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="fltlft" style="margin-left: 260px;float:none">
 <form action="" method="get" class="-koowa-grid" data-toolbar=".toolbar-list">
    <div class="scopebar">
        <div class="scopebar-group hidden-tablet hidden-phone">
            <a class="<?= is_null($state->enabled) && is_null($state->status) && is_null($state->access) ? 'active' : ''; ?>" href="<?= @route('&enabled=&access=&search=&status=' ) ?>">
                <?= @text('All') ?>
            </a>
        </div>
        <div class="scopebar-group last hidden-tablet hidden-phone">
            <a class="<?= $state->enabled === 0 ? 'active' : ''; ?>" href="<?= @route($state->enabled === 0 ? '&enabled=&status=' : '&enabled=0&status=' ) ?>">
                <?= @text('Unpublished') ?>
            </a>
            <a class="<?= $state->status === 'published' ? 'active' : ''; ?>" href="<?= @route($state->status === 'published' ? '&enabled=&status=' : '&enabled=1&status=published' ) ?>">
                <?= @text('Published') ?>
            </a>
            <a class="<?= $state->status === 'pending' ? 'active' : ''; ?>" href="<?= @route($state->status === 'pending' ? '&enabled=&status=' : '&enabled=1&status=pending' ) ?>">
                <?= @text('Pending') ?>
            </a>
            <a class="<?= $state->status === 'expired' ? 'active' : ''; ?>" href="<?= @route($state->status === 'expired' ? '&enabled=&status=' : '&enabled=1&status=expired' ) ?>">
                <?= @text('Expired') ?>
            </a>
        </div>
        <!--<div class="scopebar-group last">
            <?= @helper('listbox.access'); ?>
        </div>-->
        <div class="scopebar-search">
            <?= @helper('grid.search') ?>
        </div>
    </div>
<table class="table table-striped">
	<thead>
		<tr>
			<th style="text-align: center;" width="1">
				<?= @helper('grid.checkall')?>
			</th>
			<th>
				<?= @helper('grid.sort', array('column' => 'title', 'title' => 'Title')); ?>
			</th>
            <!--<th width="5%">
                <?= @helper('grid.sort', array('column' => 'storage_path', 'title' => 'Location')); ?>
            </th>-->
			<th width="5%">
				<?= @helper('grid.sort', array('column' => 'enabled', 'title' => 'Status')); ?>
			</th>
            <th width="5%">
                <?= @helper('grid.sort', array('column' => 'access', 'title' => 'Access')); ?>
            </th>
			<th width="5%">
				<?= @helper('grid.sort', array('column' => 'created_by', 'title' => 'Owner')); ?>
			</th>
            <th width="5%">
                <?= @helper('grid.sort', array('column' => 'created_on', 'title' => 'Date')); ?>
            </th>
            <? if(!$state->category) : ?>
            <th width="10%">
                <?= @helper('grid.sort', array('column' => 'docman_category_id', 'title' => 'Category')); ?>
            </th>
            <? endif ?>
		</tr>
	</thead>
    <? if (count($documents)): ?>
    <tfoot>
		<tr>
			<td colspan="8">
				<?= @helper('paginator.pagination', array('total' => $total)) ?>
			</td>
		</tr>
	</tfoot>
    <? endif; ?>
	<tbody>
		<? foreach ($documents as $document):
			$document->isAclable();
		?>
		<tr>
			<td style="text-align: center;">
				<?= @helper('grid.checkbox', array('row' => $document))?>
			</td>
			<td style="padding-top: 2px; padding-bottom: 2px; white-space: normal;">
				<a href="<?= @route('view=document&id='.$document->id); ?>">
					<?= $document->title; ?>
				</a>
                <br />
                <? if ($document->storage_type == 'remote') : ?>
                <? $location = $document->storage_path; ?>
                <? elseif ($document->storage_type == 'file') : ?>
                <? $location = $document->storage_path; ?>
                <? if ($document->size): ?>
                    <? $location .= ' - '.@helper('string.bytes2text', array('bytes' => $document->size, 'abbreviate' => true)); ?>
                    <? endif; ?>
                <? endif ?>
                <small title="<?= @escape($location) ?>"><?= $location ?></small>
			</td>
			<td>
                <?= @helper('grid.state', array('row' => $document, 'clickable' => $document->canPerform('edit'))) ?>
			</td>
            <td>
                <? $access_level = $document->access_title ?>
                <? if ($document->access_raw == -1): ?>
                <? $access_level .= ' (' . @text('Inherited') . ')' ?>
                <? endif ?>
                <span title="<?= @escape($access_level) ?>" style="
                    display: inline-block;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    max-width: 100px;
                "><?= $access_level ?></span>
            </td>
            <td>
                <?= $document->created_by_name; ?>
            </td>
			<td>
				<?= @date(array('date' => $document->created_on, 'format' => '%d %b %Y')); ?>
			</td>
            <? if(!$state->category) : ?>
            <td>
                <span title="<?= @escape($document->category_title) ?>" style="
                    display: inline-block;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    max-width: 140px;
                "><?= $document->category_title ?></span>
            </td>
            <? endif ?>
		</tr>
		<? endforeach; ?>

		<? if (!count($documents)) : ?>
		<tr>
			<td colspan="8" align="center" style="text-align: center;">
				<?= @text('No documents found.') ?>
			</td>
		</tr>
		<? endif; ?>
	</tbody>
</table>
</form>
</div>
</div>