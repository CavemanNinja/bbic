<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<ul class="sidebar-nav" <?= isset($id) ? 'id="'.$id.'"' : ''; ?>>
<? foreach($categories as $category): ?>
	<li class="<?= $state->category == $category->id ? 'active' : ''; ?>" id="node<?= $category->id; ?>"
		data-id="<?= $category->id; ?>" 
		data-level="<?= $category->level ?>"
		data-title="<?= $category->title; ?>"
	>
		<a href="#">
			<?= $category->title; ?>
		</a>
	</li>
<? endforeach; ?>
</ul>