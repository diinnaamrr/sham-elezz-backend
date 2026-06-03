"use strict";

function refreshSelect2($element) {
    if (!$element.length) {
        return;
    }

    if ($element.hasClass('select2-hidden-accessible') && typeof $element.select2 === 'function') {
        $element.select2('destroy');
    }

    if (typeof $.HSCore !== 'undefined' && $.HSCore.components && $.HSCore.components.HSSelect2) {
        $.HSCore.components.HSSelect2.init($element);
    }
}

function formatParentSubOption(state) {
    if (!state.id) {
        return state.text;
    }

    const breadcrumb = $(state.element).data('breadcrumb');

    if (!breadcrumb) {
        return state.text;
    }

    return (
        '<div class="parent-sub-option">' +
            '<div class="font-weight-bold">' + state.text + '</div>' +
            '<small class="text-muted d-block">' + breadcrumb + '</small>' +
        '</div>'
    );
}

function refreshParentSubSelect2($element) {
    if (!$element.length || typeof $element.select2 !== 'function') {
        refreshSelect2($element);
        return;
    }

    if ($element.hasClass('select2-hidden-accessible')) {
        $element.select2('destroy');
    }

    $element.select2({
        placeholder: $element.data('direct-label') || '',
        allowClear: true,
        width: '100%',
        templateResult: formatParentSubOption,
        templateSelection: function (state) {
            if (!state.id) {
                return state.text;
            }

            const breadcrumb = $(state.element).data('breadcrumb');
            return breadcrumb || state.text;
        },
        escapeMarkup: function (markup) {
            return markup;
        },
    });
}

function populateParentSubOptions(mainCategoryId, selectedParentSubId) {
    const $parentSub = $('#parent_sub_category_id');

    if (!$parentSub.length) {
        return;
    }

    const map = window.subCategoriesByMain || {};
    const key = String(mainCategoryId);
    const options = map[key] || map[mainCategoryId] || [];

    const directLabel = $parentSub.data('direct-label')
        || $parentSub.find('option:first').text()
        || 'Direct under main category';

    $parentSub.empty();
    $parentSub.append($('<option>', { value: '', text: directLabel }));

    options.forEach(function (option) {
        const displayName = option.label || option.name.replace(/^—+\s*/g, '').trim();

        $parentSub.append(
            $('<option>', {
                value: option.id,
                text: displayName,
                title: option.breadcrumb || displayName,
            }).attr('data-breadcrumb', option.breadcrumb || displayName)
        );
    });

    if (mainCategoryId) {
        $parentSub.prop('disabled', false);
        $parentSub.val(selectedParentSubId ? String(selectedParentSubId) : '');
    } else {
        $parentSub.prop('disabled', true);
        $parentSub.val('');
    }

    refreshParentSubSelect2($parentSub);
}

function initSubCategoryParentPicker() {
    const $mainCategory = $('#main_category_id');
    const $parentSub = $('#parent_sub_category_id');

    if (!$mainCategory.length || !$parentSub.length) {
        return;
    }

    $parentSub.data(
        'direct-label',
        $parentSub.find('option:first').text() || 'Direct under main category'
    );

    $mainCategory.on('change', function () {
        populateParentSubOptions($(this).val(), '');
    });

    const defaults = window.subCategoryFormDefaults || {};
    const currentMain = $mainCategory.val() || defaults.main_category_id;

    if (currentMain) {
        populateParentSubOptions(currentMain, defaults.parent_sub_category_id || '');
    } else {
        $parentSub.prop('disabled', true);
        refreshParentSubSelect2($parentSub);
    }
}

$(document).on('ready', function () {
    $('.js-nav-scroller').each(function () {
        if (typeof HsNavScroller !== 'undefined') {
            new HsNavScroller($(this)).init();
        }
    });

    $('#main_category_id').each(function () {
        refreshSelect2($(this));
    });

    const $datatable = $('#datatable').length ? $('#datatable') : $('#columnSearchDatatable');

    if ($datatable.length && typeof $.HSCore !== 'undefined' && $.HSCore.components && $.HSCore.components.HSDatatables) {
        try {
            var datatable = $.HSCore.components.HSDatatables.init($datatable, {
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'copy', className: 'd-none' },
                    { extend: 'print', className: 'd-none' },
                ],
            });

            $('#export-excel').on('click', function () {
                if (datatable) {
                    datatable.button('.buttons-excel').trigger();
                }
            });
            $('#export-csv').on('click', function () {
                if (datatable) {
                    datatable.button('.buttons-csv').trigger();
                }
            });
        } catch (e) {
            console.warn('Datatable init skipped:', e);
        }
    }

    initSubCategoryParentPicker();
});

document.querySelectorAll('.priority-form').forEach(function (form) {
    var select = form.querySelector('.priority-select');
    if (select) {
        select.addEventListener('change', function () {
            form.submit();
        });
    }
});
