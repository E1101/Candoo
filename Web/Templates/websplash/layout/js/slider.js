sBPreviewImg= '.slider_preview img';
sBThumbImg 	= '.slider_thumbs img';
sBTitle		= '.slider_title';
sBDetails	= '.slider_details';
SliderAnimation  = 800;

$(window).load(function() {
	brilliant_gallery('#slider');
});
function brilliant_gallery(sB){
	$(sB).addClass('slider_cont');
	sBNumItems =  $(sB+' li').size();
	// corns 
	sBCorns = '<div class="corns corns_left_top"/><div class="corns corns_right_top"/><div class="corns corns_left_bottom"/><div class="corns corns_right_bottom"/>';
	// navi 
	sBNavi = $('<div/>',{
		id: 'sB-Navi-wrapper',
		html:$('<div/>',{
			id: 'sB-Navi',
			html: generateThumbs(sB,sBNumItems)+'<div id="sB-Navi-help"><a href="Prev" id="sB-Navi-help-Prev"/><a href="Next" id="sB-Navi-help-Next"/></div>'}) 
		});
	
	$(sB).after(sBNavi).after(sBCorns).after(generatePreview(sB,sBNumItems));
	overThumbs('.roundThumb');	
	prev = 0;
	XO = 0;
	$('.sBTextWrapper:not(:first)').hide(0);
	
	
	intS = setInterval(function(){
		$('#sB-Navi-help-Next').click()
	},5000)
	
	$('.roundThumb').click(function(){
		if(!$(this).hasClass('active')){
			UL = $('#sBSlider');
			p = $('#sB-Navi a.active').attr('sbnum');
			c = $(this).attr('sbnum');
			$('.sBTextWrapper').stop(true,true).fadeOut(0);
			UL.find('li').eq(c).find('.sBTextWrapper').fadeOut(0).delay(SliderAnimation).fadeIn('slow');
			
			UL.stop(true,true).animate({marginLeft:c*(-929)},SliderAnimation,'swing');
			$('#sB-Navi a.active').removeClass('active');
			$(this).addClass('active');
			XO=0;
			
			clearInterval(intS)
			intS = setInterval(function(){
				$('#sB-Navi-help-Next').click()
			},5000)
		}
		return false;
	});
	$('#sB-Navi-help-Prev').click(function(e) {
		XO=1;
		if($('.roundThumb.active').prev().size()){
			$('.roundThumb.active').prev().click()
		}
		else $('.roundThumb:last').click();
		return false;
	});
	$('#sB-Navi-help-Next').click(function(e){
		XO=0-1;
		if($('.roundThumb.active').attr('sbnum')<sBNumItems-1){$('.roundThumb.active').next().click()}
		else {$('.roundThumb:first').click();}
		return false;
	});
	
}

// generate thumbs 
function generateThumbs(sB,sBNumItems){
	sBThumbs='';
	for(i=0;i<sBNumItems;i++){i==0 ? cl=' active' : cl='';sBThumbs=sBThumbs+'<a href="#" class="roundThumb'+cl+'" sBNum="'+i+'"><span class="thumbPrev"><span class="thumbPrevIn"><img class="thumbPrevIm" src="'+$(sB+' li').eq(i).find(sBThumbImg).attr('src')+'" alt=""/></span></span></a>';}
	return sBThumbs;
}
// generate previews
function generatePreview(sB,sBNumItems){
	sBPreviews='';
	for(i=0;i<sBNumItems;i++){
		Li = $(sB+' li').eq(i);
		if(Li.find(sBTitle).size() || Li.find(sBDetails).size()){T = '<div class="sBTextWrapper"><div class="sBTextTitle">'+Li.find(sBTitle).text()+'</div><div class="sBText">'+Li.find(sBDetails).html()+'</div></div>';}
		else T='';
		sBPreviews=sBPreviews+'<li class="sBItem'+i+'"><img class="sBImage" src="'+Li.find(sBPreviewImg).attr('src')+'" alt="" />'+T+'</li>';
	}
	return '<ul id="sBSlider">'+sBPreviews+'</ul>';
}
// over thumbs 
function overThumbs(sbThumb){
	$(sbThumb).hover(function(){
		$(this).find('.thumbPrev').fadeIn(1000);
	});
	$(sbThumb).mouseleave(function(){
		$('.thumbPrev').stop(true,true).fadeOut(0);
	});
}