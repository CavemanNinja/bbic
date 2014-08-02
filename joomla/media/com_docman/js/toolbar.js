
window.addEvent('domready', function() {
	var controller = document.getElement('.-koowa-grid').retrieve('controller'),
		events = {
			'delete': 'core.delete',
			'edit': 'core.edit',
			'add': 'core.add' 	
		};
	
	Object.each(events, function(value, key) {
		controller.addEvent('before.'+key, function(data, novalidate) {
			var boxes = Koowa.Grid.getAllSelected(),
				// TODO: this doesn't work as multiple buttons may have the same action
				// find a way to get the clicked button 
				/*button = this.buttons.filter(function(button) {
					return button.get('data-action') === key;
				}).shift();*/

			results = [];
			Object.each(boxes, function(selected){
	            results.push(JSON.decode(selected.get('data-permissions'))[value]);
	        });
	        
			if (results.contains(false)) {
				var message = 'You are not authorized to perform the %s action on these items';
				alert(message.replace('%s', key));
				return false;
			}
	        
			return true;
		});
	});
});