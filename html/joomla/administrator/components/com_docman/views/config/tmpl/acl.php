<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<? 
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>

<form action="" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate">
	<fieldset>
		<div class="fltrt">
			<button>
				<?= @text('Apply');?>
			</button>
			<button onclick="window.parent.SqueezeBox.close();">
				<?= @text('Cancel');?>
			</button>
		</div>
		<div class="configuration" >
			<?= @text('Advanced Permissions') ?>
		</div>
	</fieldset>

	<?= @helper('access.rules'); ?>
</form>
