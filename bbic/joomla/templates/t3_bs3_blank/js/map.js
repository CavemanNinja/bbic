jQuery(function(){
	jQuery('.map-popover').popover({
		animation: false,
		trigger: 'manual'
	});
	jQuery('.campus-map-popover').popover({
		animation: false,
		trigger: 'hover'
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
		jQuery('.map-label > h2').text("BBIC Campus");
		jQuery('.floor-label').hide();
		jQuery(this).hide();
	});

	// jQuery('.building-img').error(function(){
	// 	jQuery(this).hide();
	// });
});

function drillDown(building) {
	/*Check if the current map is being loaded again don't hide*/
	if (jQuery('.'+building).css('display') == "none")
		jQuery('.building-map').hide();
	jQuery('.campus').hide();
	jQuery('.'+building).fadeIn();
	switch(building) {
		case 'building8':
			jQuery('.map-label > h2').text("Building 8");
			jQuery('.floor-label').hide();
			break;
		case 'a1':
			jQuery('.map-label > h2').text("Lot A");
			jQuery('.floor-label').hide();
			break;
		case 'a2':
			jQuery('.map-label > h2').text("Lot A");
			jQuery('.floor-label').hide();
			break;
		case 'a3':
			jQuery('.map-label > h2').text("Lot A");
			jQuery('.floor-label').hide();
			break;
		case 'b1':
			jQuery('.map-label > h2').text("Lot B");
			jQuery('.floor-label').hide();
			break;
		case 'b2':
			jQuery('.map-label > h2').text("Lot B");
			break;
		case 'b3':
			jQuery('.map-label > h2').text("Lot B");
			break;
		case 'c1':
			jQuery('.map-label > h2').text("Lot C");
			jQuery('.floor-label').hide();			
			break;
		case 'c2':
			jQuery('.map-label > h2').text("Lot C");
			jQuery('.floor-label').hide();
			break;
		case 'c3':
			jQuery('.map-label > h2').text("Lot C");
			jQuery('.floor-label').hide();
			break;
		case 'wh':
			jQuery('.map-label > h2').text("Warehouses");
			jQuery('.floor-label').hide();			
			break;
		case 'bdb':
			jQuery('.map-label > h2').text("Building W2");
			jQuery('.floor-label').show();
			jQuery('.ground').css('left', '190px');
			jQuery('.ground').css('top', '30px');
			jQuery('.first').css('top', '30px');
			jQuery('.first').css('right', '220px');
			break;
		case 'w1':
			jQuery('.map-label > h2').text("Building W1");
			jQuery('.floor-label').show();
			jQuery('.ground').css('left', '190px');
			jQuery('.ground').css('top', '30px');
			jQuery('.first').css('top', '30px');
			jQuery('.first').css('right', '220px');
			break;
		case 'w4':
			jQuery('.map-label > h2').text("Building W4");
			jQuery('.floor-label').hide();
			break;
		case 'w3':
			jQuery('.map-label > h2').text("Building W3");
			jQuery('.floor-label').hide();
			break;
	}
	jQuery("#back-button").show();
}
function selectOffice(office) {
	jQuery('.map-popover').popover('hide');
	jQuery('.'+office).popover('show');
}

