var DOCman = DOCman || {};

DOCman.CategoriesTree = new Class({
	Extends: MooTreeControl,
	Implements: [Options],
	options: {
		category: 0,
		theme: null,
		mode: 'folders',
		grid: true,
		onClick: function() {},
		onAdopt: function() {},
		adopt: null,
		root: {
			text: 'Root',
			open: true
		}
	},
	initialize: function(options) {
		this.setOptions(options);

		this.onAdopt = this.options.onAdopt;

		this.parent(this.options, this.options.root);

		if (options.adopt) {
			this.adopt(options.adopt);
		}
	},
	select: function(node, noClick) {
		if (!noClick) {
			this.onClick(node); node.onClick(); // fire click events
		}
		if (this.selected === node) return; // already selected
		if (this.selected) {
			// deselect previously selected node:
			this.selected.select(false);
			this.onSelect(this.selected, false);
		}
		// select new node:
		this.selected = node;
		node.select(true);
		this.onSelect(node, true);
		while (true) {
			if (!node.parent || node.parent.id == null) {
				break;
			}
			node.parent.toggle(false, true);
			node = node.parent;
		}
	},
	adopt: function(id, parentNode) {
        var length = document.id(id).getElements('li').length;
        this.parent(id, parentNode);

        //@NOTE the setTimeout and setInterval is to fix an IE bug that throws an "Stack overflow on line: 0" alert dialog
        setTimeout(function(){
            if(length === new Hash(this.index).getLength()) {
                clearInterval(pollAdopt);
                this.onAdopt(id, parentNode);
            } else {
                var pollAdopt = setInterval(function(){
                    if(length === new Hash(this.index).getLength()) {
                        clearInterval(pollAdopt);
                        this.onAdopt(id, parentNode);
                    }
                }.bind(this), 10);
            }
        }.bind(this), 0);
	},
    getDataFromElement: function(element){
        var options = {}, i = 0, total, key, name;
        if(element.dataset) {
            for(key in element.dataset){
                options[key] = element.dataset[key];
            }
        } else {
            total = element.attributes.length;
            for (var i = 0; i < total; i++){
                key = element.attributes[i].name;
                if(key.substring && key.substring(0, 5) === 'data-') {
                    name = key.substring(5, key.length).camelCase();
                    options[name] = element.attributes[i].value;
                }
            }
        }
        
        return options;
    },
	_adopt: function(id, parentNode) {
		if (typeof id === 'string') {
			var elements = document.id(id).getElements('li'),
				nodes = [],
				last_node = null,
				last_level = 0, // Level of the last list element we passed
				top_level = 0; // Top level element of the list

			elements.each(function(element) {
				var node = {
					text: element.get('text'),
					children: [],
					parent: null,
					data: this.getDataFromElement(element)
				};
				
				if (node.data.id) {
					node.id = node.data.id;
				}
	
				var level = parseInt(node.data.level, 10);
				
				if (last_level == 0) {
					top_level = level;
				}

				if (level == top_level) {
					nodes.push(node);
				}
				else if (level > last_level) {
					node.parent = last_node;
					last_node.children.push(node);
				}
				else if (level == last_level) {
					node.parent = last_node.parent;
					last_node.parent.children.push(node);
				}
				else if (level < last_level) { 
					var delta = last_level-level;
						tmp = last_node.parent;

					while (delta) {
						tmp = tmp.parent;
						delta--;
					};
					
					node.parent = tmp;
					
					tmp.children.push(node);
				}
	
				last_node = node;
				last_level = level;
			}, this);
		} 
		else {
			var nodes = id;
		}

            //@NOTE the setTimeout is to fix an IE bug that throws an "Stack overflow on line: 0" alert dialog
            setTimeout(function(){
                Object.each(nodes, function(node) {
                    var last = parentNode.insert(node);

                    if (node.children) this._adopt(node.children, last);

                }.bind(this));
            }.bind(this), 10);
    }
});