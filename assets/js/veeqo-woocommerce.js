jQuery(document).ready(function($) {
	
	$(".bridge-connection-view").show();	
	
	$(document).on("click", ".view-additional-info a", function() {
		$(".bridge-installation").slideToggle(500);
	});
	
	// Store registration
	$(".bridge-connection").submit(function(e) {
		$(this).slideUp(500);
		e.preventDefault();
		$(".home-view").hide();
		
		bridge_data = {
			"bridge_url": $(this).find("input[name='bridge_url']").val(),
			"action": "start_veeqo_connection_process"
		}
				
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: bridge_data,
			xhrFields: {
				onprogress: function(response) {
					$(".loading-msg").show();
				}
			},
			success: function(response) {
				bridge_response = $(".bridge-connection-view .bridge-response");
				bridge_response.html(response);
				
				if(bridge_response.find(".failure").length > 0) {
					$(".bridge-connection-view .loading-msg h1").html("An error has occurred!");
					$(".bridge-connection-view .bridge-response .bridge-installation").slideDown(500);
				} else {
					$(".bridge-connection-view .loading-msg h1").html("Your account has been connected!");
				}
			}
		});
		
	});
	
});