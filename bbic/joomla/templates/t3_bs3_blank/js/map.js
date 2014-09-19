jQuery(function(){
	jQuery('.map-popover').popover({
		animation: false
	});
	jQuery('#map-select').change(function(){
		building = this.value.split('_');
		drillDown(building[0]);
		selectOffice(this.value);
	});
	jQuery('i[class^="campus_building"]').click(function(){
		building = jQuery(this).attr('class').substring(16);
		drillDown(building);
	});

	jQuery('#back-button').click(function(){
		jQuery('.map-popover').popover('hide');
		jQuery('#map-select').val('0');
		jQuery('.building-map').hide();
		jQuery('.campus').fadeIn();
		jQuery(this).hide();
	})
});

function drillDown(building) {
	jQuery('.campus').hide();
	jQuery('.'+building).fadeIn();
	jQuery("#back-button").show();
}
function selectOffice(office) {
	jQuery('.map-popover').popover('hide');
	jQuery('.'+office).popover('show');
}

