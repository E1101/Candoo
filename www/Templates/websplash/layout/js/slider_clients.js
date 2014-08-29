$(document).ready(function() {
	$('#clients').cycle({
		fx:     'scrollHorz', 
		speed:  500,
		timeout: 5000,
		next:   '.block_clients .arrow_right',
		prev:   '.block_clients .arrow_left',
		pause: 0,
		rev: 1
	});
});