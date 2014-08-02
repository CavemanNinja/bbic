<?
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2013 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */
defined('KOOWA') or die; ?>

<?= @helper('behavior.bootstrap')?>
<?= @helper('behavior.mootools')?>

<!--
<script src="media://lib_koowa/js/koowa.js" />
-->
<script>
window.addEvent('domready', function() {
	var requests = new Chain();
	var request = new Request.JSON({
		url: 'index.php?option=com_docman&view=document&format=json',
		urlEncoded: true,
		onComplete: function() {
			requests.callChain();
		},
		onSuccess: function(response, text) {
			if (this.status == 201) {
				var item = response.data,
					form = document.getElement('form.document-form[data-path='+item.storage_path+']');
				form.empty();
				new Element('p').adopt(
					new Element('a', {
						'href': 'index.php?option=com_docman&view=document&id='+item.id,
						// TODO: translate this
						'text': 'Continue editing this document: '+item.title,
						'target': '_blank'
					})
				).inject(form);
			}
		}
	});

	var createQueryString = function(form) {
		var queryString = {};
		form.getElements('input, select, textarea', true).each(function(el){
			if (!el.name || el.disabled || el.type == 'submit' || el.type == 'reset' || el.type == 'file') return;
			var value = (el.tagName.toLowerCase() == 'select') ? Element.getSelected(el).map(function(opt){
				return opt.value;
			}) : ((el.type == 'radio' || el.type == 'checkbox') && !el.checked) ? null : el.value;

            var splat = typeof $splat === 'undefined' ? Array.from : $splat;
			splat(value).each(function(val){
				if (typeof val != 'undefined') queryString[el.name] = encodeURIComponent(val);
			});
		});

		return queryString;
	};
	var getQueryString = function(form) {
		var batches = createQueryString(document.id('document-batch'));
		var values = createQueryString(form);

		['enabled', 'docman_category_id'].each(function(key) {
			if (values[key] == -1 || values[key] === '') {
				values[key] = batches[key];
			}
		});

		values['storage_type'] = 'file';

		return values;
	};

	Object.append($('document-batch').retrieve('controller'), {
		'_actionApply': function(controller, data, novalidate) {
			var batches = createQueryString(document.id('document-batch'));
			var select = document.id('document-batch').getElement('select[name=docman_category_id]');

			if (!select.get('value')) {
				select.getParent().getParent().addClass('error');
				return;	
			}
						
			$$('form.document-form').each(function(form) {
				var query = createQueryString(form);
				var values = [];
				Object.each(getQueryString(form), function(value, key) {
					values.push(key+'='+value);
				});
				requests.chain(function() {
					request.send(values.join('&'));
				});
			});
			requests.chain(function() {
				document.id('toolbar-apply').getElement('a').addClass('disabled');
			});
			requests.callChain();
		},
		'_actionBack': function(controller, data, novalidate) {
			if (!$$('form.document-form input[name=title]').length || confirm('<?= @text('You will lose all unsaved data. Are you sure?') ?>')) {
				window.location = '<?= JRoute::_('index.php?option=com_docman&view=files', false); ?>';
			}
		}
	});
	

	$$('#document-list .cancel').addEvent('click', function(e) {
		e.stop();
		var el = e.target.getParent('.document-form');
		var fx = new Fx.Morph(el);
		fx.addEvent('complete', function() {
			el.dispose();
		});
		fx.start({opacity: 0, height: 0});
	});
});
</script>
<div class="docman-container">

<div class="spacing">
<form class="form-horizontal -koowa-form" id="document-batch" data-toolbar=".toolbar-list">
	<fieldset>
	<legend><?= @text('Batch Values'); ?></legend>
	<div class="control-group">
		<label class="control-label"><?= @text('Category');?>:</label> 
		<div class="controls">
		<?= @helper('listbox.categories', array(
				'required' => true,
				'name' => 'docman_category_id'
			))?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?= @text('Status');?>:</label>
		<div class="controls">
			 <?= @helper('select.booleanlist', array(
	        	'name' => 'enabled', 
	        	'selected' => 1,
	        	'true' => 'Published',
	        	'false' => 'Unpublished'
	          )); ?>
		</div>
	</div>
	</fieldset>
</form>
<? if (!is_array($state->paths)): ?>
	<?= @text('You did not select any files. Please go back and select some files first.')?>
<? else: ?>
<legend><?= @text('Documents'); ?></legend>
<div id="document-list" class="row">

<? $i=0;foreach ((array)$state->paths as $path): ?>
<form class="form-vertical document-form span3" method="post" id="form<?= $i?>" data-path="<?= $path ?>">
    <div style="text-align: right">
    	<button class="cancel btn btn-mini"><i class="icon icon-minus-sign"></i></button>
    </div>

	<div class="control-group">
		<label class="control-label"><?= @text('File name');?>:</label>
		<div class="controls">
			<input class="disabled" type="text" value="<?= $path; ?>" disabled/>
		</div>
	</div>
	
	<div class="control-group">
		<label class="control-label"><?= @text('Title');?>:</label>
		<div class="controls">
			<input type="text" name="title" value="<?= @helper('string.humanize', array(
				'string' => $path, 'strip_extension' => true)); ?>" />
		</div>
	</div>
		
	<div class="control-group">
		<label class="control-label"><?= @text('Description');?>:</label>
		<div class="controls">
			<textarea style="resize:vertical;" name="description"></textarea>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label"><?= @text('Category');?>:</label>
		<div class="controls">
			<?= @helper('listbox.categories', array(
				'name' => 'docman_category_id',
                'selected' => '',
                'attribs' => array('data-placeholder' => @text('- Use Batch Value -'))
			))?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?= @text('Status');?>:</label>
		<div class="controls">
			<?= @helper('select.optionlist', array(
				'name' => 'enabled',
				'selected' => -1,
				'options' => array(
					array('value' => -1, 'text' => @text('- Use Batch Value -')),
					array('value' => 1, 'text' => @text('Published')),
					array('value' => 0, 'text' => @text('Unpublished'))
				)
			))?>
		</div>
	</div>
    <input type="hidden" name="storage_path" value="<?= $path; ?>" />

    <input type="hidden" name="automatic_thumbnail" value="1" />
</form>
<? $i++;endforeach; ?>
</div>
</div>
<? endif; ?>
</div>