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
		var image = jQuery('#newsArticleImage', iframe.contents());
		var url = jQuery('#jform_images_image_fulltext').val();


		if (image.length) {
			console.log('1');
			image.attr('src', url);
		} else {
			console.log('2');
			tinymce.html('<p><img id="newsArticleImage" src="' + url + '"></p>' + tinymce.html());
		}
	});


});

