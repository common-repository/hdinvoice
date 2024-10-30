jQuery(document).ready(function() {
	console.log("HDInvoice loaded");
	hdv_render_dashboard_chart();
});

// General Functions
// ______________________________________________

// Dashboard Customer Quick Search
jQuery("#search").keyup(function() {
	var startSearch = jQuery(this).val().length;
	if (startSearch >= 3) {
		var searchTerm = jQuery(this)
			.val()
			.toLowerCase();
		jQuery("#hdv_customers_list .customer_item").hide();
		jQuery("#hdv_customers_list .customer_item").each(function() {
			var customerName = jQuery(this)
				.attr("data-name")
				.toLowerCase();
			if (customerName.indexOf(searchTerm) != -1) {
				jQuery(this).show();
			}
		});
	} else {
		jQuery(".customer_item").show();
	}
});

// accordion
jQuery("#content").on("click", ".hdv_accordion h3", function(event) {
	jQuery(this)
		.next("div")
		.toggle(600);
});

// Show model popup
function hdv_show_model(title, content, button1, button2) {
	content = "<h2>" + title + "</h2>" + "<p>" + content + "</p>";
	var footer = "";
	if (button1 != "" && button1 != null) {
		var button1ID = button1.replace(/\s+/g, "-").toLowerCase(); // create button ID based on button name
		footer =
			'<div id = "' +
			button1ID +
			'" class = "hd_button" style = "display: inline-block">' +
			button1 +
			"</div>";
	}
	if (button2 != "" && button2 != null) {
		var button2ID = button2.replace(/\s+/g, "-").toLowerCase();
		footer =
			footer +
			' &nbsp;&nbsp;&nbsp;<div id = "' +
			button2ID +
			'" class = "hd_button" style = "display: inline-block; background: #3fb7f4">' +
			button2 +
			"</div>";
	}
	jQuery("#hdv_model_content").html(content);
	jQuery("#hdv_model_footer").html(footer);
	jQuery("#hdv_model")
		.addClass("flipInY")
		.show();
}

function hdv_render_dashboard_chart() {
	var chart = new CanvasJS.Chart("chartContainer", {
		title: {
			text: ""
		},
		backgroundColor: "transparent",
		scaleShowLabels: false,
		scaleFontSize: 0,
		axisY: {
			valueFormatString: " ",
			lineThickness: 0,
			tickThickness: 0,
			gridThickness: 0,
			margin: 0,
			valueFormatString: " "
		},
		axisX: {
			valueFormatString: " ",
			lineThickness: 0,
			tickThickness: 0,
			gridThickness: 0,
			margin: -10,
			valueFormatString: " ",
			labelFontSize: 0,
			labelFontColor: "#262835"
		},
		animationEnabled: true,
		animationDuration: 2500,
		data: [
			{
				type: "splineArea",
				color: "rgba(164,138,212,.7)",
				dataPoints: hdv_chart_data2
			},
			{
				type: "splineArea",
				color: "rgba(78,208,209,.7)",
				dataPoints: hdv_chart_data
			}
		]
	});

	chart.render();
}

// WP Media Uploader
var file_frame_featured_image;
jQuery("#content").on("click", ".hdv_upload", function(event) {
	event.preventDefault();

	// get upload element id
	var imageId = jQuery(this).attr("id");

	// If the media frame already exists, reopen it.
	if (file_frame_featured_image) {
		file_frame_featured_image.open();
		return;
	}

	// Create the media frame.
	file_frame_featured_image = wp.media.frames.file_frame = wp.media({
		title: "Upload a logo",
		button: {
			text: "SET LOGO"
		},
		multiple: false
	});

	// When an image is selected, run a callback.
	file_frame_featured_image.on("select", function() {
		attachment = file_frame_featured_image
			.state()
			.get("selection")
			.first()
			.toJSON();
		imgURL = attachment.url;
		jQuery("#" + imageId).attr("src", imgURL);
		jQuery("#" + imageId).attr("data-attachment-id", attachment.id);
	});

	file_frame_featured_image.open();
});

//  #sidebar_1
jQuery("#sidebar_1 ul li").click(function() {
	jQuery("#sidebar_1 ul li.active").removeClass("active");
	jQuery(this).addClass("active");
	jQuery(".customer_item.active").removeClass("active");
});

// tab navigation
jQuery("#content").on("click", "#hdv_tabs li", function(event) {
	jQuery("#hdv_tabs li").removeClass("hdv_active_tab");
	jQuery(this).addClass("hdv_active_tab");
	var hdvContent = jQuery(this).attr("data-hdv-content");
	jQuery(".hdv_tab_active").fadeOut();
	jQuery(".hdv_tab").removeClass("hdv_tab_active");
	jQuery("#" + hdvContent)
		.delay(250)
		.fadeIn();
	jQuery("#" + hdvContent).addClass("hdv_tab_active");
});

// load dashboard when selected
jQuery("#hdv_nav_dashboard").click(function() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_view_dashboard",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val()
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("HD INVOICE DASHBOARD");
			hdv_render_dashboard_chart();
			jQuery("#hdv_tools_list").hide();
			jQuery("#hdv_customers_list").show();
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// load tools when selected
jQuery("#hdv_nav_tools").click(function() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_view_tools",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val()
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("HD INVOICE TOOLS");
			jQuery("#hdv_customers_list").hide();
			jQuery("#hdv_tools_list").fadeIn();
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// detect when a file was selected and update the label
jQuery("#content").on("change", "#hdv_file_upload", function(event) {
	var filename = jQuery("#hdv_file_upload")
		.val()
		.split("\\")
		.pop();
	console.log(jQuery("#hdv_file_upload").val());
	jQuery("#hdv_file_upload + label > span").html(filename);
});

// Continue Import
jQuery("#content").on("click", "#hdv_continue_import", function() {
	jQuery("#hdv_check_before_import").fadeOut();
	var hdv_csv_path1 = hdv_csv_path;
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	jQuery("h1").text("IMPORTING: DO NOT LEAVE THIS PAGE");
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_continue_import",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_csv_path: hdv_csv_path1,
			counter: 0
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
		},
		error: function() {
			console.log("Permission denied");
			jQuery("h1").text("IMPORT ERROR");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// Continue Import
function hdv_continue_import(count) {
	var hdv_csv_path1 = hdv_csv_path;
	var ajaxUrl = hdv_ajax;
	jQuery("h1").text("STILL IMPORTING... DO NOT LEAVE THIS PAGE");
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_continue_import",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_csv_path: hdv_csv_path1,
			counter: count
		},
		url: ajaxUrl,
		success: function(data) {
			if (
				data ==
				"<h2>All Invoices have been uploaded</h2><p>This page will refresh in 5 seconds...</p>"
			) {
				jQuery("h1").text("IMPORT COMPLETE");

				function reload() {
					window.location.href = hdv_dashboard_url;
				}
				setTimeout(reload, 5000);
			} else {
				jQuery("#content").prepend(data);
			}
		},
		error: function() {
			console.log("Permission denied");
			jQuery("h1").text("IMPORT ERROR");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
}

// for when settings is selected
// load the settings page
jQuery("#hdv_nav_settings").click(function() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_view_settings",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val()
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("HDInvoice Settings");
			//init Visual Editors
			function init_visual_edutor() {
				jQuery(".hdv_visual_editor").trumbowyg({
					btns: [
						"strong",
						"em",
						"|",
						"unorderedList",
						"orderedList",
						"|",
						"link",
						"viewHTML"
					],
					autogrow: true,
					semantic: true,
					minimalLinks: true
				});
			}
			setTimeout(init_visual_edutor, 10);

			jQuery("#hdv_tools_list").hide();
			jQuery("#hdv_customers_list").show();
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// save the settings
jQuery("#content").on("click", "#save_settings", function() {
	jQuery("#hdv_loading").fadeIn();
	jQuery(this).hide();
	var ajaxUrl = hdv_ajax;

	// get settings global values
	var hdv_setting_currency_symbol = jQuery(
		"#hdv_setting_currency_symbol"
	).val();
	if (jQuery("#hdv_setting_currency_position").is(":checked")) {
		var hdv_setting_currency_position = "right";
	} else {
		var hdv_setting_currency_position = "left";
	}
	var hdv_setting_tax_name1 = jQuery("#hdv_setting_tax_name1").val();
	var hdv_setting_tax_name2 = jQuery("#hdv_setting_tax_name2").val();
	var hdv_setting_tax_name3 = jQuery("#hdv_setting_tax_name3").val();
	var hdv_setting_tax_percent1 = jQuery("#hdv_setting_tax_percent1").val();
	var hdv_setting_tax_percent2 = jQuery("#hdv_setting_tax_percent2").val();
	var hdv_setting_tax_percent3 = jQuery("#hdv_setting_tax_percent3").val();
	var hdv_setting_invoice_start = jQuery("#hdv_setting_invoice_start").val();

	// get setttings company values
	var hdv_setting_name = jQuery("#hdv_setting_name").val();
	var hdv_setting_email = jQuery("#hdv_setting_email").val();
	var hdv_setting_website = jQuery("#hdv_setting_website").val();
	var hdv_setting_logo = jQuery("#hdv_company_logo_img").attr(
		"data-attachment-id"
	);
	var hdv_setting_phone = jQuery("#hdv_setting_phone").val();
	var hdv_setting_address = jQuery("#hdv_setting_address").val();
	var hdv_setting_address2 = jQuery("#hdv_setting_address2").val();
	var hdv_setting_city = jQuery("#hdv_setting_city").val();
	var hdv_setting_state = jQuery("#hdv_setting_state").val();
	var hdv_setting_country = jQuery("#hdv_setting_country").val();
	var hdv_setting_zip = jQuery("#hdv_setting_zip").val();
	var hdv_setting_info = jQuery("#hdv_setting_info").trumbowyg("html");

	// get customizer values
	var hdv_setting_layout = jQuery("#hdv_setting_layout").val();

	if (jQuery("#hdv_setting_layout_logo").is(":checked")) {
		var hdv_setting_layout_logo = "disable";
	} else {
		var hdv_setting_layout_logo = "enable";
	}
	if (jQuery("#hdv_setting_layout_address").is(":checked")) {
		var hdv_setting_layout_address = "disable";
	} else {
		var hdv_setting_layout_address = "enable";
	}
	if (jQuery("#hdv_setting_layout_love").is(":checked")) {
		var hdv_setting_layout_love = "enable";
	} else {
		var hdv_setting_layout_love = "disable";
	}

	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_save_settings",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_setting_currency_symbol: hdv_setting_currency_symbol,
			hdv_setting_currency_position: hdv_setting_currency_position,
			hdv_setting_tax_name1: hdv_setting_tax_name1,
			hdv_setting_tax_name2: hdv_setting_tax_name2,
			hdv_setting_tax_name3: hdv_setting_tax_name3,
			hdv_setting_tax_percent1: hdv_setting_tax_percent1,
			hdv_setting_tax_percent2: hdv_setting_tax_percent2,
			hdv_setting_tax_percent3: hdv_setting_tax_percent3,
			hdv_setting_invoice_start: hdv_setting_invoice_start,
			hdv_setting_name: hdv_setting_name,
			hdv_setting_email: hdv_setting_email,
			hdv_setting_website: hdv_setting_website,
			hdv_setting_logo: hdv_setting_logo,
			hdv_setting_phone: hdv_setting_phone,
			hdv_setting_address: hdv_setting_address,
			hdv_setting_address2: hdv_setting_address2,
			hdv_setting_city: hdv_setting_city,
			hdv_setting_state: hdv_setting_state,
			hdv_setting_country: hdv_setting_country,
			hdv_setting_zip: hdv_setting_zip,
			hdv_setting_info: hdv_setting_info,
			hdv_setting_layout: hdv_setting_layout,
			hdv_setting_layout_logo: hdv_setting_layout_logo,
			hdv_setting_layout_address: hdv_setting_layout_address,
			hdv_setting_layout_love: hdv_setting_layout_love
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("Settings Updated");
			//init Visual Editors
			function init_visual_edutor() {
				jQuery(".hdv_visual_editor").trumbowyg({
					btns: [
						"strong",
						"em",
						"|",
						"unorderedList",
						"orderedList",
						"|",
						"link",
						"viewHTML"
					],
					autogrow: true,
					semantic: true,
					minimalLinks: true
				});
			}
			setTimeout(init_visual_edutor, 10);
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#save_settings").show();
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// layout selection
jQuery("#content").on("click", ".hdv_setting_layout", function() {
	jQuery(".hdv_setting_layout").removeClass("hdv_selected_layout");
	jQuery(this).addClass("hdv_selected_layout");
	let layout = jQuery(this).attr("data-layout");
	jQuery("#hdv_setting_layout").val(layout);
});

// Customer functions
// ______________________________________________

// Add new customer load
jQuery("#add_customer").click(function() {
	jQuery("#hdv_loading").fadeIn();
	jQuery("#sidebar_1 ul li.active").removeClass("active");
	jQuery("#hdv_nav_dashboard").addClass("active");
	jQuery("#sidebar_2 #hdv_customers_list .customer_item_first").remove(); // remove the notice if this is the first customer added
	var ajaxUrl = hdv_ajax;
	jQuery(this).hide();
	jQuery(".customer_item.active").removeClass("active");
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_add_new_customer",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val()
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("Adding a new customer");
			//init Visual Editors
			function init_visual_edutor() {
				jQuery(".hdv_visual_editor").trumbowyg({
					btns: [
						"strong",
						"em",
						"|",
						"unorderedList",
						"orderedList",
						"|",
						"link",
						"viewHTML"
					],
					autogrow: true,
					semantic: true,
					minimalLinks: true
				});
			}
			setTimeout(init_visual_edutor, 10);
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#add_customer").show();
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// edit customer
jQuery("#content").on("click", "#edit_customer", function(event) {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	var hdv_customer_id = jQuery(this).attr("data-customer-id");
	var hdv_customer_name = jQuery("#header h1").html();
	jQuery(this).hide();
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_edit_customer",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_customer_id: hdv_customer_id
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("Editing " + hdv_customer_name);
			//init Visual Editors
			function init_visual_edutor() {
				jQuery(".hdv_visual_editor").trumbowyg({
					btns: [
						"strong",
						"em",
						"|",
						"unorderedList",
						"orderedList",
						"|",
						"link",
						"viewHTML"
					],
					autogrow: true,
					semantic: true,
					minimalLinks: true
				});
			}
			setTimeout(init_visual_edutor, 10);
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#edit_customer").show();
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// save customer
jQuery("#content").on("click", "#save_customer", function() {
	if (jQuery("#hdv_customer_name").val() != "") {
		jQuery(this).hide();
		save_new_customer();
	} else {
		jQuery("#hdv_customer_name").addClass("hd_error");
		jQuery("#content").animate(
			{
				scrollTop: 0
			},
			"slow"
		);
	}
});

function save_new_customer() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	var hdv_customer_name = jQuery("#hdv_customer_name").val();
	var hdv_customer_email = jQuery("#hdv_customer_email").val();
	var hdv_customer_website = jQuery("#hdv_customer_website").val();
	var hdv_customer_phone = jQuery("#hdv_customer_phone").val();
	var hdv_customer_address = jQuery("#hdv_customer_address").val();
	var hdv_customer_address2 = jQuery("#hdv_customer_address2").val();
	var hdv_customer_city = jQuery("#hdv_customer_city").val();
	var hdv_customer_state = jQuery("#hdv_customer_state").val();
	var hdv_customer_country = jQuery("#hdv_customer_country").val();
	var hdv_customer_zip = jQuery("#hdv_customer_zip").val();
	var hdv_customer_tax = jQuery("#hdv_customer_tax").val();
	var hdv_customer_info = jQuery("#hdv_customer_info").trumbowyg("html");
	var hdv_customer_logo = jQuery("#hdv_company_logo_img").attr(
		"data-attachment-id"
	);
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_save_new_customer",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			// grab all form data
			hdv_customer_name: hdv_customer_name,
			hdv_customer_email: hdv_customer_email,
			hdv_customer_website: hdv_customer_website,
			hdv_customer_phone: hdv_customer_phone,
			hdv_customer_address: hdv_customer_address,
			hdv_customer_address2: hdv_customer_address2,
			hdv_customer_city: hdv_customer_city,
			hdv_customer_state: hdv_customer_state,
			hdv_customer_country: hdv_customer_country,
			hdv_customer_zip: hdv_customer_zip,
			hdv_customer_tax: hdv_customer_tax,
			hdv_customer_info: hdv_customer_info,
			hdv_customer_logo: hdv_customer_logo
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			// find the customer name and ID
			var customer_name = jQuery("#content")
				.find(".hd_saved_customer_name")
				.html();
			var customer_id = jQuery("#content")
				.find(".hd_saved_customer_id")
				.html();
			// push the new customer to sidebar customer list
			var customer_data =
				'<div class = "customer_item active" data-id = "' +
				customer_id +
				'" data-name = "' +
				customer_name +
				'">' +
				customer_name +
				"</div>";
			jQuery("#sidebar_2 #hdv_customers_list").prepend(customer_data);
			// show success title
			jQuery("h1").text(customer_name + " has been added");
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
}

// save current customer
jQuery("#content").on("click", "#save_current_customer", function() {
	if (jQuery("#hdv_customer_name").val() != "") {
		jQuery(this).hide();
		save_current_customer();
	} else {
		jQuery("#hdv_customer_name").addClass("hd_error");
		jQuery("#content").animate(
			{
				scrollTop: 0
			},
			"slow"
		);
	}
});

function save_current_customer() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	var hdv_customer_id = jQuery(
		"#sidebar_2 #hdv_customers_list .customer_item.active"
	).attr("data-id");
	var hdv_customer_name = jQuery("#hdv_customer_name").val();
	var hdv_customer_email = jQuery("#hdv_customer_email").val();
	var hdv_customer_website = jQuery("#hdv_customer_website").val();
	var hdv_customer_phone = jQuery("#hdv_customer_phone").val();
	var hdv_customer_address = jQuery("#hdv_customer_address").val();
	var hdv_customer_address2 = jQuery("#hdv_customer_address2").val();
	var hdv_customer_city = jQuery("#hdv_customer_city").val();
	var hdv_customer_state = jQuery("#hdv_customer_state").val();
	var hdv_customer_country = jQuery("#hdv_customer_country").val();
	var hdv_customer_zip = jQuery("#hdv_customer_zip").val();
	var hdv_customer_tax = jQuery("#hdv_customer_tax").val();
	var hdv_customer_info = jQuery("#hdv_customer_info").trumbowyg("html");
	var hdv_customer_logo = jQuery("#hdv_company_logo_img").attr(
		"data-attachment-id"
	);
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_save_current_customer",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			// grab all form data
			hdv_customer_id: hdv_customer_id,
			hdv_customer_name: hdv_customer_name,
			hdv_customer_email: hdv_customer_email,
			hdv_customer_website: hdv_customer_website,
			hdv_customer_phone: hdv_customer_phone,
			hdv_customer_address: hdv_customer_address,
			hdv_customer_address2: hdv_customer_address2,
			hdv_customer_city: hdv_customer_city,
			hdv_customer_state: hdv_customer_state,
			hdv_customer_country: hdv_customer_country,
			hdv_customer_zip: hdv_customer_zip,
			hdv_customer_tax: hdv_customer_tax,
			hdv_customer_info: hdv_customer_info,
			hdv_customer_logo: hdv_customer_logo
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			// find the customer name and ID
			var customer_name = jQuery("#content")
				.find(".hd_saved_customer_name")
				.html();
			// show success title
			jQuery("h1").text(customer_name + " has been updated");
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
}

// view customer
jQuery("#sidebar_2 #hdv_customers_list, #content").on(
	"click",
	".customer_item, .dashboard_item",
	function() {
		jQuery("#hdv_loading").fadeIn();
		jQuery(".customer_item").show();
		jQuery("#sidebar_1 ul li.active").removeClass("active");
		jQuery("#hdv_nav_dashboard").addClass("active");
		var ajaxUrl = hdv_ajax;
		var hdv_customer_id = jQuery(this).attr("data-id");
		var hdv_customer_name = jQuery(this).attr("data-name");
		jQuery(".customer_item.active").removeClass("active");
		jQuery(".customer_item[data-id*='" + hdv_customer_id + "']").addClass(
			"active"
		);
		var isDashboardItem = false;
		if (jQuery(this).hasClass("dashboard_item")) {
			isDashboardItem = true;
		}
		jQuery.ajax({
			type: "POST",
			data: {
				action: "hdv_view_customer",
				hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
				hdv_customer_id: hdv_customer_id
			},
			url: ajaxUrl,
			success: function(data) {
				jQuery("#content").html(data);
				jQuery("h1").text(hdv_customer_name);
			},
			error: function() {
				console.log("Permission denied");
			},
			complete: function() {
				// complete
				jQuery("#add_customer").show();
				jQuery("#hdv_loading").fadeOut();
				jQuery("#content").animate(
					{
						scrollTop: 0
					},
					"slow"
				);
				if (isDashboardItem || jQuery("#search").val() != "") {
					// auto scroll customer list to customer position
					var hdv_customer_top =
						jQuery(".customer_item.active")[0].offsetTop -
						jQuery("#hdv_customers_list")[0].offsetTop;
					jQuery("#sidebar_2").animate(
						{
							scrollTop: hdv_customer_top
						},
						"slow"
					);
					jQuery("#search").val("");
				}
			}
		});
	}
);

// Invoices
// ______________________________________________

// Add invoice load
jQuery("#content").on("click", "#add_invoice", function() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	var hdv_customer_id = jQuery(
		"#sidebar_2 #hdv_customers_list .customer_item.active"
	).attr("data-id");
	var hdv_customer_name = jQuery(
		"#sidebar_2 #hdv_customers_list .customer_item.active"
	).attr("data-name");
	jQuery(this).hide();
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_add_new_invoice",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_customer_id: hdv_customer_id
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("Adding invoice to " + hdv_customer_name);
			//init Visual Editors
			function init_visual_edutor() {
				jQuery(".hdv_visual_editor").trumbowyg({
					btns: [
						"strong",
						"em",
						"|",
						"unorderedList",
						"orderedList",
						"|",
						"link",
						"viewHTML"
					],
					autogrow: true,
					semantic: true,
					minimalLinks: true
				});
			}
			setTimeout(init_visual_edutor, 10);
			// set the tax percent in the total amount owed label
			hdv_set_invoice_tax_percent();
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#add_invoice").show();
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// Invoice Tax Calculations
function hdv_set_invoice_tax_percent() {
	var hdv_tax = hdv_tax_percent;
	jQuery("#tax_p").html(hdv_tax_percent);
}

// if user selects disble tax
jQuery("#content").on("click", "#hdv_invoice_disable_tax", function() {
	var hdv_tax = hdv_tax_percent;
	var hdv_default_tax = hdv_tax_percent_default;
	if (jQuery("#hdv_invoice_disable_tax").is(":checked")) {
		jQuery("#tax_p").html("0");
	} else {
		if (hdv_tax == 0 && hdv_default_tax != 0) {
			jQuery("#tax_p").html(hdv_default_tax);
		} else {
			jQuery("#tax_p").html(hdv_tax);
		}
	}
	hdv_recalculate_amount_owed();
});

function hdv_recalculate_amount_owed() {
	var hdv_tax = parseInt(jQuery("#tax_p").html());
	var hdv_invoice_subtotal = jQuery("#hdv_invoice_subtotal").val();
	if (!hdv_invoice_subtotal) {
		hdv_invoice_subtotal = 0;
	} else {
		hdv_invoice_subtotal = parseFloat(hdv_invoice_subtotal);
	}
	var hdv_invoice_paid = jQuery("#hdv_invoice_paid").val();
	if (!hdv_invoice_paid) {
		hdv_invoice_paid = 0;
	} else {
		hdv_invoice_paid = parseFloat(hdv_invoice_paid);
	}
	// calculate tax values
	var hdv_tax_p = hdv_tax / 100;
	var hdv_tax_a = hdv_invoice_subtotal * hdv_tax_p;
	var hdv_amount_owed = hdv_invoice_subtotal + hdv_tax_a - hdv_invoice_paid;
	jQuery("#hdv_invoice_total").val(hdv_amount_owed.toFixed(2));
}

// when subtotal or amount paid is changed, recalculate total
jQuery("#content").on(
	"change",
	"#hdv_invoice_subtotal, #hdv_invoice_paid",
	function() {
		var data = jQuery(this).val();
		data = parseFloat(data).toFixed(2);
		jQuery(this).val(data);
		hdv_recalculate_amount_owed();
	}
);

// change invoice publish date
jQuery("#content").on("click", "#hd_enable_date", function() {
	//jQuery("#hdv_invoice_publish_date, #date_padding").toggle();
	hdv_date_picker();
});

function hdv_date_picker() {
	let month = 0;
	let day = 0;
	let year = 0;

	jQuery(".hd_birthdate_picker_model").fadeIn();
	jQuery(".hd_birthdate_picker_model_month").show();

	// on month select
	jQuery(".hd_birthdate_picker_model_month .hd_birthdate_item").click(
		function(div) {
			month = jQuery(this).attr("data-id");
			jQuery(".hd_birthdate_picker_model_month").hide();
			jQuery(".hd_birthdate_picker_model_day").fadeIn();
		}
	);

	// on day select
	jQuery(".hd_birthdate_picker_model_day .hd_birthdate_item").click(function(
		div
	) {
		day = jQuery(this).attr("data-id");
		jQuery(".hd_birthdate_picker_model_day").hide();
		jQuery(".hd_birthdate_picker_model_year").fadeIn();
	});

	// on year select
	jQuery(".hd_birthdate_picker_model_year").on(
		"click",
		".hd_birthdate_item",
		function(event) {
			year = jQuery(this).attr("data-id");
			jQuery(".hd_birthdate_picker_model_year").hide();
			jQuery(".hd_birthdate_picker_model").fadeOut();
			jQuery("#hdv_invoice_publish_date").val(
				year + "-" + month + "-" + day
			);
			jQuery("#hd_enable_date").html(year + "-" + month + "-" + day);
		}
	);

	function hd_populate_years() {
		jQuery(".hd_birthdate_picker_model_year").html("");
		let currentTime = new Date();
		let year = currentTime.getFullYear() + 1;
		let data = "";
		let x = 0;
		for (i = 0; i < 80; i++) {
			x++;
			year = parseInt(year) - parseInt(1);
			if (x != 7) {
				data =
					'<div class="hd_birthdate_item one_seventh" data-id="' +
					year +
					'">' +
					year +
					"</div>";
			} else {
				x = 0;
				data =
					'<div class="hd_birthdate_item one_seventh last" data-id="' +
					year +
					'">' +
					year +
					'</div><div class = "clear"></div>';
			}
			jQuery(".hd_birthdate_picker_model_year").append(data);
		}
		jQuery(".hd_birthdate_picker_model_year").append(
			'<div class = "clear"></div>'
		);
	}

	hd_populate_years();
}

// Add Line Item
jQuery("#content").on("click", "#hdv_add_line_item", function() {
	var data =
		'\
	<div class = "hdv_line_item">\
		<div class = "one_half">\
			<div class = "hdv_row">\
				<input type = "text" class = "hdv_input hdv_line_item_name hdv_required" placeholder = "line item description"/>\
			</div>\
		</div>\
		<div class = "one_half last">\
			<div class = "hdv_row">\
				<input type = "text" class = "hdv_input hdv_line_item_value hdv_required" placeholder = "0.00"/>\
				<div class = "hdv_close">&nbsp;</div>\
			</div>\
		</div>\
		<div class = "clear"></div>\
	</div>';
	jQuery("#hdv_line_items").append(data);

	// set padding for better spacing when there are line items
	jQuery("#hdv_line_items").css("padding-top", "22px");

	// if this is the first line-item, show the line item subtotal
	if (jQuery(".hdv_line_item").length == 1) {
		jQuery("#hdv_line_item_subtotal").fadeIn();
	}
});

// remove line item
jQuery("#content").on("click", ".hdv_close", function() {
	var removeDiv = jQuery(this)
		.parent()
		.parent()
		.parent();
	jQuery(removeDiv)
		.fadeOut(250)
		.delay(251)
		.remove();

	function is_last_line_item() {
		if (jQuery(".hdv_line_item").length <= 0) {
			// set padding for better spacing when there are no line items
			jQuery("#hdv_line_items").css("padding-top", "0");
			jQuery("#hdv_line_item_subtotal").hide();
		}
		hdv_recalculate_line_item_subtotal();
	}
	setTimeout(is_last_line_item, 300);
});

// when subtotal or amount paid is changed, recalculate total
jQuery("#content").on("change", "#hdv_invoice_publish_date", function() {
	jQuery("#hdv_invoice_publish_date, #date_padding").toggle();
	var publish_date = jQuery("#hdv_invoice_publish_date").val();
	jQuery("#hd_enable_date").html(hdv_convert_date(publish_date));
});

// when line item amount is changed, recalculate line item subtotal
jQuery("#content").on("change", ".hdv_line_item_value", function() {
	var data = jQuery(this).val();
	data = parseFloat(data).toFixed(2);
	jQuery(this).val(data);
	hdv_recalculate_line_item_subtotal();
});

function hdv_recalculate_line_item_subtotal() {
	var line_item_subtotal = 0;
	jQuery(".hdv_line_item_value").each(function() {
		var item = jQuery(this).val();
		if (item) {
			item = parseFloat(item);
		} else {
			item = 0;
		}
		line_item_subtotal = line_item_subtotal + item;
	});
	jQuery("#hdv_line_item_subtotal span").html(line_item_subtotal.toFixed(2));
}

// convert string to natural date (do not want to load moment.js just for this)
// https://stackoverflow.com/a/20438448
function hdv_convert_date(date_str) {
	var months = [
		"January",
		"February",
		"March",
		"April",
		"May",
		"June",
		"July",
		"August",
		"September",
		"October",
		"November",
		"December"
	];
	temp_date = date_str.split("-");
	return (
		months[Number(temp_date[1]) - 1] +
		" " +
		temp_date[2] +
		", " +
		temp_date[0]
	);
}

// Save Invoice Validation
jQuery("#content").on("click", "#save_invoice", function() {
	jQuery(this).fadeOut();
	var hdv_validated = true;
	jQuery(".hdv_required").each(function() {
		var data = jQuery(this).val();
		if (data == "" || data == null || data == "NaN") {
			jQuery(this).addClass("hd_error");
			hdv_validated = false;
		} else {
			jQuery(this).removeClass("hd_error");
		}
	});
	if (!hdv_validated) {
		jQuery("#content").animate(
			{
				scrollTop: 0
			},
			"slow"
		);
		jQuery(this).fadeIn();
	} else {
		// check if the invoice line items equals the subtotal
		// and show notice if it does not
		var invoice_subtotal = parseFloat(
			jQuery("#hdv_invoice_subtotal").val()
		).toFixed(2);
		var line_item_subtotal = parseFloat(
			jQuery("#hdv_line_item_subtotal span").html()
		).toFixed(2);
		if (line_item_subtotal > 0 && line_item_subtotal != invoice_subtotal) {
			var data =
				"The sum of your line items do not equal your invoice subtotal; you may have entered a wrong value.";
			hdv_show_model("Warning", data, "Continue Editing", "Save Anyways");
		} else {
			hdv_save_new_invoice();
		}
	}
});

jQuery("#content").on("click", "#continue-editing", function() {
	jQuery("#hdv_model")
		.removeClass("flipInY")
		.hide();
	jQuery("#save_invoice, #save_edit_invoice").fadeIn();
});

jQuery("#content").on(
	"click",
	"#save_new_invoice_wrapper #save-anyways",
	function() {
		jQuery("#hdv_model")
			.removeClass("flipInY")
			.hide();
		hdv_save_new_invoice();
	}
);

jQuery("#content").on(
	"click",
	"#edit_invoice_wrapper #save-anyways",
	function() {
		jQuery("#hdv_model")
			.removeClass("flipInY")
			.hide();
		hdv_save_current_invoice();
	}
);

// view invoice
jQuery("#content").on("click", ".hdv_edit_invoice", function() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	var hdv_invoice_id = jQuery(this).attr("data-id");
	var hdv_customer_id = jQuery(".customer_item,active").attr("data-id");
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_view_invoice",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_invoice_id: hdv_invoice_id,
			hdv_customer_id: hdv_customer_id
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("Editing Invoice");
			//init Visual Editors
			function init_visual_edutor() {
				jQuery(".hdv_visual_editor").trumbowyg({
					btns: [
						"strong",
						"em",
						"|",
						"unorderedList",
						"orderedList",
						"|",
						"link",
						"viewHTML"
					],
					autogrow: true,
					semantic: true,
					minimalLinks: true
				});
			}
			setTimeout(init_visual_edutor, 10);
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// save new invoice
function hdv_save_new_invoice() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	var hdv_customer_id = jQuery(
		"#sidebar_2 #hdv_customers_list .customer_item.active"
	).attr("data-id");
	var hdv_customer_name = jQuery(
		"#sidebar_2 #hdv_customers_list .customer_item.active"
	).attr("data-name");
	// get the invoice data
	var hdv_invoice_publish_date = jQuery("#hdv_invoice_publish_date").val();
	var hdv_tax_rate = parseFloat(jQuery("#tax_p").html());
	var hdv_invoice_subtotal = jQuery("#hdv_invoice_subtotal").val();
	var hdv_invoice_paid = jQuery("#hdv_invoice_paid").val();
	var hdv_invoice_total = jQuery("#hdv_invoice_total").val();
	var hdv_invoice_description = jQuery("#hdv_invoice_description").trumbowyg(
		"html"
	);
	var hdv_invoice_note = jQuery("#hdv_invoice_note").trumbowyg("html");
	// get line items and convert to JSON string
	var hdv_line_items = [];
	var hasLineItems = false;
	jQuery(".hdv_line_item").each(function() {
		var line_item_name = jQuery(this)
			.find(".hdv_line_item_name")
			.val();
		var line_item_value = jQuery(this)
			.find(".hdv_line_item_value")
			.val();
		hdv_line_items.push([line_item_name, line_item_value]);
		hasLineItems = true;
	});

	if (hasLineItems) {
		hdv_line_items = JSON.stringify(hdv_line_items);
	} else {
		hdv_line_items = "";
	}
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_save_new_invoice",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_customer_id: hdv_customer_id,
			hdv_customer_name: hdv_customer_name,
			hdv_invoice_publish_date: hdv_invoice_publish_date,
			hdv_tax_rate: hdv_tax_rate,
			hdv_invoice_subtotal: hdv_invoice_subtotal,
			hdv_invoice_paid: hdv_invoice_paid,
			hdv_invoice_total: hdv_invoice_total,
			hdv_invoice_description: hdv_invoice_description,
			hdv_invoice_note: hdv_invoice_note,
			hdv_line_items: hdv_line_items
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text(hdv_customer_name);
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
}

// Edit Invoice Validation
jQuery("#content").on("click", "#save_edit_invoice", function() {
	jQuery(this).fadeOut();
	var hdv_validated = true;
	jQuery(".hdv_required").each(function() {
		var data = jQuery(this).val();
		if (data == "" || data == null || data == "NaN") {
			jQuery(this).addClass("hd_error");
			hdv_validated = false;
		} else {
			jQuery(this).removeClass("hd_error");
		}
	});
	if (!hdv_validated) {
		jQuery("#content").animate(
			{
				scrollTop: 0
			},
			"slow"
		);
		jQuery(this).fadeIn();
	} else {
		// check if the invoice line items equals the subtotal
		// and show notice if it does not
		var invoice_subtotal = parseFloat(
			jQuery("#hdv_invoice_subtotal").val()
		).toFixed(2);
		var line_item_subtotal = parseFloat(
			jQuery("#hdv_line_item_subtotal span").html()
		).toFixed(2);
		if (line_item_subtotal > 0 && line_item_subtotal != invoice_subtotal) {
			var data =
				"The sum of your line items do not equal your invoice subtotal; you may have entered a wrong value.";
			hdv_show_model("Warning", data, "Continue Editing", "Save Anyways");
		} else {
			hdv_save_current_invoice();
		}
	}
});

jQuery("#content").on("click", "#void_invoice", function() {
	var data =
		"You are about to mark this invoice as void. This does not delete the invoice, but instead marks it as unpayable and cancelled.</p><p>Use this if you created this invoice in error, or if the invoice will no longer be paid";
	hdv_show_model("Warning", data, "NEVERMIND", "VOID INVOICE");
});

jQuery("#content").on("click", "#unvoid_invoice", function() {
	var data =
		"You are about to unvoid this invoice. This means that this invoice will become visible again and payments will be allowed to be made.";
	hdv_show_model("Warning", data, "NEVERMIND", "ENABLE INVOICE");
});

jQuery("#content").on("click", "#nevermind", function() {
	jQuery("#hdv_model")
		.removeClass("flipInY")
		.hide();
});

jQuery("#content").on("click", "#void-invoice", function() {
	jQuery("#hdv_model")
		.removeClass("flipInY")
		.hide();
	hdv_save_current_invoice("void");
});

jQuery("#content").on("click", "#enable-invoice", function() {
	jQuery("#hdv_model")
		.removeClass("flipInY")
		.hide();
	hdv_save_current_invoice("unvoid");
});

// save new invoice
function hdv_save_current_invoice(hdv_invoice_void = 0) {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	var invoice_id = hdv_invoice_id;
	var hdv_customer_id = jQuery(
		"#sidebar_2 #hdv_customers_list .customer_item.active"
	).attr("data-id");
	var hdv_customer_name = jQuery(
		"#sidebar_2 #hdv_customers_list .customer_item.active"
	).attr("data-name");
	// get the invoice data
	var hdv_invoice_publish_date = jQuery("#hdv_invoice_publish_date").val();
	var hdv_tax_rate = parseFloat(jQuery("#tax_p").html());
	var hdv_invoice_subtotal = jQuery("#hdv_invoice_subtotal").val();
	var hdv_invoice_paid = jQuery("#hdv_invoice_paid").val();
	var hdv_invoice_total = jQuery("#hdv_invoice_total").val();
	var hdv_invoice_description = jQuery("#hdv_invoice_description").trumbowyg(
		"html"
	);
	var hdv_invoice_note = jQuery("#hdv_invoice_note").trumbowyg("html");
	// get line items and convert to JSON string
	var hdv_line_items = [];
	var hasLineItems = false;
	jQuery(".hdv_line_item").each(function() {
		var line_item_name = jQuery(this)
			.find(".hdv_line_item_name")
			.val();
		var line_item_value = jQuery(this)
			.find(".hdv_line_item_value")
			.val();
		hdv_line_items.push([line_item_name, line_item_value]);
		hasLineItems = true;
	});

	if (hasLineItems) {
		hdv_line_items = JSON.stringify(hdv_line_items);
	} else {
		hdv_line_items = "";
	}
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_save_current_invoice",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_invoice_id: invoice_id,
			hdv_customer_id: hdv_customer_id,
			hdv_customer_name: hdv_customer_name,
			hdv_invoice_publish_date: hdv_invoice_publish_date,
			hdv_tax_rate: hdv_tax_rate,
			hdv_invoice_subtotal: hdv_invoice_subtotal,
			hdv_invoice_paid: hdv_invoice_paid,
			hdv_invoice_total: hdv_invoice_total,
			hdv_invoice_description: hdv_invoice_description,
			hdv_invoice_note: hdv_invoice_note,
			hdv_line_items: hdv_line_items,
			hdv_invoice_void: hdv_invoice_void
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text(hdv_customer_name);
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
}

// view tool
jQuery("#sidebar_2 #hdv_tools_list").on("click", ".customer_item", function() {
	jQuery("#hdv_loading").fadeIn();
	jQuery("#sidebar_1 ul li.active").removeClass("active");
	jQuery("#hdv_nav_tools").addClass("active");
	var ajaxUrl = hdv_ajax;
	var hdv_tool_id = jQuery(this).attr("data-id");
	var hdv_tool_name = jQuery(this).html();
	jQuery(".customer_item.active").removeClass("active");
	jQuery(this).addClass("active");
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_view_tool",
			hdv_dashboard_nonce: jQuery("#hdv_dashboard_nonce").val(),
			hdv_tool_id: hdv_tool_id
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			console.log(hdv_tool_name);
			if (hdv_tool_name.toLocaleLowerCase().indexOf("href") != -1) {
				jQuery("h1").text("");
			} else {
				jQuery("h1").text(hdv_tool_name);
			}
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});

// for when help is selected
// load the help page
jQuery("#hdv_nav_help").click(function() {
	jQuery("#hdv_loading").fadeIn();
	var ajaxUrl = hdv_ajax;
	jQuery.ajax({
		type: "POST",
		data: {
			action: "hdv_view_help"
		},
		url: ajaxUrl,
		success: function(data) {
			jQuery("#content").html(data);
			jQuery("h1").text("HDInvoice Help");
		},
		error: function() {
			console.log("Permission denied");
		},
		complete: function() {
			// complete
			jQuery("#hdv_loading").fadeOut();
			jQuery("#content").animate(
				{
					scrollTop: 0
				},
				"slow"
			);
		}
	});
});
