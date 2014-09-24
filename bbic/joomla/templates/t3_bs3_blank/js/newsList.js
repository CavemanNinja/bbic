jQuery(function(){
	loadList(1);

	jQuery('.twoj_tab_block_a').each(function(index){
		jQuery(this).click(function(){
			// alert(index+1);
			// if (index != 0)
			loadList(index+1);
		})
	})
});

function loadList(tab_num) {
 	// alert(tab_num);
 	num_page = 3;
	articles = [];
 	content = '';
 	if (jQuery('#news-list-content-id').hasClass('news-list-content-ar'))
 		modid_lookup = ['154', '155', '156', '157'];
 	else
 		modid_lookup = ['145', '151', '152', '153'];

	jQuery('#Mod'+modid_lookup[tab_num-1]+' #list-ul li').each(
		function(index) {
			articles[index] = jQuery(this).html();
			// alert(articles[index]);
			//alert(index);
	});
	
	total_page = Math.ceil(articles.length/num_page);
	
	for (i=0; i < num_page && i < articles.length; i++) {
		content += '<li>'+articles[i]+'</li>';
	}

	jQuery('#twoj_fragment1-'+tab_num+' #list-ul').html(content);

	jQuery('#twoj_fragment1-'+tab_num+' #page-selection').bootpag({
        total: total_page,
        // total: 20,
        // maxVisible: 1,
        leaps: false,

    }).on('page', function(event, num){
 		jQuery(this).bootpag({total: total_page});
 		content = '';

		for (i=num_page*(num-1); i < num_page*num && i < articles.length; i++) {
			content += '<li>'+articles[i]+'</li>';
		}        	

		jQuery('#twoj_fragment1-'+tab_num+' #list-ul').html(content);
		addButtonEvents();

    });

	addButtonEvents();
}

function addButtonEvents() {
	jQuery('button[id|=\'btn-expand\']').unbind().click(function(){
		id_array = this.id.split('-');
		id = id_array[id_array.length - 1];
		fragmentId = this.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.id;
		// alert(fragmentId);
		jQuery('#'+fragmentId+' #fulltext-'+id).slideDown();
		jQuery('#'+fragmentId+' #btn-expand-'+id).hide();
		jQuery('#'+fragmentId+' #btn-collapse-'+id).show();	
	});	

	jQuery('button[id|=\'btn-collapse\']').unbind().click(function(){
	
		id_array = this.id.split('-');
		id = id_array[id_array.length - 1];
		fragmentId = this.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.id;
	
		jQuery('#'+fragmentId+' #fulltext-'+id).slideUp();
		jQuery('#'+fragmentId+' #btn-collapse-'+id).hide();	
		jQuery('#'+fragmentId+' #btn-expand-'+id).show();
	});

}