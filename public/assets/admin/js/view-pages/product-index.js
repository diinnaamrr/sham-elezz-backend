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
    fetchDynamicCategories(parent_category_id, 0);
});

$(document).on('change', '.dynamic-category-select', function() {
    let parent_id = $(this).val();
    let depth = $(this).data('depth');
    fetchDynamicCategories(parent_id, depth);
});

function appendDynamicCategory(html) {
    let $html = $(html);
    if ($('.dynamic-category-wrapper').length) {
        $('.dynamic-category-wrapper').last().after($html);
    } else {
        $('#category_id').closest('[class*="col-"]').after($html);
    }
}

function fetchDynamicCategories(parent_id, depth) {
    // Remove any deeper category selects
    $('.dynamic-category-wrapper').each(function() {
        if ($(this).data('depth') > depth) {
            $(this).remove();
        }
    });

    if (parent_id) {
        $.get({
            url: window.location.origin + '/admin/item/get-categories?parent_id=' + parent_id + '&sub_category=true',
            success: function(data) {
                if (data && data.length > 0) {
                    let newDepth = depth + 1;
                    let html = `<div class="col-sm-6 col-lg-3 dynamic-category-wrapper" data-depth="${newDepth}">
                        <div class="form-group mb-0">
                            <label class="input-label">Sub Category</label>
                            <select name="sub_category_ids[]" class="form-control dynamic-category-select" data-depth="${newDepth}">
                                <option value="" disabled selected>---Select---</option>`;
                    data.forEach(item => {
                        html += `<option value="${item.id}">${item.text}</option>`;
                    });
                    html += `</select></div></div>`;

                    appendDynamicCategory(html);
                }
            }
        });
    }
}
$(document).on('change', '.combination_update', function () {
    combination_update();
});

function toggleZeroPriceVariationHint() {
    const price = parseFloat($('input[name="price"]').val());
    if (!isNaN(price) && price === 0) {
        $('#zero_price_variation_hint').removeClass('d-none');
        $('#food_variation_section').removeClass('d-none');
    } else {
        $('#zero_price_variation_hint').addClass('d-none');
    }
}

$(document).on('input change', 'input[name="price"]', toggleZeroPriceVariationHint);
$(document).ready(function () {
    toggleZeroPriceVariationHint();
});
