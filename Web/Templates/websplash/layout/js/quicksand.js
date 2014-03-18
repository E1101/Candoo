$(document).ready(function() {
	$('ul.block_filtered_items li').each(function(index, element) {
		$(this).attr('data-type',$(this).attr('title')).attr('data-id','item'+index).removeAttr('title');
	});

  // get the action filter option item on page load
  var $filterType = $('.block_filter a.active').attr('title');
	
  // get and assign the ourHolder element to the
	// $holder varible for use later
  var $holder = $('ul.block_filtered_items');

  // clone all items within the pre-assigned $holder element
  var $data =$holder.clone()

  // attempt to call Quicksand when a filter option
	// item is clicked
	$('.block_filter a').click(function(e) {
		// reset the active class on all the buttons
		$('.block_filter a').removeClass('active');
		
		// assign the class of the clicked filter option
		// element to our $filterType variable
		var $filterType = $(this).attr('title');
		$(this).addClass('active');
		
		if ($filterType == 'all') {
			// assign all li items to the $filteredData var when
			// the 'All' filter option is clicked
			var $filteredData = $data.find('li');
		} 
		else {
			// find all li elements that have our required $filterType
			// values for the data-type element
			var $filteredData = $data.find('li[data-type=' + $filterType + ']');
		}
		
		// call quicksand and assign transition parameters
		$holder.quicksand($filteredData, {
			duration: 800,
			easing: 'easeInOutQuad'
		}, function(){pretty();slow_hover('.block_general_pic', '.block_hover') ;})
		return false;
	});
});