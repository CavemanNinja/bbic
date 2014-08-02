
window.addEvent('domready', function() {
	document.id('files-grid').addEvent('click', function(event) {
		if (Files.app.grid.layout !== 'details') {
			return;
		}
		
		var target = event.target;
		if (target.get('tag') === 'a' || target.get('tag') === 'input') {
			return;
		}
		
		var node = target.getParent('.files-node-shadow') || target.getParent('.files-node');

        if(node) {
            row = node.retrieve('row');

            if (row) {
                Files.app.grid.checkNode(row);
            }
        }
    });
	
	['clickImage', 'clickFile'].each(function(event) {
		Files.app.grid.removeEvents(event);
		Files.app.grid.addEvent(event, function(e) {
			var target = document.id(e.target),
				node = target.getParent('.files-node-shadow') || target.getParent('.files-node'),
				row = node.retrieve('row');

			var url = Files.app.createRoute({
				option: 'com_docman', view: 'documents', format: 'json',
				storage_type: 'file', storage_path: row.path, routed: 0});
			new Request.JSON({
				url: url,
				onSuccess: function(response) {
					if (typeOf(response) == 'object') {
						var copy = Object.append({}, row);
						copy.documents = response.documents.items;

						copy.template = 'documents_list';
						var render = copy.render();

                        //Setting display to inline-block for the dynamic width/height to work, also adding css hooks
                        render.setStyle('display', 'inline-block').addClass('com_docman modal-inspector');

                        var template = render.inject(document.body);
						template.getElements('a.document-link').addEvent('click', function(e) {
							e.stop();
							var url = Files.app.createRoute({
								option: 'com_docman', view: 'document', format: 'html',
								routed: 0, container: false,
								id: this.get('data-id')
							});
							window.parent.open(url);
						});
						SqueezeBox.open(template, {
							handler: 'adopt',
                            size: template.measure(function(){return this.getSize();})
						});
					}
				}
			}).get();
		});
	});
	
	$$('a.toolbar').addEvent('click', function(e) {
		if (this.hasClass('unauthorized')) {
			return false;
		}
	});

	document.id('toolbar-upload').getElement('a').addEvent('click', function(e) {
		e.stop();
		
		if (this.hasClass('unauthorized')) {
			return;
		}
		
		document.id('files-show-uploader').fireEvent('click', e);
	});

	document.id('toolbar-new').getElement('a').addEvent('click', function(e) {
		e.stop();

		if (this.hasClass('unauthorized')) {
			return;
		}
		
		document.id('files-new-folder-toolbar').fireEvent('click', e);
	});

	document.id('toolbar-delete').getElement('a').addEvent('click', function(e) {
		e.stop();

		if (this.hasClass('unauthorized')) {
			return;
		}
		
		document.id('files-batch-delete').fireEvent('click', e);
	});

	var enableButton = function(button) {
		document.id(button).getElement('a').removeClass('disabled');
	};
	var disableButton = function(button) {
		document.id(button).getElement('a').addClass('disabled');
	};
	
	var checkbox_dependents = $$('#toolbar-delete', '#toolbar-create-documents');
	checkbox_dependents.each(function(el) {
		disableButton(el);
	});
	
	Files.app.grid.addEvent('afterCheckNode', function() {
		var checked = Files.app.grid.nodes.filter(function(row) { return row.checked }),
			folders = Object.getLength(checked.filter(function(row) { return row.type == 'folder'})),
			files = Object.getLength(checked) - folders;

		if (files || folders) {
			enableButton('toolbar-delete');
			if (files) {
				enableButton('toolbar-create-documents');
			}
		} else {
			checkbox_dependents.each(function(el) {
				disableButton(el);
			});
		}
		
	}.bind(this));

	Files.app.addEvent('afterNavigate', function() {
		checkbox_dependents.each(function(el) {
			el.getElement('a').addClass('disabled');
		});
	});
	
	Files.app.addEvent('uploadFile', function(row) {
		Files.app.grid.checkNode(row);
	});

	document.id('toolbar-create-documents').getElement('a').addEvent('click', function(e) {
		e.stop();
		
		var checked_files = Files.app.grid.nodes.filter(function(row) { return row.checked && row.type !== 'folder' }),
			paths = [];

		Object.each(checked_files, function(row) {
			paths.push(row.path);
		});

		if (!paths.length) {
			return;
		}
		
		var form = jQuery('<form></form>'),
			createField = function(name, value) {
				var field = jQuery('<input></input>');
	
		        field.attr('type', 'hidden');
		        field.attr('name', name);
		        field.attr('value', value);
		        
		        return field;
			};

	    form.attr('method', 'post');
	    form.attr('action', 'index.php?option=com_docman&view=files&layout=form');
	    form.append(createField('_method', 'GET'));

	    jQuery.each(paths, function(key, value) {
	        form.append(createField('paths[]', value));
	    });

	    jQuery(document.body).append(form);
	    form.submit();
	});

	var fileCountAdder = function() {
		if (Files.app.grid.layout !== 'details') {
			return;
		}

		var counts = {},
			requests = new Chain(),
			files = Files.app.grid.getFiles(),
			count = files.length,
			url = Files.app.createRoute({
				option: 'com_docman', view: 'documents', format: 'json',
                limit: 1000,
                storage_type: 'file', routed: 0
			}),
		    request = new Request.JSON({
			    url: url,
                method: 'POST',
                data: {
                    _method: 'GET',
                    storage_path: []
                },
                onComplete: function() {
                    requests.callChain();
                },
                onSuccess: function(response) {
                    if (typeof response != 'object' || typeof response.documents != 'object') {
                        return;
                    }

                    Object.each(response.documents.items, function(row) {
                        var data = row.data;
                        if (!counts[data.storage_path]) {
                            counts[data.storage_path] = 0;
                        }
                        counts[data.storage_path]++;
                    });

                    Files.app.grid.nodes.each(function(row) {
                        var count = counts[row.path] || 0;
                        row.document_count = count;
                        var count_box = row.element.getElement('.file-count');
                        if (count_box) {
                            count_box.set('html', '<a href="#" class="navigate">'+count+'</a>');
                        }
                    });
                }
			});

			var i;
			for(i = 0; i < count; i += 30) {
				requests.chain(function() {
					var slice = files.splice(0, Math.min(30, count));
					request.options.data.storage_path = slice;
					request.send();
					count -= 30;
				});
			}
			requests.callChain();
	};
	
	// If we already have files, run it
	if (Files.app.grid.getFiles()) {
		fileCountAdder();
	}
	
	Files.app.grid.addEvent('afterInsertRows', fileCountAdder);

    var attachCheckAllHandlers = function(){
        document.id('select-orphans').addEvent('click', function(e) {
            e.preventDefault();
            var check = false;
            Files.app.grid.nodes.each(function(row) {
                if (row.type !== 'folder' && !row.checked && row.document_count === 0) {
                    Files.app.grid.checkNode(row);
                    check = true;
                } else if(row.checked && (row.document_count !== 0 || row.type === 'folder')) {
                    Files.app.grid.checkNode(row);
                }
            });
            document.id('select-check-all').checked = check;
        });

        document.id('select-all').addEvent('click', function(e) {
            e.preventDefault();
            Files.app.grid.nodes.each(function(row) {
                if (!row.checked) {
                    Files.app.grid.checkNode(row);
                }
            });
            document.id('select-check-all').checked = true;
        });
        document.id('select-none').addEvent('click', function(e) {
            e.preventDefault();
            Files.app.grid.nodes.each(function(row) {
                if (row.checked) {
                    Files.app.grid.checkNode(row);
                }
            });
            document.id('select-check-all').checked = false;
        });
        document.id('select-check-all').addEvent('click', function(e){
            e.stopPropagation();
            var value = document.id('select-check-all').checked,
                grid = Files.app.grid,
                nodes = grid.nodes;

            Object.each(nodes, function(node) {
                if (value && !node.checked) {
                    grid.checkNode(node);
                } else if (!value && node.checked) {
                    grid.checkNode(node);
                }

            });
        });
    };
    if(Files.app.grid.layout == 'details') {
        attachCheckAllHandlers();
    }
    Files.app.grid.addEvent('afterRender', function() {
        if(Files.app.grid.layout == 'details')
        {
            attachCheckAllHandlers();
        }
    });
});