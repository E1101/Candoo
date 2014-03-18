//Set jQuery into no conflict mode
var $jq = jQuery.noConflict();

$jq(document).ready(function(){
	// in contact.html
	$jq("#main-contact-form").submit(function(){
		// 'this' refers to the current submitted form
		var str = $jq(this).serialize();
		$jq.ajax({
			type: "POST",
			url: "contact.php",
			data: str,
			success: function(msg){
				$jq("#note").ajaxComplete(function(event, request, settings){
					if(msg == 'OK'){ // Message Sent? Show the 'Thank You' message and hide the form
						result = '<div class="notification_ok">Your message has been sent. Thank you!</div>';
						$jq("#fields").hide();
					} else {
						result = msg;
					}
					$jq(this).html(result);
				});
			}
		});
		return false;
	});
	
	
});