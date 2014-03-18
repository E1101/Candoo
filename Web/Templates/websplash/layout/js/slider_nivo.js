$(window).load(function() {
	$('#slider_nivo').nivoSlider();
	$('#slider_nivo').css('background-image','url('+$('#slider_nivo img:first').attr('src')+')');
});