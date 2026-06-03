"use strict";

function populateParentSubOptions(mainCategoryId, selectedParentSubId) {
    const $parentSub = $('#parent_sub_category_id');
    const options = (window.subCategoriesByMain && window.subCategoriesByMain[mainCategoryId]) || [];

    $parentSub.empty();
    $parentSub.append(
        $('<option>', { value: '', text: $parentSub.data('direct-label') || 'Direct under main category' })
    );

    options.forEach(function (option) {
        $parentSub.append(
            $('<option>', {
                value: option.id,
                text: option.name,
                selected: String(selectedParentSubId) === String(option.id),
            })
        );
    });

    $parentSub.prop('disabled', !mainCategoryId).trigger('change');
}

$(document).on('ready', function () {
    $('.js-nav-scroller').each(function () {
        new HsNavScroller($(this)).init();
    });

    $('.js-select2-custom').each(function () {
        $.HSCore.components.HSSelect2.init($(this));
    });

    var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'd-none' },
            { extend: 'print', className: 'd-none' },
        ],
        select: {
            style: 'multi',
            selector: 'td:first-child input[type="checkbox"]',
            classMap: {
                checkAll: '#datatableCheckAll',
                counter: '#datatableCounter',
                counterInfo: '#datatableCounterInfo',
            },
        },
    });

    $('#export-copy').click(function () {
        datatable.button('.buttons-copy').trigger();
    });
    $('#export-excel').click(function () {
        datatable.button('.buttons-excel').trigger();
    });
    $('#export-csv').click(function () {
        datatable.button('.buttons-csv').trigger();
    });
    $('#export-pdf').click(function () {
        datatable.button('.buttons-pdf').trigger();
    });
    $('#export-print').click(function () {
        datatable.button('.buttons-print').trigger();
    });

    $('#datatableSearch').on('mouseup', function () {
        var $input = $(this),
            oldValue = $input.val();
        if (oldValue == "") return;
        setTimeout(function () {
            if ($input.val() == "") {
                datatable.search('').draw();
            }
        }, 1);
    });

    const $mainCategory = $('#main_category_id');
    const $parentSub = $('#parent_sub_category_id');

    if ($mainCategory.length && $parentSub.length) {
        $parentSub.data(
            'direct-label',
            $parentSub.find('option:first').text() || 'Direct under main category'
        );

        $mainCategory.on('change', function () {
            populateParentSubOptions($(this).val(), '');
        });

        const defaults = window.subCategoryFormDefaults || {};
        if (defaults.main_category_id) {
            $mainCategory.val(defaults.main_category_id).trigger('change');
            populateParentSubOptions(
                defaults.main_category_id,
                defaults.parent_sub_category_id || ''
            );
        }
    }
});

var forms = document.querySelectorAll('.priority-form');
forms.forEach(function (form) {
    var select = form.querySelector('.priority-select');
    if (select) {
        select.addEventListener('change', function () {
            form.submit();
        });
    }
});
