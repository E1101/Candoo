$(document).ready(function(){
	pretty();	
	
});
function pretty(){
	$("a[rel^='prettyPhoto']").prettyPhoto({
		deeplinking : false,
		counter_separator_label : ' of ',
		gallery_markup : '',
		social_tools : '',
		slideshow : false,
		opacity : 0.29
	});
	$("a[rel^='prettyPhoto']").click(function(){
		if($(this).attr('name')!=''){
			$('.pp_pic_holder').addClass($(this).attr('name'));
			init_fields_2();
		}
	});
}