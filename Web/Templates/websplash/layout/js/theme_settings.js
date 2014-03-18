$(document).ready(function() {
	// show 
	$('#show_settings_button').click(function(e) {
		showSettings();
	});
	// hide
	$('#hide_settings_button').click(function(e) {
		hideSettings();
	});
	
	// select theme type
	var skin_type=$.cookie('skin_type');
	$('#skin_type [value='+skin_type+']').attr('selected','selected');
	$('body').attr('id',skin_type);
	$('#skin_type').change(function(){
		selType();
	});
	// select theme width
	var skin_wid=$.cookie('skin_wid');
	$('#skin_bg [value='+skin_wid+']').attr('selected','selected');
	$('body').addClass(skin_wid).attr('skin_wid',skin_wid);
	if(skin_wid!='boxed') $('.boxes_bg').hide();
	else $('.boxes_bg').show();
	$('#skin_bg').change(function(){
		skinBg();
	});
	// color theme
	var skin_color=$.cookie('skin_color');
	if(skin_color=='null') skin_color='blue';
	$('body').addClass(skin_color).attr('skin_color',skin_color);
	$('#color_scheme a').click(function(e) {
		skin_color = $(this).attr('id');
		bef_skin_color = $('body').attr('skin_color');
		$('body').attr('skin_color',skin_color).removeClass(bef_skin_color).addClass(skin_color);
		$.cookie('skin_color',skin_color);
	});
	// pattern theme
	var skin_pattern=$.cookie('skin_pattern');
	$('body').addClass(skin_pattern).attr('skin_pattern',skin_pattern);
	$('#pattern_scheme a').click(function(e) {
		skin_pattern= $(this).attr('id');
		bef_skin_pattern = $('body').attr('skin_pattern');
		$('body').attr('skin_pattern',skin_pattern).removeClass(bef_skin_pattern).addClass(skin_pattern);
		$.cookie('skin_pattern',skin_pattern);
	});
	
	
	// select theme font
	var skin_font=$.cookie('skin_font');
	$('#skin_font [value='+skin_font+']').attr('selected','selected');
	$('body').addClass(skin_font).attr('skin_font',skin_font);
	$('#skin_font').change(function(){
		skinFont();
	});
	
	$('#reset_styles').click(function(e) {
		resetStyles();
	});
	
	// preloader select 
	$('.main_skin').click(function(){
		skin_type = $(this).attr('name');
		$.cookie('skin_type',skin_type);
		$.cookie('skin_color','blue');
		$.cookie('skin_wid',$(this).parent('.preloader_skin').find('.link_bg:first').attr('name'));
	});
	$('.link_bg').click(function(){
		skin_wid = $(this).attr('name');
		$.cookie('skin_wid',skin_wid);
		$.cookie('skin_color','blue');
		$.cookie('skin_type',$(this).parents('.preloader_skin').find('.main_skin:first').attr('name'));
	});
	
});



function resetStyles(){
	$('#skin_type option:first').attr('selected','selected');
	selType();
	$('#skin_bg option:first').attr('selected','selected');
	skinBg();
	$('#color_scheme a:first').click();
	$('#skin_font option:first').attr('selected','selected');
	skinFont();	
}

function selType(){
	var skin_type = $('#skin_type').val();
	$('body').attr('id',skin_type);
	$.cookie('skin_type',skin_type);
	if($('body').attr('skin_color')=='null'){
		$('body').attr('skin_color','blue').addClass('blue');
		$.cookie('skin_color','blue');
	}
}
function skinBg(){
	var skin_wid = $('#skin_bg').val();
	var bef_skin_wid = $('body').attr('skin_wid');
	$('body').attr('skin_wid',skin_wid).removeClass(bef_skin_wid).addClass(skin_wid);
	$.cookie('skin_wid',skin_wid);
	if(skin_wid!='boxed') $('.boxes_bg').hide();
	else $('.boxes_bg').show();
}
function skinFont(){
	var skin_font = $('#skin_font').val();
	var bef_skin_font = $('body').attr('skin_font');
	$('body').attr('skin_font',skin_font).removeClass(bef_skin_font).addClass(skin_font);
	$.cookie('skin_font',skin_font);
}

function showSettings(){
	$('.theme_settings_container').stop(true,false).animate({left:0,opacity:1});
}
function hideSettings(){
	$('.theme_settings_container').stop(true,false).animate({left:-200,opacity:0});
}