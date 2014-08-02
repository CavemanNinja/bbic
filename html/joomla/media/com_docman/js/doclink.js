
var Doclink = {};

(function(Doclink) {
	
var default_url = {
	option: 'com_docman',
	view: 'documents',
	format: 'json',
	category: 0,
	limit: 100000,
	enabled: 1,
	sort: 'created_on',
	direction: 'desc'
};

Doclink.Request = new Class({
	Extends: Request.JSON,

    get: function(url) {
        if (typeof url === 'object') {
            url = Object.merge(default_url, url);
            this.parent(url);
        }
		else if (isNaN(url)) {
			this.parent(url);
		}
		else { // just the category id
			default_url.category = url;
			this.parent(default_url);
		}
	}
});

Doclink.request = new Doclink.Request({
    method: 'get',
    onSuccess: function(response, responseText) {
      if (typeof this._onSuccess === 'function') {
        this._onSuccess(response);
      }
    },
    onFailure: function(xhr) {
      if (typeof this._onFailure === 'function') {
        this._onFailure(xhr);
      }
      else {
        var resp = JSON.decode(xhr.responseText, true),
            error = resp && resp.error ? resp.error : 'An error occurred during request';
        alert(error);
      }
    }
});
Doclink.onAJAXSuccess = function(response, node, table) {
    table.empty();

    // No results
    if (typeof response.documents === 'undefined' || response.documents.total == 0) {
        new Element('tr').adopt(
            new Element('td', {colspan: 3, text: Doclink._.empty_folder_text})
        ).inject(table);
    }
    else {
        Object.each(response.documents.items, function(el) {
            var row = el.data;
            row.category_slug = el.category.slug;
            row.itemid = node.data.itemid;
            row = Doclink.createRow(row);

            row.inject(table);
        });
    }
};

Doclink.updateProperties = function(data) {
	document.id('insert-image').set('text', Doclink._['insert_'+data.type.toLowerCase()]);
	document.id('url').set('value', data.url || '');
	if (Doclink.caption_from_editor !== true) {
		document.id('caption').set('value', data.title || '');
	}

    if(data.type !== 'Document') {
        document.id('documents-sidebar').addClass('focus');
        document.id('files-container').removeClass('focus');
    } else {
        document.id('files-container').addClass('focus');
        document.id('documents-sidebar').removeClass('focus');
    }

    if (data.type === 'Menu' || data.type === 'Category') {
        Doclink.link_target = data.target;
    }

    Doclink.link_type = data.type;
};

Doclink.onClickMenu = function(data) {
	var properties = {
		type: 'Menu',
		url:'index.php?Itemid='+data.itemid,
		title: data.title,
        target: data.target
	};

	Doclink.updateProperties(properties);
};

Doclink.onClickDocument = function(e) {
	e.stop();
	
	var tr = this.getParent().getParent(),
		row = tr.retrieve('row');
	
	tr.getSiblings('tr').removeClass('selected');
	tr.addClass('selected');

	var data = {
		type: 'Document',
		url:'index.php?option=com_docman&view=document&alias='+row.alias+'&category_slug='+row.category_slug+'&Itemid='+row.itemid,
		title: row.title
	};
	Doclink.updateProperties(data);
};

Doclink.onClickCategory = function(data) {
	var data = {
		type: 'Category',
		url:'index.php?option=com_docman&view=category&slug='+data.slug+'&Itemid='+data.itemid,
		title: data.title,
        target: data.target
	};
	Doclink.updateProperties(data);
};

Doclink.createRow = function(el) {
	var row = new Element('tr').adopt(
		new Element('td').adopt(new Element('a', {href: '#', text: el.title})),
		new Element('td', {text: el.publish_date})
	);

	row.addEvent('click:relay(a)', Doclink.onClickDocument);
	row.store('row', el);

	return row;
};

Doclink.getLinkString = function() {
	var href = document.id('url').get('value'),
		caption = document.id('caption').get('value'),
        target = (Doclink.link_type === 'Document' && Doclink.link_target === 'blank') ? ' target="_blank"' : '',
		str = '';

	str += ' <a class="doclink" href="'+href+'"'+target+'>';
	str += caption;
	str += '</a>';

	return str;
};

Doclink.initialize = function() {
	var tbody = document.getElement('#document_list tbody'),
        initial_row = tbody.getElement('.initial-row').clone();
	
	document.id('insert-image').addEvent('click', function(e) {
		e.stop();

		window.parent.jInsertEditorText(Doclink.getLinkString(), Doclink.editor);
		window.parent.SqueezeBox.close();
	});

	if (window.parent.tinyMCE) {
		var text = window.parent.tinyMCE.activeEditor.selection.getContent({format:'text'});
		if (text) {
			Doclink.caption_from_editor = true;
			document.id('caption').set('value', text);
		}
	}

    var trees = [],
        onClick = function(node) {
	        var root = this.root;

	        // Deselect all other menu trees
	        Object.each(trees, function(tree) {
	            if (tree.root !== root) {
	                if(tree.selected) tree.selected.select(false);
	            } else {
                    if(tree.selected) tree.selected.select(true);
                }
	        });
	        //*/

            Doclink.request._onSuccess = function(response) {
                Doclink.onAJAXSuccess(response, node, tbody);
            };

            //Otherwise the class is reset
            this.root.div.icon.addClass('menuitem');

            if (node === root) {
                node.toggle(false, true);
                Doclink.onClickMenu(node.data);

                tbody.empty().adopt(initial_row);

                if (node.data.view === 'filteredlist') {
                    Doclink.request.get({'Itemid': node.data.itemid, 'category': ''});
                }
            } else {
                Doclink.onClickCategory(node.data);
                this.root.select(false);

                Doclink.request.get({'Itemid': node.data.itemid, 'category': node.data.id});
            }
	    };

	$$('ul.pages > li').each(function(element) {
		var id = element.get('data-id');

		new Element('div', {id: 'page-tree-'+id}).inject(document.getElement('div.sidebar-inner'));
		trees.push(new DOCman.CategoriesTree({
            onAdopt: function(){
                // Set menu target for categories
                var target = this.root.data.target;

                $each (this.index, function(node) {
                   node.data.target = target;
                });

                this.root.div.icon.addClass('menuitem');
                for(key in this.index) {
                    if(this.index.hasOwnProperty(key)) {
                        this.index[key].div.main.setAttribute('title', this.index[key].data.title);
                    }
                }
            },
	        div: 'page-tree-'+id,
	        adopt: 'page-categories-'+id,
		    theme: Doclink.tree_theme,
		    onClick: onClick,
            onExpand: function(node, open){
                if(node === this.root) {
                    var icon =  this.root.div.icon;
                    icon.addClass('menuitem');
                    if(!open) icon.addClass('closed');
                }
            },
            onSelect: function(){
                var icon =  this.root.div.icon;
                icon.addClass('menuitem');
            },
		    root: {
			    text: element.get('data-title'),
			    data: {
				    id: id,
				    itemid: id,
				    title: element.get('data-title'),
                    view: element.get('data-view'),
                    target: element.get('data-target')
			    }
		    }
		}));
	});
	
	$$('ul.pages').destroy();
};

})(Doclink);
