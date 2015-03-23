$('imgInsertButton').onClick(function(){
	$('#tinymce').prepend('<p><img src="' + $('#jform_images_image_fulltext').value() + '"></p>');
});