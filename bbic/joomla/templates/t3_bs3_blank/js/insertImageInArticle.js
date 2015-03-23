jQuery(function(){
	console.log('insertImageInArticle');
	jQuery('#imgInsertButton').on('click', function(){
		var iframe = jQuery('#jform_articletext_ifr');
		var tinymce = jQuery('#tinymce', iframe.contents());
		tinymce.html('<p><img src="' + jQuery('#jform_images_image_fulltext').val() + '"></p>' + tinymce.html());
	});	
});