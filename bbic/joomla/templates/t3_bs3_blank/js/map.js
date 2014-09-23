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
		class_substr = jQuery(this).attr('class').substring(16);
		classes = class_substr.split(" ");
		building = classes[0];
		// alert(building);
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
	/*Check if the current map is being loaded again don't hide*/
	if (jQuery('.'+building).css('display') == "none")
		jQuery('.building-map').hide();
	jQuery('.campus').hide();
	jQuery('.'+building).fadeIn();
	jQuery("#back-button").show();
}
function selectOffice(office) {
	jQuery('.map-popover').popover('hide');
	jQuery('.'+office).popover('show');
}

