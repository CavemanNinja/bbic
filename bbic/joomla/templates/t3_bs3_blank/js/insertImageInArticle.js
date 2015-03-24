jQuery(function(){
	// console.log('insertImageInArticle');
	// jQuery('#popupInsertImage').on('click', function(){
	// 	var iframe = jQuery('#jform_articletext_ifr');
	// 	var tinymce = jQuery('#tinymce', iframe.contents());
	// 	tinymce.html('<p><img src="' + jQuery('#jform_images_image_fulltext').val() + '"></p>' + tinymce.html());
	// });	

	jQuery('#jform_images_image_fulltext').on('change', function(){
		var iframe = jQuery('#jform_articletext_ifr');
		var tinymce = jQuery('#tinymce', iframe.contents());
			
		if (jQuery('#newsArticleImage', iframe.contents()).length) {
			console.log('1');
			jQuery('#newsArticleImage', iframe.content()).attribs('src', jQuery('#jform_images_image_fulltext').val());
		} else {
			console.log('2');
			tinymce.html('<p><img id="newsArticleImage" src="' + jQuery('#jform_images_image_fulltext').val() + '"></p>' + tinymce.html());
		}
	});


});

