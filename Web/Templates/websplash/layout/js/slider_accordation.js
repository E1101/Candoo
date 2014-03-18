// variables
smWid = 89; // width for hidden
sumWid = 929; // width for continer

$(document).ready(function(){
	accordation('.slider_wrapper', '.slider_image','.accordation_text');
 
});
function accordation(s,im,tx){
	num = $(s+' li').size();
	actWid = Math.ceil(sumWid-(num-1)*smWid);
	defWid = Math.ceil(sumWid/num);
	
	activeItem = $(s+' li:last');
	$(im).width(actWid);
	$(s+' li').animate({width:defWid}, {duration:500, queue:false});
	cT = setTimeout(function(){},0);
	
	$(s+' li').hover(function(){
		clearTimeout(cT);
		c= $(this).find(tx);
		c.animate({bottom:-200},500).animate({bottom:0},300);
		cT = setTimeout(function(){
			c.addClass('slider_act');
		},500);
		$(s+' li').animate({width:smWid}, {duration:500, queue:false});
		$(this).animate({width:actWid}, {duration:500, queue:false});
		activeItem = this;
	});
	$(s+' li').mouseleave(function(){
		c= $(this).find(tx);
		clearTimeout(cT);
		c.animate({bottom:-200},500).removeClass('slider_act').stop(true,true).animate({bottom:0},300);
	});
	$(s).mouseleave(function(){
		$(s+' li').animate({width:defWid}, {duration:500, queue:false});
	});
}