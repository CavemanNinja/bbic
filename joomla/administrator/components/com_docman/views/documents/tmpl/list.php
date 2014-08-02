<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<table class="adminlist cpanelmodule">
	<tr>
		<th><?= @text('Title'); ?></th>
		<th><?= @text('Created date'); ?></th>
		<th><?= @text('Published'); ?>
	</tr>
	<? if (!$total): ?>
	<tr>
		<td colspan="3"><?= @text('There are no documents'); ?></td>
	</tr>
	<? else:
	foreach ($documents as $document): ?>
	<tr>
		<td>
			<a href="<?= @route('view=document&id='.$document->id); ?>">
				<?= $document->title; ?>
			</a>
			<?= $document->published == '0' ? '('.@text('Unpublished').')' : ''; ?>
		</td>
		<td>
			<?= @date(array('date' => $document->created_on)); ?>
		</td>
		<td>
			<?= @helper('grid.enable', array('row' => $document)); ?>
		</td>
	</tr>
	<?
	endforeach;
	endif; ?>
</table>
