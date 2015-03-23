jQuery(function(){
	console.log('insertImageInArticle');
	jQuery('#imgInsertButton').on('click', function(){
		console.log('imgInsertButton: ' + jQuery('#jform_images_image_fulltext').value() );
		jQuery('#tinymce').prepend('<p><img src="' + jQuery('#jform_images_image_fulltext').value() + '"></p>');
	});	
});