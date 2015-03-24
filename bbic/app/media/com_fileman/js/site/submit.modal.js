/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright    Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license        GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://github.com/nooku/nooku-files for the canonical source repository
 */

(function($) {

//We only want to run this once
var exposePlupload = function (uploader) {
    document.id('files-upload').addClass('uploader-files-queued').removeClass('uploader-files-empty');
    uploader.refresh();
    uploader.unbind('QueueChanged', exposePlupload);
    window.fireEvent('QueueChanged');
};

var addDragDrop = function(uploader) {
    var timer,
        cancel = function (e) {
            e.preventDefault();// required by FF + Safari
            e.stopPropagation();
            e.originalEvent.dataTransfer.dropEffect = 'copy'; // tells the browser what drop effect is allowed here
        },
        createDragoverHandler = function (container) {

            //Create hilite + label
            var focusring = $('<div class="dropzone-focusring"></div>'),
                label = $('<div class="alert alert-success">' + Koowa.translate('Drop your files to upload to {folder}').replace('{folder}', Files.app.title) + '</div>');

            focusring.css({
                display: 'none',
                position: 'absolute',
                backgroundColor: 'hsla(0, 0%, 100%, 0.75)',
                top: 0,
                left: 0,
                bottom: 0,
                right: 0,
                zIndex: 65558,
                borderStyle: 'solid',
                borderWidth: '5px',
                opacity: 0,
                transition: 'opacity 300ms',
                paddingTop: 10,
                textAlign: 'center'
            });
            container.append(focusring);

            //To inherit styling
            $('#files-upload').append(label);
            ['border-radius', 'color', 'background', 'border'].forEach(function (prop) {
                label.css(prop, label.css(prop));
            });
            label.css({
                display: 'inline-block',
                margin: '0 auto'
            });
            focusring.append(label);
            focusring.css('border-color', label.css('color')); //border-color too bright

            return function (e) {

                e.preventDefault();// required by FF + Safari
                e.originalEvent.dataTransfer.dropEffect = 'copy'; // tells the browser what drop effect is allowed here
                if (focusring.css('display') == 'none') {
                    label.text(Koowa.translate('Drop your files to upload to {folder}').replace('{folder}', Files.app.title));
                    focusring.css('display', 'block');
                    setTimeout(function () {
                        focusring.css('opacity', 1);
                        if (!$('#files-upload').is(':visible')) {
                            $('#files-canvas').addClass('dropzone-droppable');
                        }
                    }, 1);

                }
                //container.addClass('dropzone-dragover'); //This breaks safaris drag and drop, still unknown why

                //This is a failsafe measure
                clearTimeout(timer);
                timer = setTimeout(function () {
                    $('.dropzone-focusring').css('opacity', 0).css('display', 'none');
                    $('#files-canvas').removeClass('dropzone-droppable');
                }, 300);
            };
        },
        createDragleaveHandler = function (/*container*/) {
            return function (e) {
                //@TODO following code is too buggy, it fires multiple times causing a flickr, for now the focusring will only dissappear on drop
                //container.removeClass('dropzone-dragover');
                //$('.dropzone-focusring').css('opacity', 0).css('display', 'none');
            };
        },
        addSelectedFiles = function(native_files) {
            var file, i, files = [], id, fileNames = {};

            // Add the selected files to the file queue
            for (i = 0; i < native_files.length; i++) {
                file = native_files[i];

                // Safari on Windows will add first file from dragged set multiple times
                // @see: https://bugs.webkit.org/show_bug.cgi?id=37957
                if (fileNames[file.name]) {
                    continue;
                }
                fileNames[file.name] = true;

                uploader.addFile(file);
            }
        },
    // Make the document body a dropzone
        files_canvas = $('#files-canvas'),
        body = $(document.body);

    document.id('files-upload').addClass('uploader-droppable');

    //Prevent file drops from duplicating due to double drop events
    $('#files-upload-multi_filelist').bind('drop', function (event) {
        event.stopPropagation();
        //@TODO implement the rest of the drop code from handler, to remove focusring
        $(document.body).removeClass('dropzone-dragover');
    });

    body.bind('dragover', createDragoverHandler(body)); //Using dragenter caused inconsistent behavior
    body.bind('dragleave', createDragleaveHandler(body));
    body.bind('dragenter', cancel);
    body.bind('drop', function (event) {
        event.preventDefault();
        files_canvas.removeClass('dragover');
        var dataTransfer = event.originalEvent.dataTransfer;

        // Add dropped files
        if (dataTransfer && dataTransfer.files && dataTransfer.files.length) {
            var copy = dataTransfer.files;

            if (!$('#files-upload').is(':visible')) {
                //@TODO the click handler is written in mootools, so we use mootools here
                document.getElement(Files.app.options.uploader_dialog.button).fireEvent('click', 'DOMEvent' in window ? new DOMEvent : new Event);
            }

            setTimeout(function() {
                addSelectedFiles(copy);
            }, 300);
        }
    });
    body.bind('dragend', function () {
        $('.dropzone-focusring').css('opacity', 0).css('display', 'none');
    });

    uploader.bind('QueueChanged', exposePlupload);
};


Files.createUploader = function (options) {
    options = $.extend({}, {
        element: '#files-upload-multi',
        multi_selection: true,
        media_path: ''
    }, options);
    var element = $(options.element);

    if (element.length === 0) {
        return;
    }

    var config  = {
        runtimes: 'html5,flash',
        browse_button: 'pickfiles',
        multi_selection: options.multi_selection,
        dragdrop: true,
        unique_names: false,
        rename: true,
        url: '/', // this is added on the go in BeforeUpload event
        flash_swf_url: options.media_path+'koowa/com_files/js/plupload/Moxie.swf',
        urlstream_upload: true, // required for flash
        multipart_params: {
            _action: 'add',
            csrf_token: Files.token
        },
        filters: {
            prevent_duplicates: true
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        preinit: {
            Error: function (up, args) {
                if (args.code == plupload.INIT_ERROR) {
                    var message = Koowa.translate('{html5} or {flash} required for uploading files from your computer.', {
                        html5: '<a href="https://google.com/chrome" target="_blank">' + Koowa.translate('HTML5 enabled browser') + '</a>',
                        flash: '<a href="https://get.adobe.com/flashplayer/" target="_blank">' + Koowa.translate('Flash Player') + '<a/>'
                    });

                    // Plupload clears element's contents after the event so setTimeout is needed
                    setTimeout(function() {
                        element.append('<div class="alert alert-error warning">' + message + '</div>');
                    }, 100);
                }
            }
        }
    };

    config.filters.mime_types = [
        {title: Koowa.translate('All Files'), extensions: Files.app.container.parameters.allowed_extensions.join(',')}
    ];

    config.max_file_size = Files.app.container.parameters.maximum_size;

    window.addEvent('refresh', function () {
        uploader.refresh();
    });

    element.pluploadQueue(config);

    var uploader = element.pluploadQueue();

    $('.plupload_start', element).click(function (e) {
        e.preventDefault();

        var $this = $(this),
            getNamesFromArray = function (array) {
                var results = [];
                $.each(array, function (i, entity) {
                    results.push(entity.name);
                });

                return results;
            },
            startUpload = function () {
                if (!$this.hasClass('plupload_disabled')) {
                    uploader.start();
                }
            },
            getConfirmationMessage = function (files) {
                var message = '';

                if (files.length === 1) {
                    message = Koowa.translate('A file with the same name already exists. Would you like to overwrite it?');
                } else if (files.length > 1) {
                    message = Koowa.translate('Following files already exist. Would you like to overwrite them? {names}', {
                        names: "\n" + files.join("\n")
                    });
                }

                return message;
            },
            makeUnique = function (file, similar) {
                var names = [];
                if (typeof similar.entities === 'object' && similar.entities.length) {
                    names = getNamesFromArray(similar.entities);
                }
                $.each(uploader.files, function (i, f) {
                    if (f.id !== file.id) {
                        names.push(f.name);
                    }
                });

                file.name = getUniqueName(file.name, function (name) {
                    return $.inArray(name, names) !== -1;
                });

                $('#' + file.id).find('div.plupload_file_name span').text(file.name);
            },
            checkDuplicates = function (response) {
                uploader.settings.multipart_params.overwrite = 0;

                if (typeof response.entities === 'object' && response.entities.length) {
                    var existing = getNamesFromArray(response.entities),
                        promises = [];

                    if (confirm(getConfirmationMessage(existing))) {
                        uploader.settings.multipart_params.overwrite = 1;

                        return startUpload();
                    }

                    $.each(uploader.files, function (i, file) {
                        if ($.inArray(file.name, existing) !== -1) {
                            promises.push($.ajax({
                                type: 'GET',
                                url: Files.app.createRoute({
                                    view: 'files', folder: Files.app.getPath(), limit: 100,
                                    search: file.name.substr(0, file.name.lastIndexOf('.')) + ' ('
                                })
                            }).done(function (response) {
                                return makeUnique(file, response)
                            }));
                        }
                    });

                    if (promises) {
                        $.when.apply(kQuery, promises).then(function () {
                            startUpload();
                        });
                    }
                }
                else {
                    startUpload();
                }
            };

        var names = [];
        $.each(uploader.files, function (i, file) {
            if (file.loaded == 0) {
                names.push(file.name);
            }
        });

        if (names.length) {
            $.ajax({
                url: Files.app.createRoute({view: 'files', limit: 100, folder: Files.app.getPath()}),
                type: 'POST',
                data: {
                    _method: 'GET',
                    name: names
                }
            }).done(checkDuplicates).fail(startUpload);
        }

    });

    // Do not allow more than 100 files to be uploaded at once
    uploader.bind('FilesAdded', function (uploader) {
        if (uploader.files.length > 100) {
            uploader.splice(0, uploader.files.length - 100);
        }
    });

    var msie    = window.navigator.userAgent.indexOf('MSIE '),
        trident = window.navigator.userAgent.indexOf('Trident/'),
        is_ie   = (msie > 0 || trident > 0),
        hideDropZone = function() {
            document.id('files-upload')
                .addClass('uploader-nodroppable')
                .setStyle('position', '')
                .addClass('uploader-files-queued')
                .removeClass('uploader-files-empty');

            uploader.refresh();
        };

    if (!is_ie) {
        setTimeout(function() {
            if (uploader.features.dragdrop) {
                addDragDrop(uploader);
            } else {
                hideDropZone();
            }
        }, 1500);
    } else {
        hideDropZone();
    }

    uploader.bind('BeforeUpload', function (uploader, file) {
        // set directory in the request
        uploader.settings.url = Files.app.createRoute({
            view: 'file',
            plupload: 1,
            folder: Files.app.getPath() // TODO: need to implement
        });
    });

    uploader.bind('UploadComplete', function (uploader) {
        $('li.plupload_delete a,div.plupload_buttons', element).show();

        uploader.disableBrowse(false);
        uploader.refresh();
    });

    // Keeps track of failed uploads and error messages so we can later display them in the queue
    var failed = {};
    uploader.bind('FileUploaded', function (uploader, file, response) {
        var json = JSON.decode(response.response, true) || {};
    });

    uploader.bind('StateChanged', function (uploader) {
        Object.each(failed, function (error, id) {
            icon = $('#' + id).attr('class', 'plupload_failed').find('a').css('display', 'block');
            if (error) {
                icon.attr('title', error);
            }
        });

    });

    $$('.plupload_clear').addEvent('click', function (e) {
        e.stop();

        if (confirm(Koowa.translate('Are you sure you want to clear the upload queue? This cannot be undone!'))) {
            // need to work on a clone, otherwise iterator gets confused after elements are removed
            var files = uploader.files.slice(0);
            files.each(function (file) {
                uploader.removeFile(file);
            });
        }
    });

    //Width fix
    setRemoteWrapMargin();

    //Remove FLOC fix
    document.id('files-upload').getParent().setStyle('visibility', '');
};

})(kQuery);