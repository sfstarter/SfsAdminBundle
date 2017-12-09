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
	$("#wrapper").toggleClass("menu-toggled");
});

// Activate bootstrap tooltips
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
});

// Ajax on modal bootstrap
$('[data-toggle="ajax-modal"]').on('click', function (e) {
    e.preventDefault();

    var url = $(this).data('ajax-url');
    var title = $(this).data('title');
    var modalType = $(this).data('modal-type');

    $.ajax({
		url: url,
	}).success(function(data) {
		// Modal size
        $('#modal-ajax .modal-dialog').removeClass('modal-lg');
        $('#modal-ajax .modal-dialog').removeClass('modal-sm');
		if(modalType)
        	$('#modal-ajax .modal-dialog').addClass(modalType);

		$('#modal-ajax .modal-title').html(title);
		$('#modal-ajax .modal-body').html(data);
        $('#modal-ajax').modal({show: true});
	});
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
