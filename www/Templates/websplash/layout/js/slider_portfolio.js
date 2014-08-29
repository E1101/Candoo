$(window).load(function() {
	if($('#portfolio_gallery img').size()>1){
		$('#portfolio_gallery').after('<a href="Prev" class="pg-prevNav"/><a href="Next" class="pg-nextNav"/>');
	//	$('#portfolio_gallery img:not(:first)').fadeOut(0);
		$('#portfolio_gallery img:first').addClass('active');
		$('.pg-prevNav').click(function(e) {
			$('#portfolio_gallery img').stop(true,true);
			n=$('#portfolio_gallery img.active');
			$('#portfolio_gallery img.active').removeClass('active').fadeOut(1000);
			if(n.prev().size()){n.prev().fadeIn(1000).addClass('active');}
			else{$('#portfolio_gallery img:last-child').fadeIn().addClass('active');}
			return false;
		});
		$('.pg-nextNav').click(function(e) {
			$('#portfolio_gallery img').stop(true,true);
			n=$('#portfolio_gallery img.active');
			$('#portfolio_gallery img.active').removeClass('active').fadeOut(1000);
			if(n.next().size()){n.next().fadeIn(1000).addClass('active');}
			else{$('#portfolio_gallery img:first').fadeIn().addClass('active');}
			return false;
		});
		$('#portfolio_gallery').click(function(e) {
			$('#portfolio_gallery img').stop(true,true);
			n=$('#portfolio_gallery img.active');
			$('#portfolio_gallery img.active').removeClass('active').fadeOut(1000);
			if(n.next().size()){n.next().fadeIn(1000).addClass('active');}
			else{$('#portfolio_gallery img:first').fadeIn().addClass('active');}
			return false;
		});
	}
});