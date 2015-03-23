jQuery(function(){
	console.log('insertImageInArticle');
	jQuery('#imgInsertButton').on('click', function(){
		console.log('imgInsertButton: ' + jQuery('#jform_images_image_fulltext').val() );
		jQuery('#tinymce').html('<p><img src="' + jQuery('#jform_images_image_fulltext').val() + '"></p>' + jQuery('#tinymce').html());
	});	
});