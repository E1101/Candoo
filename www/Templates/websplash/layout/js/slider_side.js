function nav_position() {
	$('#slider_side_nav').css({'margin-left' : -$('#slider_side_nav').outerWidth() / 2 - 1 + 'px'});
	$('#slider_side img').removeClass('disp_none');
}

$(window).load(function() {
	$('#slider_side').cycle({
		fx:     'fade', 
		speed:  500,
		timeout: 4000,
		pager: '#slider_side_nav',
		pause: 0
	});
	nav_position();
});