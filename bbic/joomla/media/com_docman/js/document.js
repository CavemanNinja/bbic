
if(Form && Form.Validator) {
	Form.Validator.add('validate-storage', {
		errorMsg: Form.Validator.getMsg("required"),
		test: function(field){
			var storage_type = document.id('storage_type').get('value'),
			    type = field.get('data-type');
	
			if (storage_type === type) {
				return !!field.get('value');
			} else {
				return true;
			}
		}
	});

    if(Form && Form.Validator) {
        Form.Validator.add('validate-stream-wrapper', {
            // TODO: translate this
            errorMsg: 'Invalid remote link. This link type is not supported by your server.',
            test: function(field){
                var value = field.get('value'),
                    streams = jQuery(field).data('streams'),
                    scheme = null,
                    matches = value.match(/^([a-zA-Z0-9\-]+):\/\//);

                if (matches) {
                    scheme = matches[1];
                }

                // If scheme is in the array it will not be redirected by browser
                // Therefore we need to check if it's enabled
                if (scheme && typeof streams[scheme] !== 'undefined') {
                    return streams[scheme];
                }

                return true;
            }
        });
    }
}

jQuery(function($) {
	var storage_type = $('#storage_type'),
		remote_row   = $('#document-remote-path-row'),
		file_row     = $('#document-file-path-row'),
		initial      = storage_type.val() == 'remote' ? remote_row : file_row,
        checkbox     = $('#automatic_thumbnail input'),
        thumbnail_extensions     = ['jpg', 'jpeg', 'gif', 'png', 'bmp'],
        prev_automatic_thumbnail = checkbox.prop('checked'),
        container   = $('#automatic_thumbnail').closest('.image-picker'),
        toggle_thumbnail = function(toggle){
            if (toggle === true) {
                if (prev_automatic_thumbnail) {
                    $('#image').val($('#automatic_thumbnail').data('automatic_thumbnail_image')).trigger('change');
                    $('#thumbnail-delete-image').removeClass('disabled');
                    checkbox.prop('checked', true).trigger('change');
                }

                $('#automatic_thumbnail').show();

            } else {
                // auto generate doesn't work with remote files
                // uncheck automatic thumbnail

                prev_automatic_thumbnail = checkbox.prop('checked');
                if(prev_automatic_thumbnail) {
                    $('#image').val('').trigger('change');
                    $('#thumbnail-delete-image').addClass('disabled');
                    checkbox.prop('checked', false).trigger('change');
                }

                $('#automatic_thumbnail').hide();
            }
        };
	
	initial.css('display', 'table-row');
    $('#automatic_thumbnail').data('automatic_thumbnail_image', $('#image').val());

    storage_type.on('change', function() {
		var value = $(this).val();

		remote_row.css('display', value === 'remote' ? 'table-row' : 'none');
		file_row.css('display', value === 'file' ? 'table-row' : 'none');

        toggle_thumbnail(value === 'file');

        if(value === 'file') {
            container.find('.help-inline.automatic-unsupported-location').hide();
        } else {
            if(!container.find('.help-inline.automatic-enabled').is(':visible')) {
                container.find('.help-inline.automatic-unsupported-location').show();
                container.find('.help-inline.automatic-unsupported-format').hide();
            }
        }
	});

    // Set on page load
    storage_type.trigger('change');

    // Check if file can have automatic thumbnails based on file extension
    // Check if file type has an icon we can use
    $('#storage_path_file').on('change', function() {
        $('#automatic_thumbnail').data('automatic_thumbnail_image', '');

        if (checkbox.prop('checked')) {
            $('#image').val('').trigger('change');
            $('#thumbnail-delete-image').removeClass('disabled');
        }

        var path = $(this).val(),
            extension = path.substr(path.lastIndexOf('.')+1).toLowerCase();

        if ($.inArray(extension, thumbnail_extensions) === -1) {
            toggle_thumbnail(false);

            if(!container.find('.help-inline.automatic-enabled').is(':visible') && !container.find('.help-inline.automatic-unsupported-location').is(':visible')) {
                container.find('.help-inline.automatic-unsupported-format').show();
            }
        } else {
            toggle_thumbnail(true);

            container.find('.help-inline.automatic-unsupported-format').hide();
            // check if manual thumb exists
        }

        if ($('#params_icon').val().indexOf('icon:') !== 0) {

            $.each(Docman.icon_map, function(key, value) {
                if ($.inArray(extension, value) !== -1) {
                    $('#params_icon').val(key+'.png').trigger('change');
                }
            });
        }
    });
    if($('#storage_path_file').val()) {
        var path = $('#storage_path_file').val(),
            extension = path.substr(path.lastIndexOf('.')+1).toLowerCase();

        if ($.inArray(extension, thumbnail_extensions) === -1) {
            toggle_thumbnail(false);

            if(!container.find('.help-inline.automatic-enabled').is(':visible') && !container.find('.help-inline.automatic-unsupported-location').is(':visible')) {
                container.find('.help-inline.automatic-unsupported-format').show();
            }
        } else {
            container.find('.help-inline.automatic-unsupported-format').hide();
        }
    }

    /*
     * Send the correct storage_path value on save
     * TODO: refactor once Koowa.js is refactored
     */
    var evt = function() {
        var value = $('#storage_path_'+storage_type.val()).val();

        $('<input type="hidden" name="storage_path" />').val(value).appendTo($(this.form));
    };

	$$('.-koowa-form')
		.addEvent('before.apply', evt)
		.addEvent('before.save', evt)
		.addEvent('before.save2new', evt);
});