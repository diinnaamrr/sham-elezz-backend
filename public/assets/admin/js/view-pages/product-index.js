"use strict";
let element = "";
let count = 0;
let countRow = 0;
let module_id = "";
let parent_category_id = 0;
let module_data = null;
let stock = true;
let module_type = "";

function show_min_max(data) {
    $('#min_max1_' + data).removeAttr("readonly");
    $('#min_max2_' + data).removeAttr("readonly");
    $('#min_max1_' + data).attr("required", "true");
    $('#min_max2_' + data).attr("required", "true");
}

function hide_min_max(data) {
    $('#min_max1_' + data).val(null).trigger('change');
    $('#min_max2_' + data).val(null).trigger('change');
    $('#min_max1_' + data).attr("readonly", "true");
    $('#min_max2_' + data).attr("readonly", "true");
    $('#min_max1_' + data).attr("required", "false");
    $('#min_max2_' + data).attr("required", "false");
}

$(document).on('change', '.show_min_max', function () {
    let data = $(this).data('count');
    show_min_max(data);
});

$(document).on('change', '.hide_min_max', function () {
    let data = $(this).data('count');
    hide_min_max(data);
});

function new_option_name(value, data) {
    $("#new_option_name_" + data).empty();
    $("#new_option_name_" + data).text(value)
    console.log(value);
}

function removeOption(e) {
    element = $(e);
    element.parents('.view_new_option').remove();
}

$(document).on('click', '.delete_input_button', function () {
    let e = $(this);
    removeOption(e);
});

function deleteRow(e) {
    element = $(e);
    element.parents('.add_new_view_row_class').remove();
}

$(document).on('click', '.deleteRow', function () {
    let e = $(this);
    deleteRow(e);
});
$(document).on('click', '.add_new_row_button', function () {
    let data = $(this).data('count');
    add_new_row_button(data);
});

$(document).on('keyup', '.new_option_name', function () {
    let data = $(this).data('count');
    let value = $(this).val();
    new_option_name(value, data);
});

$('.foodModalClose').on('click',function (){
    $('#food-modal').hide();
})

$('.foodModalShow').on('click',function (){
    $('#food-modal').show();
})

$('.attributeModalClose').on('click',function (){
    $('#attribute-modal').hide();
})

$('.attributeModalShow').on('click',function (){
    $('#attribute-modal').show();
})

$('#store_id').on('change', function () {
    let route = '{{url('/')}}/admin/store/get-addons?data[]=0&store_id='+$(this).val();
    let id = 'add_on';
    getRestaurantData(route, id);
});

function getRestaurantData(route, id) {
    $.get({
        url: route + id,
        dataType: 'json',
        success: function(data) {
            $('#' + id).empty().append(data.options);
        },
    });
}

function getRequest(route, id) {
    $.get({
        url: route,
        dataType: 'json',
        success: function(data) {
            $('#' + id).empty().append(data.options);
        },
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function(e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function() {
    readURL(this);
});

$('#category_id').on('change', function () {
    parent_category_id = $(this).val();
    console.log(parent_category_id);
});
$(document).on('change', '.combination_update', function () {
    combination_update();
});

function reindexSizeOptionRows() {
    $('#size_options_rows .size-option-row').each(function (index) {
        $(this).find('input[name^="size_options"]').each(function () {
            const field = $(this).attr('name').includes('[label]') ? 'label' : 'price';
            $(this).attr('name', 'size_options[' + index + '][' + field + ']');
        });
    });
}

function setFormSectionEnabled(sectionSelector, enabled) {
    const $section = $(sectionSelector);
    if (!$section.length) {
        return;
    }
    $section.find('input, select, textarea').each(function () {
        const $el = $(this);
        if (!enabled) {
            if ($el.prop('required')) {
                $el.attr('data-was-required', '1');
                $el.prop('required', false);
            }
            $el.prop('disabled', true);
        } else {
            if ($el.attr('data-was-required')) {
                $el.prop('required', true);
                $el.removeAttr('data-was-required');
            }
            $el.prop('disabled', false);
        }
    });
}

function toggleSizeOptionsPanel() {
    if ($('#has_sizes').is(':checked')) {
        $('#size_options_wrapper').removeClass('d-none');
        $('#food_variation_section').addClass('d-none');
        setFormSectionEnabled('#food_variation_section', false);
        setFormSectionEnabled('#size_options_wrapper', true);
    } else {
        $('#size_options_wrapper').addClass('d-none');
        $('#food_variation_section').removeClass('d-none');
        setFormSectionEnabled('#food_variation_section', true);
        setFormSectionEnabled('#size_options_wrapper', false);
    }
}

$(document).on('change', '#has_sizes', function () {
    toggleSizeOptionsPanel();
});

$(document).on('click', '#add_size_row', function () {
    const index = $('#size_options_rows .size-option-row').length;
    const row = `
        <tr class="size-option-row">
            <td><input type="text" class="form-control" name="size_options[${index}][label]" placeholder="Small" required></td>
            <td><input type="number" class="form-control" name="size_options[${index}][price]" value="0" min="0" step="0.01" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-size-row"><i class="tio-delete-outlined"></i></button>
            </td>
        </tr>`;
    $('#size_options_rows').append(row);
});

$(document).on('click', '.remove-size-row', function () {
    if ($('#size_options_rows .size-option-row').length <= 1) {
        $(this).closest('tr').find('input').val('');
        return;
    }
    $(this).closest('tr').remove();
    reindexSizeOptionRows();
});

$(document).ready(function () {
    if ($('#has_sizes').length) {
        toggleSizeOptionsPanel();
    }
});
