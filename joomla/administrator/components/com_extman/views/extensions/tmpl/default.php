<?
/**
 * @package     EXTman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die; ?>

<?= @helper('behavior.bootstrap'); ?>
<?= @helper('behavior.mootools'); ?>

<script src="media://lib_koowa/js/koowa.js" />
<script>
window.addEvent('domready', function() {
    document.id('installer-form').retrieve('controller').addEvent('before.delete', function(data) {
        return confirm(<?= json_encode(@text('Uninstalling this extension will remove all its data and settings. Do you want to proceed?')); ?>);
    });
    var radios = document.id('installer-form').getElements('tbody input[type=radio]');
    document.id('installer-form').getElements('tr').each(function(tr){
        var radio = tr.getElement('input[type=radio]');
        tr.addEvent('click', function(){
        	if (radio) {
            	radios.set('checked', false);
            	radios.fireEvent('change');

	            radio.set('checked', true);
	            radio.fireEvent('change');
            }
        });

    });
});
</script>

<style>
.toolbar-list a.disabled {
  color: gray;
  font-weight: normal;
}
.toolbar-list .disabled span {
  background-position: bottom;
}
</style>

<div class="-installer-grid">
<form action="" method="get" class="-koowa-grid" id="installer-form">
    <table class="table table-striped table-hover">
    	<thead>
    		<tr>
    			<th class="title" width="20px"></th>
    			<th class="title" nowrap="nowrap">
    			    <?= @text('Currently Installed') ?>
    			</th>
    			<th class="title" width="10%" align="center">
    			    <?= @text('Version') ?>
    			</th>
    		</tr>
    	</thead>
    	<tbody>
    	<? foreach($extensions as $extension): ?>
    		<tr>
    			<td align="center">
    				<input type="radio" name="id[]" value="<?= $extension->id ?>" class="-koowa-grid-checkbox" />
    			</td>
    			<td>
    				<?= @translateComponentName($extension->name); ?>
    			</td>
    			<td align="center">
    			    <?= $extension->version ?>
    			</td>
    		</tr>
    	<? endforeach ?>
    	</tbody>
    </table>
    </form>
</div>