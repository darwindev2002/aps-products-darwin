// post script
(function($) {
	"use strict";
	$(document).on("click", "a.clone-this", function(e) {
		
		// submit post data to clone
		$.ajax({
			url: ajaxurl,
			type: "POST",
			data: {
				id: $(this).attr("data-id"),
				code: $(this).attr("data-code"),
				action: "clone_product"
			},
			success: function(res) {
				if (res == true) {
					// Reload the page
					location.reload();
				}
			}
		});
		e.preventDefault();
	});
	
	// replace brands textarea with select
	$("#the-list").on("click", "a.editinline", function() {
		var b_selected = $(this).parents(".type-aps-products").find(".taxonomy-aps-brands a").text(),
		select_box = "<select class='tax_input_aps-brands' name='tax_input[aps-brands]'>";
		
		$.each(aps_brands, function(k,v) {
			select_box += "<option value='" +v+ "'" +((b_selected == v) ? " selected='selected'" : "")+ ">" +v+ "</option>";
		});
		
		select_box += "</select>";
		$(".tax_input_aps-brands").replaceWith(select_box);
	});
	
	$(".clone_product").each(function() {
		var post_id = $(this).find("a.clone-this").data("id");
		$(this).parent().before("<span>"+ aps_id_name + ": <b>" + post_id +"</b></span>");
	});
})(jQuery);