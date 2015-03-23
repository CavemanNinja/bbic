jQuery(function(){
	console.log('insertImageInArticle');
	jQuery('#imgInsertButton').on('click', function(){
		console.log('imgInsertButton: ' + jQuery('#jform_images_image_fulltext').val() );
		var iframe = jQuery('#jform_articletext_ifr');
		var tinymce = jQuery('#tinymce', iframe.contents());
		console.log('timymce html: ' + jQuery('#tinymce', iframe.contents()).html());
		console.log('tinymce.html' + tinymce.html());

		tinymce.html('<p><img src="' + jQuery('#jform_images_image_fulltext').val() + '"></p>' + tinymce.html());
	});	
});