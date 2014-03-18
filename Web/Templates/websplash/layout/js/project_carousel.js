function items_carousel_1() {
	$('#block_filtered_items').carouFredSel({
		circular: false,
		infinite: false,
		auto : false,
		width : 726,
		pagination  : {
			container : '#recent_projects_pager',
			items : 3
		}
	});
}
function items_carousel_2() {
	$('#latest_works').carouFredSel({
		circular: false,
		infinite: false,
		auto : false,
		width : 726,
		pagination  : {
			container : '#latest_projects_pager',
			items : 4
		}
	});
}