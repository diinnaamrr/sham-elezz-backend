"use strict";

let element = "";
let count =   $('.count_div').length;
let countRow = 0;
let mod_type="";
let removedImageKeys = [];

$(document).on('click','.function_remove_img' ,function(){
let key = $(this).data('key');
 let photo = $(this).data('photo');
 function_remove_img(key,photo);
});

function function_remove_img(key,photo) {
$('#product_images_' + key).addClass('d-none');
removedImageKeys.push(photo);
$('#removedImageKeysInput').val(removedImageKeys.join(','));
}

$(document).ready(function() {
    $('#organic').hide();
    if (mod_type == 'food') {
        $('#food_variation_section').show();
        $('#attribute_section').hide();
    } else {
        $('#food_variation_section').hide();
        $('#attribute_section').show();
    }
    if (mod_type == 'grocery') {
        $('#organic').show();
    }

    // INITIALIZATION OF SELECT2
    // =======================================================
    $('.js-select2-custom').each(function () {
        let select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});

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

$(document).on('change', '.get-request', function () {
    let val= $(this).val();
    let route= $(this).data('url')+val;
    let id= $(this).data('id');
    // For regular get-request items (not dynamic categories), use the default logic
    if (!$(this).hasClass('dynamic-category-select') && $(this).attr('id') !== 'category_id') {
        getRequest(route, id);
    }
})

$(document).on('change', '#category_id', function () {
    let parent_category_id = $(this).val();
    fetchDynamicCategories(parent_category_id, 0);
});

$(document).on('change', '.dynamic-category-select', function() {
    let parent_id = $(this).val();
    let depth = $(this).data('depth');
    fetchDynamicCategories(parent_id, depth);
});

function fetchDynamicCategories(parent_id, depth) {
    // Remove any deeper category selects
    $('.dynamic-category-wrapper').each(function() {
        if ($(this).data('depth') > depth) {
            $(this).remove();
        }
    });

    if (parent_id) {
        $.get({
            url: window.location.origin + '/store-panel/item/get-categories?parent_id=' + parent_id + '&sub_category=true',
            success: function(data) {
                // If the response is HTML string representing options
                if (data && data.options && data.options.trim() !== '<option value="" disabled selected>---Select---</option>') {
                    let newDepth = depth + 1;
                    let html = `<div class="col-sm-6 col-lg-4 dynamic-category-wrapper" data-depth="${newDepth}">
                        <div class="form-group mb-0">
                            <label class="input-label">Sub Category</label>
                            <select name="sub_category_ids[]" class="form-control js-select2-custom dynamic-category-select" data-depth="${newDepth}">
                                ${data.options}
                            </select></div></div>`;
                    
                    $('#dynamic-category-container').append(html);
                    // Reinitialize select2 for the new elements
                    $('.js-select2-custom').each(function () {
                        let select2 = $.HSCore.components.HSSelect2.init($(this));
                    });
                }
            }
        });
    }
}

function getRequest(route, id) {
    $.get({
        url: route,
        dataType: 'json',
        success: function (data) {
            $('#' + id).empty().append(data.options);
        },
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function () {
    readURL(this);
    $('#image-viewer-section').show(1000)
});

$('#choice_attributes').on('change', function () {
    $('#customer_choice_options').html(null);
    $.each($("#choice_attributes option:selected"), function () {
        add_more_customer_choice_option($(this).val(), $(this).text());
    });
});

$(document).on('change', '.combination_update', function () {
    combination_update();
});

setTimeout(function () {
    $('.call-update-sku').on('change', function () {
        combination_update();
    });
}, 2000)

$('#colors-selector').on('change', function () {
    combination_update();
});

$('input[name="unit_price"]').on('keyup', function () {
    combination_update();
});
