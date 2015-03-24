<?php /**
 * @package     DOCman
 * @copyright   Copyright (C) 2012 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?php echo $this->helper('bootstrap.load'); ?>
<?php echo $this->helper('behavior.koowa');?>


<?php // RSS feed ?>
<link href="<?php echo $this->route('format=rss');?>" rel="alternate" type="application/rss+xml" title="RSS 2.0" />


<div class="docman_table_layout docman_table_layout--filtered_table">

    <?php if ($params->get('show_page_heading')): ?>
    <h1>
        <?php echo $this->escape($params->get('page_heading')); ?>
    </h1>
    <?php endif; ?>

    <?php // Table | Import child template from documents view ?>
    <form action="" method="get" class="-koowa-grid">
        <?php echo $this->import('com://site/docman.documents.table.html', array(
            'documents' => $documents,
            'params'    => $params,
            'state'     => $this->parameters()
        ))?>

        <?php // Pagination ?>
        <?php if ($params->show_pagination !== '0' && $this->parameters()->total): ?>
            <?php echo $this->helper('paginator.pagination', array(
                'show_limit' => (bool) $params->show_document_sort_limit
            )) ?>
        <?php endif; ?>
    </form>

</div>