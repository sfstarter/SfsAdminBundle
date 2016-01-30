// On load, hide the aside menu if on small page width
$(document).ready(function() {
	var width = $( window ).width();

	if(width <= 750) {
		$("#menu-toggle").click();
	}
});

// Aside menu toggle to the left
$("#menu-toggle").click(function(e) {
	e.preventDefault();
	$("#wrapper, #topbar-title-site").toggleClass("menu-toggled");
});

// Activate bootstrap tooltips
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
});

// Batch checkbox
$(".batch-checkbox").click(function(e) {
	element = $(e.target);
	value = element.is(":checked");

	element.closest('table').find('.batch-row-checkbox').prop( "checked", value );
});

// Wysiwyg editors
$('.wysihtml5').wysihtml5();

// Colorpickers
$('.colorpicker-default').colorpicker({
	customClass: 'colorpicker-2x',
	sliders: {
		saturation: {
			maxLeft: 200,
			maxTop: 200
		},
		hue: {
			maxTop: 200
		},
		alpha: {
			maxTop: 200
		}
	}
});

// Select2
$('.selectpicker').select2({
	minimumResultsForSearch: 6,
	tags: true,
	tokenSeparators: [',', ' ']
});
// Multiselect list
$('.select-list').multiSelect({ selectableOptgroup: true });

// Bootstrap switch
$(".switch").bootstrapSwitch();

// Slider
$(".slider").ionRangeSlider({
	prettify_separator: '.'
});

// Datetimepicker
$('.datetimepicker').datetimepicker({});

//Charts
Chart.defaults.global.responsive = true;
