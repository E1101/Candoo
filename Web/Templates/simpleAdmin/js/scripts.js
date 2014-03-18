jQuery(function(){

	// Add the "&raquo;" to the menu and fix spacings
	jQuery(".menu-header ul li").each(function(){
		if(jQuery(this).children('ul').length>0){
			jQuery(this).find('a:first').append(' &raquo;');
		}
	});
		
		
	// This is the CUFON FONT target. All elements are targeted individually for complete control. Delete or add as you wish., 
   Cufon.replace("h1, h2, h3, h4, h5, h6, span.port_4_title, a.cycle_cta, .sc_button, blockquote, .portfolio_item_4_col span.portfolio_title, a.blog_button, a.tagline_button, .testimonial_quote", {hover: true});
   		   		
				
});
		
jQuery(document).ready(function() {


	// Shortcode animation - Show/Hide
	jQuery("a.show_hide").toggle(
		function(){
			jQuery(this).parent("div").children("div").fadeIn(300);
		},
		function() {
			jQuery(this).parent("div").children("div").fadeOut(300);
		}
	);
   
   
	//Get Rid of Margin on 4th Homepage Box and Footer widgets
	jQuery("#homepage_icon_boxes div:nth-child(4), #homepage_icon_boxes div:nth-child(8)").css("margin-right", "0px");
	jQuery("#footer_inner div:nth-child(4)").css("margin-right", "0px");

	
	//Tagline Button animation
	if (jQuery.browser.msie) {		
	
	jQuery("img.pixastic_logo").fadeTo(700, 0.4);
	
	} else {
		jQuery("a.tagline_button").hover(
			function(){
				jQuery(this).children("span").stop().fadeTo(900, 1);
			},
			function(){
				jQuery(this).children("span").stop().fadeTo(900, 0);
			}
		);	
	}
	
	//Main Navigation animation	
	if (jQuery.browser.msie) {
	
	
		jQuery(".menu-header a, #footer a").fadeTo(700, 0.7);		
		
		
		jQuery("#footer a").hover(
			function(){
				jQuery(this).stop().fadeTo(700, 1);
			},
			function(){
				jQuery(this).stop().fadeTo(700, 0.7);
			}
		);
		
		
		jQuery(".menu-header ul li").hover(
			function(){			
				li_width = jQuery(this).width();    //Get the width of parent li element				
				centred_width = ( ( 108 - (li_width / 2) ) * -1 );    //Calculate displacement for background image			
				centred_width2 = ( ( 74 - (li_width / 2) ) * -1 );    //Calculate displacement for Dropdown				
				jQuery(this).children("span").css("left",  centred_width  + "px");    //Alter absolute left property (centre)				
				jQuery(this).children("ul").css("left",  centred_width2  + "px");    //Alter absolute left property (centre)				
				jQuery(".menu-header ul li ul li ul").css("left",  "143px");    //Alter absolute left property of dropdown level 3				
				jQuery(this).children("a").stop().fadeTo(700, 1); // Fade in text fully 				
			},
			function(){							 
				jQuery(this).children("a").stop().fadeTo(700, 0.7);						
			}
		);		
		
	} else {
	
		jQuery(".menu-header ul li").hover(
			function(){			
				li_width = jQuery(this).width();    //Get the width of parent li element				
				centred_width = ( ( 108 - (li_width / 2) ) * -1 );    //Calculate displacement for background image			
				centred_width2 = ( ( 74 - (li_width / 2) ) * -1 );    //Calculate displacement for Dropdown		
				jQuery(this).prepend(jQuery("<span></span>"));	  //Inject the background image			
				jQuery(".menu-header ul li ul li span").remove();	  //Remover the image from drop downs	
				jQuery(this).children("span").css("left",  centred_width  + "px");    //Alter absolute left property (centre)				
				jQuery(this).children("ul").css("left",  centred_width2  + "px");    //Alter absolute left property (centre)				
				jQuery(".menu-header ul li ul li ul").css("left",  "143px");    //Alter absolute left property of dropdown level 3				
				jQuery(this).children("a").stop().fadeTo(700, 1); // Fade in text fully 
				jQuery(this).children("span:nth-child(1)").stop().fadeTo(700, 0.9); //Fade in the background image
			},
			function(){
				jQuery(this).children("span:nth-child(1)").stop().fadeOut(700);				 
				jQuery(this).children("a").stop().fadeTo(700, 0.7);						
			}
			
		);	
		
		jQuery("#footer a").hover(
			function(){
				jQuery(this).stop().fadeTo(700, 1);
			},
			function(){
				jQuery(this).stop().fadeTo(700, 0.7);
			}
		);
	}
	

	//Dropdown animation
	if (jQuery.browser.msie) {	
		
		jQuery(".menu-header ul li").hover(
			function(){	
				jQuery(this).children("ul").fadeIn(1);			
			},
			function(){
				jQuery(this).children("ul").fadeOut(1); 		
			}
		);

	} else {

		jQuery(".menu-header ul li").hover(
			function(){	
				jQuery(this).children("ul").stop(true, true).slideDown(200);			
			},
			function(){
				jQuery(this).children("ul").animate({opacity: '+=0'}, 100).fadeOut(300); 		
			}
		);
		
	}
		
	
	//Homepage Testimonials	
	jQuery(".testimonial_quote:first, .pixastic_positioner:first img.colour_logo").fadeIn(300);	
	jQuery(".pixastic_positioner").addClass("inactive");	
	jQuery(".pixastic_positioner:first").removeClass("inactive").addClass("active");
	
	jQuery(".pixastic_positioner").hover(
		function(){			
			if(jQuery(this).hasClass("inactive")) {	
				jQuery("img.colour_logo").stop(true, true).fadeOut(300);
				jQuery(".testimonial_quote").stop(true, true).fadeOut(300);
				jQuery(this).children("a").children("img.colour_logo").stop(true, true).fadeIn(300);			
				jQuery(this).parent("li").children(".testimonial_quote").stop(true, true).fadeIn(300);			
			} 
			jQuery(".pixastic_positioner").removeClass("active")
			jQuery(".pixastic_positioner").addClass("inactive")
			jQuery(this).addClass("active");
			jQuery(this).removeClass("inactive");
		}
	);	

	
	// Portfolio 4 Column Animations
	jQuery(".portfolio_item_4_col").hover(
		function(){
		jQuery(this).children("a.frame").stop().animate({ top: "-15px"}, 300 );
		jQuery(this).children(".portfolio_item_4_col span.portfolio_shadow").stop().animate({ bottom: "-7px"}, 300 );
		jQuery(this).children("div.portfolio_item_4_col.description span.portfolio_shadow").stop().animate({ top: "154px"}, 300 );
		jQuery(this).children("a.frame").children("span.portfolio_loading").children("img").stop(true, true).fadeOut(800);
		},
		function(){
		jQuery(this).children("a.frame").stop().animate({ top: "0px"}, 300 );
		jQuery(this).children(".portfolio_item_4_col span.portfolio_shadow").stop().animate({ bottom: "0px"}, 300 );
		jQuery(this).children("div.portfolio_item_4_col.description span.portfolio_shadow").stop().animate({ top: "147px"}, 300 );
		jQuery(this).children("a.frame").children("span.portfolio_loading").children("img").stop(true, true).fadeIn(800);		
		}
	);
	

});	

jQuery(document).ready(function(){	
	
	//The Colourbox Modal Window is initiated here 	
	jQuery(".colourbox-image").colorbox(); //image
	jQuery(".portfolio-modal-anchor").colorbox({iframe:true, innerWidth:1030, innerHeight:560}); //galleria
	jQuery(".video-modal-anchor").colorbox({iframe:true, innerWidth:890, innerHeight:500}); //video

});
