$(document).ready(function() {
	$('#slider_thumbnail').adGallery({
		loader_image : 'images/loader.gif',
		width : false,
		height : false,
		description_wrapper : false,
		display_next_and_prev : false,
		slideshow : {
			enable : false
		},
		effect : 'fade',
		enable_keyboard_move : false
	});
});