<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap', array('namespace' => false)); ?>
<?= @helper('behavior.mootools'); ?>

<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.validator'); ?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
-->

<script>
// Remove and add inherit option to access list based on the parent category
jQuery(function($) {
	var access    = $('#access_raw'),
		category  = $('#parent_id'),
        inherit   = access.children('option').first(),
        access_box = $('.current-access span').first(),
        default_access = <?= json_encode($default_access->title) ?>;
        use_default = <?= json_encode(@text('- Use default -')) ?>,
        use_inherit = inherit.text(),
        change_category = function(e) {
        	inherit.text(e.val ? use_inherit : use_default);

        	change_access();
    	},
    	change_access = function() {
			var value = access.val(),
				catid = category.val();

			if (value == -1) {
				if (catid) {
					$.getJSON('?option=com_docman&view=category&format=json&id='+catid, function(data) {
						$('.current-access').css('display', 'block');
						access_box.text(data.data.access_title);
					});
				}
				else {
					$('.current-access').css('display', 'block');
					access_box.text(default_access);
				}
			}
			else {
				$('.current-access').css('display', 'none');
				access_box.text('');
			}		
		};
    	
    change_category({val: category.val()});
    
    category.on('change', change_category);
    access.on('change', change_access);
});
</script>

<form action="" method="post" class="-koowa-form" data-toolbar=".toolbar-list">
<div class="com_docman boxed" style="padding: 20px;">
<div class="row-fluid">	
	<div class="span6">
		<fieldset class="form-horizontal">
			<legend><?= @text('Details') ?></legend>
		    <div class="control-group">
				<label class="control-label" for="name"><?= @text('Title') ?></label>
				<div class="controls">
					<input class="required" type="text" name="title" size="32" maxlength="255" value="<?= $category->title ?>" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="name"><?= @text('Parent Category') ?></label>
				<div class="controls">
					<?= @helper('listbox.categories', array(
					    'deselect' => true,
						'check_access' => true,
						'name' => 'parent_id',
					    'attribs' => array('id' => 'parent_id'),
						'selected' => $parent ? $parent->id : null, 
						'ignore' => $category->id ? array_merge($category->getDescendants()->getColumn('id'), array($category->id)) : array()
					)) ?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="enabled"><?= @text('Status'); ?></label>
	        	<div class="controls">
	        	    <?= @helper('select.booleanlist', array(
	        	    	'name' => 'enabled', 
	        	    	'selected' => $category->enabled,
	        	    	'true' => 'Published',
	        	    	'false' => 'Unpublished'
	        	    )); ?>
	        	</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="name"><?= @text('Alias') ?></label>
				<div class="controls">
					<input type="text" name="slug" size="32" maxlength="255" value="<?= $category->slug ?>" />
				</div>
			</div>
			<div class="control-group">
			    <label class="control-label" for="enabled"><?= @text('Access'); ?></label>
    			<div class="controls">
    			    <?= @helper('listbox.access', array(
    			        'name' => 'access_raw', 
    			        'attribs' => array('id' => 'access_raw'),
    			        'selected' => $category->access_raw,
    			        'prompt' => '- '.@text('Inherit from parent category').' -',
    			        'inheritable' => true
    			    )); ?>	

    			    <div style="clear: both"></div>
    			    <? 
    			    $default = '<span></span>';
    			    if (!$category->isNew() && $category->access_raw == -1):
    			    	$default = '<span>'.$category->access_title.'</span>';
    			    endif;
    			    ?>
    			    <p class="help-block current-access">
    			    	<?= @text('Calculated as %access%', array('%access%' => $default)); ?>
    			    </p>
    			</div>
			</div>
            <div class="control-group">
                <label class="control-label"><?= @text('Owner'); ?></label>
                <div class="controls">
                    <?= @helper('listbox.users', array(
                        'name' => 'created_by',
                        'selected' => $category->created_by ? $category->created_by : JFactory::getUser()->id,
                        'deselect' => false,
                        'attribs' => array('class' => 'select2-listbox'),
                        'behaviors' => array('select2' => array('element' => '.select2-listbox'))
                    )) ?>
                </div>
            </div>
			<div class="control-group">
    			<label class="control-label"><?= @text('Image'); ?></label>
    			<div class="controls">
                    <?= @helper('behavior.thumbnail', array(
                        'enable_automatic_thumbnail' => false,
                        'value' => $category->image,
                        'name'  => 'image',
                        'id'  => 'image'
                    )) ?>
    			</div>
    		</div>
			<div class="control-group">
    			<label class="control-label"><?= @text('Icon'); ?></label>
    			<div class="controls">
    				<?= @helper('modal.icon', array(
    					'name'  => 'params[icon]',
    				    'id' => 'params_icon',
    					'value' => empty($category->params->icon) ? 'folder.png': $category->params->icon,
    					'link'  => @route('option=com_docman&view=files&layout=select_icon&tmpl=component&container=docman-icons&types[]=image'),
    					'link_selector' => 'modal-button'
    				))?>
    			</div>
    		</div>
			<? if ($category->isAclable() && $category->getPermissions()->canAdmin()): ?>
            <script>
                jQuery(function($){
                    $('#advanced-permissions-toggle').on('click', function(e){
                        e.preventDefault();
                        $('#advanced-permissions').toggleClass('hide');
                    });
                });
            </script>
			<div class="control-group">
				<label class="control-label"><?= @text('Advanced Permissions');?></label>
				<div class="controls">
					<a class="btn" id="advanced-permissions-toggle" href="#advanced-permissions">
						<?= @text('Show category permissions')?>
					</a>
				</div>
			</div>
			<? endif; ?>
		</fieldset>
	</div>

	<div class="span6">
    	<fieldset>
    		<legend><?= @text('Description'); ?></legend>
    			<?= @editor(array(
    				'name' => 'description',
    			    'id' => 'description',
    				'width' => '100%', 
    				'height' => '391', 
    				'cols' => '100', 
    				'rows' => '20', 
    				'buttons' => array('pagebreak')
    			)); ?>
    	</fieldset>
    </div>
    
</div>
</div>
<? if ($category->isAclable() && $category->getPermissions()->canAdmin()): ?>
<div class="hide well" id="advanced-permissions" style="width-100">
<?= @helper('access.rules', array('section' => 'category', 'asset' => $category->getAssetName())); ?>
</div>
<? endif; ?>
</form>