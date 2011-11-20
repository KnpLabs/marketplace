jQuery(function($) {
	$(document).ready(function() {
		var 
			// URL we send the ajax request to 
			url = $(".show_preview_link").data("url"),
			// Contain transformed HTML
			preview_container = $("#preview"),
			// Textara the comment is typed into
			field = $(".comment_field");

		$(".preview_tabs").show().tabs().bind("change", function(event) {
			var target = $(event.target);
			
			// If we clicked on the preview link		
			if ($(event.target).hasClass("show_preview_link")) {
				preview_container.text("Loading preview...");

				$.post(url, { "markdown_content" : field.val() }, function (response, textStatus, connection) {
					preview_container.html(response);
				}, "html");
			}		
		});
	});
});