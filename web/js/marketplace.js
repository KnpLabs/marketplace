jQuery(function($) {
	var field, parent, preview_link, comment_link, comment_field_container, preview_container;
	
	field = $(".comment_field");

	comment_field_container = $(".comment_field_container");

	preview_container = $(".comment_preview");

	preview_link = $("a.show_preview_link");
	comment_link = $("a.write_comment_link");

	comment_link.bind("click", function(event) {
		event.preventDefault();

		preview_link.parent("li").removeClass("active");
		comment_link.parent("li").addClass("active");
		comment_field_container.show();
		preview_container.empty().hide();
	})

	preview_link.bind("click", function(event) { 
		event.preventDefault();
		
		comment_link.parent("li").removeClass("active");
		preview_link.parent("li").addClass("active");

		comment_field_container.hide();
		preview_container.text("Loading preview...");

		$.post("/index.php/comment/preview", {
			"markdown_content" : field.val() 
		}, function (response, textStatus, connection) {
			preview_container.show().html(response);
		}, "html");

	}).appendTo(parent);
});