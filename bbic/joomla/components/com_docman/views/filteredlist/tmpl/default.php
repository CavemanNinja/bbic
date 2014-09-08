<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap'); ?>
<?= @helper('behavior.mootools');?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
-->

<? if ($params->get('show_page_heading')): ?>
    <h1>
        <?= $params->get('page_heading'); ?>
    </h1>
<? endif; ?>

<form action="<?= @route(''); ?>" method="get" class="-koowa-grid">
	<?= @template('com://site/docman.view.documents.list', array('documents' => $documents, 'params' => $params))?>

	<? if ($params->show_pagination !== '0' && $total): ?>
		<?= @helper('paginator.pagination', array_merge(array('total' => $total, 'show_limit' => !$params->limit), $state->getData())) ?>
	<? endif; ?>
</form>