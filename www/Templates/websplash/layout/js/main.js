function init_menu() {
	$('#main_menu ul  li').hover(
		function() {
			$(this).find('ul:first').slideToggle('fast');
			
		},
		function() {
			$(this).find('ul:first').hide();
		}
	);
	
	$('#main_menu li ul li a').hover(
		function() {
			$(this).animate({paddingLeft : 18}, 200).css('color','#616161');
		},
		function() {
			$(this).animate({paddingLeft : 12}, 200).css('color','#949494');
		}
	);
	
	$('#main_menu > ul > li > ul').prepend('<li class="top"></li>');
	$('#main_menu > ul > li > ul > li > ul').prepend('<li class="top"></li>');
}

function init_search_field() {
	$('#search_show').live('click', function() {
		$(this).hide();
		$('#search_form_block').show().animate({
			width : 168
		},300);
	});
}

function init_fields() {
	$('.w_def_text').each(function() {
		var text = $(this).attr('title');
		var html = '<span>' + text + '</span>';
		$(this).parent().append(html);
		
		if($(this).val() == '') {
			$(this).hide();
			$(this).next().show();
		}
		else {
			$(this).css({'display' : 'block'});
			$(this).next().hide();
		}
	});
	
	$('.w_def_text').live('blur', function() {
		if($(this).val() == '') {
			$(this).hide();
			$(this).next().show();
		}
	});
	$('.w_def_text ~ span').live('click', function() {
		$(this).hide();
		$(this).prev().css({'display' : 'block'}).focus();
	});
	
	$('.w_focus').focus(function(){
		$(this).parent().addClass('focused');
	});
	
	$('.w_focus').blur(function() {
		$(this).parent().removeClass('focused');
	});
}
function init_fields_2(){
	setTimeout(function(){
		$('.w_focus').focus(function(){
			$(this).parent().addClass('focused');
		});
		
		$('.w_focus').blur(function() {
			$(this).parent().removeClass('focused');
		});
		$('.pp_content').animate({height : $('.pp_content').height()-41},'fast')
	},800)
}

function init_filter() {
	
	if(!$('.block_portfolio_2c').size()){
	$('body').append('<div id="filtered_store" />');
	$('#filtered_store').hide();
	var callback = $('#block_filtered_items').hasClass('items_carousel_1');
	
	$('.block_filter ul li a').live('click', function() {
			
			var filter = $(this).attr('title');
			
			$('.block_filter ul li a').removeClass('active');
			$(this).addClass('active');
			
			if(filter == 'all') {
				$('.filtering_item').attr('rel', 'passed');
			}
			else {
				$('.filtering_item').each(function() {
					if($(this).hasClass(filter)) {
						$(this).attr('rel', 'passed');
					}
					else {
						$(this).attr('rel', 'unpassed');
					}
				});
			}
			
			
			$('.filtering_item[rel="passed"]').appendTo('#block_filtered_items');
			$('.filtering_item[rel="unpassed"]').appendTo('#filtered_store');
			
			
			if(callback) items_carousel_1();
	});
		
	$('.block_filter ul li a').eq(0).click();
	}
}

function init_r_corners() {
	$('.r_conner_pic').each(function() {
		var path = $(this).attr('src');
		$(this).wrap('<span class="r_conner_wrapper" />');
		$(this).parent().css({'background' : 'url(' + path + ') no-repeat'});
		$(this).css('opacity',0);
	});
}

function init_pricing_table() {
	$('.table_type_3 td, .table_type_3 th').hover(
		function() {
			var num = $(this).parent().find('td, th').index(this);
					$(this).parent().parent().find('th').eq(num).addClass('active');
					$(this).parents('table').find('tr').find('td').eq(num).addClass('active');
		},
		function() {
			var num = $(this).parent().find('td, th').index(this);
					$(this).parent().parent().find('th').eq(num).removeClass('active');
					$(this).parents('table').find('tr').find('td').eq(num).removeClass('active');
		}
	);
}

function init_blog_comments() {
	$('.comment.replied > .v_line').each(function() {
		var height = $(this).parent().outerHeight() - 30 - $(this).parent().find('> .comment:last').outerHeight();
		var left = parseInt($(this).parent().css('padding-left')) + 27;
		$(this).css({'height' : height + 'px', 'left' : left + 'px'});
	});
}
function slow_hover(b,bb) {
	$(bb).css({opacity:0,display:'block'});
	$(b).mouseover(function(){
			$(this).find(bb).stop(true,false).animate({opacity:1},'fast');
		}).mouseleave(function(){
			$(this).find(bb).animate({opacity:0},'slow');
		}
	);
}

$(document).ready(function() {
	init_filter();
	if($('#latest_works').size())items_carousel_2();
	if($('.block_general_pic').size()) slow_hover('.block_general_pic', '.block_hover');
	init_menu();
	init_fields();
	init_search_field();
	init_r_corners();
	init_pricing_table();
	init_blog_comments();
});

$(window).load(function() {
});