$(window).load(function() {
	$('#recent_works').cycle({
		fx:     'fade', 
		speed:  500,
		timeout: 4000,
		pager: '#recent_works_nav',
		pause: 0
	});
});