$(function(){
	console.log('insertImageInArticle');
	$('#imgInsertButton').on('click', function(){
		console.log('imgInsertButton: ' + $('#jform_images_image_fulltext').value() );
		$('#tinymce').prepend('<p><img src="' + $('#jform_images_image_fulltext').value() + '"></p>');
	});	
});