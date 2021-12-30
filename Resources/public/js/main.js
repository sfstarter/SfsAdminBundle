// On load, hide the aside menu if on small page width
$(document).ready(function() {
	var width = $( window ).width();

	if(width <= 750) {
		$("#menu-toggle").click();
	}

	// Aside menu toggle to the left
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("menu-toggled");
    });

    initializeElements();
});

$(document).ajaxComplete(function(event, xhr, settings) {
    constructBootstrapSwitch($('.switch:not(.has-switch)'));
});

function initializeElements() {
	constructToolTips($('[data-toggle="tooltip"]'));
    constructAjaxModal($('[data-toggle="ajax-modal"]'));
    constructColorPicker($('.colorpicker-default'));
    constructWysiwyg($('.wysihtml5'));
    constructBatchCheckbox($(".batch-checkbox"));
    constructSelect2($('.selectpicker'));
    constructMultiSelect($('.select-list'));
    constructIonRangeSlider($(".slider"));
    constructBootstrapSwitch($('.switch'));
    constructDateTimePicker($('.datetimepicker'));
}

//Charts
Chart.defaults.global.responsive = true;

// Activate bootstrap tooltips
function constructToolTips(elt) {
	elt.tooltip();
}

// Ajax on modal bootstrap
function constructAjaxModal(elt) {
    elt.on('click', function (e) {
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
}

// Batch checkbox
function constructBatchCheckbox(elt) {
    elt.click(function(e) {
        element = $(e.target);
        value = element.is(":checked");

        element.closest('table').find('.batch-row-checkbox').prop( "checked", value );
    });
}
// Wysiwyg editors
function constructWysiwyg(elt) {
    elt.wysihtml5();
}

// Colorpickers
function constructColorPicker(elt) {
    elt.colorpicker({
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
}

// Select2
function constructSelect2(elt) {
    var tags = (typeof(elt.attr('tags'))  === 'undefined') ? false : true;
    console.log(elt);
    console.log(tags);
    elt.select2({
        minimumResultsForSearch: 6,
        tags: true,
        tokenSeparators: [',', ' ']
    });
}

// Multiselect list
function constructMultiSelect(elt) {
    elt.multiSelect({ selectableOptgroup: true });
}

// Slider
function constructIonRangeSlider(elt) {
    elt.ionRangeSlider({
        prettify_separator: '.'
    });
}

// Datetimepicker
function constructDateTimePicker(elt) {
	elt.datetimepicker({});
}

// Bootstrap switch
function constructBootstrapSwitch(elt) {
	elt.bootstrapSwitch();
}
