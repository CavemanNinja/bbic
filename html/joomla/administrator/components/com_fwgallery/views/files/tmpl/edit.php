<?php
/**
 * FW Gallery 3.1.0
 * @copyright (C) 2014 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JToolBarHelper::title(JText::_('FWG_IMAGE').'&nbsp;<small>['.JText::_($this->obj->id?'Edit':'New').']</small>', 'fwgallery-image.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel('files');

JHTML :: _('behavior.formvalidation');
$editor = JFactory::getEditor();
JHTML :: script('http://maps.google.com/maps/api/js?sensor=false');
$media_is_file = (in_array($this->obj->media, array('flv', 'mov', 'mp4', 'divx', 'avi')))?true:false;
$type_id = JFHelper :: getTypeId('video');
?>
<form action="index.php?option=com_fwgallery&amp;view=files" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_DETAILS'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_NAME'); ?> :
	            </td>
	            <td>
	                <input id="name" class="required inputbox" type="text" name="name" size="50" maxlength="100" value="<?php echo $this->escape($this->obj->name);?>" />
	            </td>
	        </tr>
	        <tr>
				<td class="key">
					<?php echo JText::_('FWG_USER'); ?>:
				</td>
				<td>
					<?php echo JHTML::_('select.genericlist', (array)$this->clients, 'user_id', 'class="required"', 'id', 'name', $this->obj->user_id?$this->obj->user_id:$this->user->id); ?>
				</td>
	        </tr>
	        <tr class="fwgallery_image_field">
	            <td class="key">
	                <?php echo JText::_('FWG_DEFAULT'); ?>:
	            </td>
	            <td>
	                <input id="selected" type="checkbox" name="selected" value="1"<?php echo $this->obj->selected?' checked':'';?> />
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_PUBLISHED'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
	                	<?php echo JHTML :: _('select.booleanlist', 'published', '', $this->obj->published); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_DATE'); ?>:
	            </td>
	            <td>
	                <?php echo JHTML::_('calendar', substr($this->obj->created?$this->obj->created:date('Y-m-d'), 0, 10), 'created', 'created', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
	            </td>
	        </tr>
<?php
if ($this->params->get('display_image_tags')) {
?>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_TAGS'); ?> :
	            </td>
	            <td>
	                <input id="tags" class="inputbox" type="text" name="tags" size="50" value="<?php echo $this->escape(implode(', ', (array)$this->obj->_tags));?>" />
	            </td>
	        </tr>
<?php
}
?>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_COPYRIGHT'); ?>:
	            </td>
	            <td>
	                <input id="copyright" class="inputbox" type="text" name="copyright" size="50" maxlength="100" value="<?php echo $this->escape($this->obj->copyright);?>" />
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_GALLERY'); ?>:
	            </td>
	            <td>
	                <?php echo JHTML :: _('fwGalleryCategory.getCategories', 'project_id', $this->obj->project_id, 'class="required"', $multiple=false, $first_option=''); ?>
	            </td>
	        </tr>
<?php
if ($this->params->get('enable_stock')) {
?>
			<tr>
				<td class="key"><?php echo JText :: _('FWG_STOCK'); ?></td>
				<td>
					<table>
						<thead>
							<tr>
								<th><?php echo JText :: _('FWG_WIDTH') ?></th>
								<th><?php echo JText :: _('FWG_HEIGHT') ?></th>
								<th><?php echo JText :: _('FWG_PRICE') ?></th>
								<th>&nbsp;</th>
							</tr>
						</thead>
						<tbody id="fwg-stock-table">
							<tr>
								<td><input type="text" name="width[]" value="" size="5" /></td>
								<td><input type="text" name="height[]" value="" size="5" /></td>
								<td><?php if ($this->params->get('currency_position') != 'after') echo $this->params->get('currency_label'); ?><input type="text" name="price[]" value="" size="5" /><?php if ($this->params->get('currency_position') == 'after') echo $this->params->get('currency_label'); ?></td>
								<td><button class="btn btn-primary" id="fwg-stock-add" type="button">+</button></td>
							</tr>
<?php
	if (!empty($this->obj->_stock)) foreach ($this->obj->_stock as $row) {
?>
							<tr>
								<td><input type="text" name="width[]" value="<?php echo $row->width; ?>" size="5" /></td>
								<td><input type="text" name="height[]" value="<?php echo $row->height; ?>" size="5" /></td>
								<td><?php if ($this->params->get('currency_position') != 'after') echo $this->params->get('currency_label'); ?><input type="text" name="price[]" value="<?php echo $row->price; ?>" size="5" /><?php if ($this->params->get('currency_position') == 'after') echo $this->params->get('currency_label'); ?></td>
								<td><button class="fwg-stock-remove btn btn-danger" type="button">-</button></td>
							</tr>
<?php
	}
?>
						</tbody>
					</table>
				</td>
			</tr>
<?php
}
?>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_FILE'); ?>:
	            </td>
	            <td>
                    <p><?php echo JText :: _('FWG_FILE_UPLOAD_SIZE_LIMIT').' '.ini_get('post_max_size'); ?></p>
					<?php echo JText :: _('FWG_FILE_TYPE') ?>: <?php echo JHTML :: _('select.genericlist', $this->types, 'type_id', '', 'id', 'name', $this->obj->type_id); ?>
					<div class="fwg-types" id="fwg-type-<?php echo $type_id; ?>"<?php if ($this->obj->_type_name != 'Video') { ?> style="display:none;"<?php } ?>>
						<?php echo JText :: _('FWG_VIDEO_TYPE'); ?>: <?php echo JHTML :: _('select.genericlist', $this->media, 'media', 'class="fwg-media" style="float:none;"', 'id', 'name', $this->obj->media, 'fwg-media'); ?>
						<div class="local"<?php if (!$media_is_file) { ?> style="display:none;"<?php } ?>>
							<?php echo JText :: _('FWG_VIDEO_FILENAME'); ?>: <input<?php if (!$media_is_file) { ?> disabled<?php } ?> style="float:none;" name="file" type="file" /><?php if ($this->obj->media_code and $this->obj->media and $media_is_file) { ?> (<?php echo $this->obj->media_code; ?>)<?php } ?><br/>
							<?php echo JText :: _('FWG_PREVIEW_FILENAME'); ?>: <input<?php if (!$media_is_file) { ?> disabled<?php } ?> style="float:none;" name="preview" type="file" /><?php if ($this->obj->filename and $this->obj->media and $media_is_file) { ?> (<?php echo $this->obj->filename; ?>)<?php } ?>
						</div>
						<div class="remote"<?php if ($media_is_file) { ?> style="display:none;"<?php } ?>>
							<?php echo JText :: _('FWG_VIDEO_ID'); ?>: <input type="text"<?php if ($media_is_file) { ?> disabled<?php } ?> style="float:none;" name="video" value="<?php if ($this->obj->media_code and $this->obj->media and !$media_is_file) echo $this->obj->media_code; ?>" />
						</div>
					</div>
					<div class="fwg-types" id="fwg-type-1"<?php if ($this->obj->type_id > 1 and $this->obj->_is_type_published) { ?> style="display:none;"<?php } ?>>
		                <img src="<?php echo JURI::root(true).'/'.JFHelper::getFileFilename($this->obj,'mid'); ?>"/><br/>
		                <?php echo JText::_('FWG_FILENAME').':'.$this->obj->filename; ?><br/>
		                <input id="filename" class="inputbox" type="file" name="filename"/>
					</div>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_DESCRIPTION'); ?>:
	            </td>
	            <td>
                    <?php echo $editor->display('descr',  $this->obj->descr, '600', '350', '75', '20', false); ?>
	            </td>
	        </tr>
			<tr>
				<td colspan="2">
					<strong><?php echo JText :: _('FWG_COORDINATES_HINT'); ?></strong>
					<table>
						<tr>
							<td><?php echo JText::_('FWG_ADDRESS_GEOHELPER'); ?>:</td>
							<td><input id="geolocation-address" class="inputbox" type="text" name="address" size="30" maxlength="100" /></td>
							<td><?php echo JText::_('FWG_LONGITUDE'); ?>:</td>
							<td><input id="longitude" class="inputbox" type="text" name="longitude" size="30" maxlength="100" value="<?php echo $this->escape($this->obj->longitude);?>" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><button type="button" id="geolocation-button" class="btn"><?php echo JText :: _('FWG_LOAD_GEOLOCATION'); ?></button></td>
							<td><?php echo JText::_('FWG_LATITUDE'); ?></td>
							<td><input id="latitude" class="inputbox" type="text" name="latitude" size="30" maxlength="100" value="<?php echo $this->escape($this->obj->latitude);?>" /></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><div id="fwg-hint-map" style="width: 50%; height: 300px;"></div></td>
			</tr>
        </table>
    </fieldset>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->obj->id; ?>" />
</form>
<script type="text/javascript">
window.addEvent('domready', function() {
	document.getElement('#fwg-stock-add').addEvent('click', function() {
		var row = this.parentNode.parentNode;
		var inputs = $(row).getElements('input');
		var height, width, price;
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].name == 'height[]') height = inputs[i];
			else if (inputs[i].name == 'width[]') width = inputs[i];
			else if (inputs[i].name == 'price[]') price = inputs[i];
		}
		if (!isNaN(height.value) && height.value > 0 && !isNaN(width.value) && width.value > 0 && !isNaN(price.value) && price.value > 0) {
			var row = new Element('tr');
			row.inject(document.getElement('#fwg-stock-table'))
			var col = new Element('td');
			col.inject(row);
			var input = new Element('input', {
				'type': 'text',
				'name': 'width[]',
				'value': width.value,
				'size': 5
			});
			input.inject(col);
			var col = new Element('td');
			col.inject(row);
			var input = new Element('input', {
				'type': 'text',
				'name': 'height[]',
				'value': height.value,
				'size': 5
			});
			input.inject(col);
			var col = new Element('td');
			col.inject(row);
<?php
if ($this->params->get('currency_position') != 'after') {
?>
			col.appendText('<?php echo $this->params->get('currency_label'); ?>');
<?php
}
?>
			var input = new Element('input', {
				'type': 'text',
				'name': 'price[]',
				'value': price.value,
				'size': 5
			});
			input.inject(col);
<?php
if ($this->params->get('currency_position') == 'after') {
?>
			col.appendText('<?php echo $this->params->get('currency_label'); ?>');
<?php
}
?>
			var col = new Element('td');
			col.inject(row);
			var input = new Element('button', {
				'type': 'button',
				'class': 'fwg-stock-remove btn btn-danger'
			});
			input.inject(col);
			input.innerHTML = '-';
			input.addEvent('click', function() {
				var row = this.parentNode.parentNode;
				row.parentNode.removeChild(row);
			});
			height.value = '';
			width.value = '';
			price.value = '';
			width.focus();
		} else alert('FWG_ALL_INPUTS_MUST_BE_FILLED');
	});
	document.getElements('#fwg-stock-table .fwg-stock-remove').each(function(el) {
		el.addEvent('click', function() {
			var row = this.parentNode.parentNode;
			row.parentNode.removeChild(row);
		});
	});
	document.getElement('#fwg-media').addEvent('click', fwg_check_media).addEvent('change', fwg_check_media);
	fwg_check_media();
<?php
if (!$this->obj->longitude) $this->obj->longitude = 0;
if (!$this->obj->latitude) $this->obj->latitude = 0;
?>
	var myLatlng = new google.maps.LatLng(<?php echo $this->obj->latitude; ?>, <?php echo $this->obj->longitude; ?>);
	var myOptions = {
	    zoom: 14,
	    center: myLatlng,
	    mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	var map = new google.maps.Map(document.getElementById('fwg-hint-map'), myOptions);
	var marker = new google.maps.Marker({
	      position: myLatlng,
	      map: map,
	      draggable: true
	});
	google.maps.event.addListener(marker, 'dragend', function(event) {
		var point = marker.getPosition();
		var form = document.getElement('#adminForm');
		form.longitude.value = point.lng().toFixed(6);
		form.latitude.value = point.lat().toFixed(6);
	});
	google.maps.event.addDomListener(window, "resize", function() {
		var center = map.getCenter();
		google.maps.event.trigger(map, "resize");
		map.setCenter(center);
	});
	
	document.getElement('#geolocation-button').addEvent('click', function() {
		var address = this.form.address;
		if (address.value == '') {
			address.focus();
			alert('<?php echo JText :: _('FWG_EMPTY_ADDRESS'); ?>');
		} else {
			geocoder = new google.maps.Geocoder();
			geocoder.geocode({
				'address': address.value
			}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					var geoloc = results[0].geometry.location;
					map.setCenter(geoloc);
					marker.setPosition(geoloc);
					google.maps.event.trigger(marker, "dragend");
				} else {
					alert("Geocode was not successful for the following reason: " + status);
				}
			});			
		}
	});

	if (document.getElement('#type_id')) {
		document.getElement('#type_id').addEvent('change', fwg_check_type).addEvent('click', fwg_check_type);
		fwg_check_type();
	}
});
function fwg_check_type() {
	var id = document.getElement('#type_id').value;
	document.getElements('.fwg-types').each(function(el) {
		if (el.id == 'fwg-type-'+id) el.setStyle('display', '');
		else el.setStyle('display', 'none');
	});
}
function fwg_check_media() {
	var media = document.getElement('#fwg-media').value;
	if (media == 'flv' || media == 'mov' || media == 'divx' || media == 'avi' || media == 'mp4') {
		document.getElement('#fwg-type-<?php echo $type_id; ?> .local').setStyle('display', '');
		document.getElement('#fwg-type-<?php echo $type_id; ?> .remote').setStyle('display', 'none');
		document.getElements('#fwg-type-<?php echo $type_id; ?> .local input').each(function(el) {
			el.removeAttribute('disabled');
		});
		document.getElement('#fwg-type-<?php echo $type_id; ?> .remote input').setAttribute('disabled', true);
	} else {
		document.getElement('#fwg-type-<?php echo $type_id; ?> .local').setStyle('display', 'none');
		document.getElement('#fwg-type-<?php echo $type_id; ?> .remote').setStyle('display', '');
		document.getElements('#fwg-type-<?php echo $type_id; ?> .local input').each(function(el) {
			el.setAttribute('disabled', true);
		});
		document.getElement('#fwg-type-<?php echo $type_id; ?> .remote input').removeAttribute('disabled');
	}
}
Joomla.submitbutton = function(task) {
	var form = document.getElement('#adminForm');
	if ((task == 'apply' || task == 'save') && !document.formvalidator.isValid(form)) {
		alert('<?php echo JText :: _('FWG_NOT_ALL_REQUIRED_FIELDS_ARE_FILLED', true); ?>');
	} else {
		form.task.value = task;
		form.submit();
	}
}
</script>