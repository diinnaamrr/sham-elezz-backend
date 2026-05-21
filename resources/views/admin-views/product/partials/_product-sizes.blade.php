@php
    $sizeRows = $sizeRows ?? [];
    $hasSizesChecked = $hasSizesChecked ?? false;
@endphp
<div class="col-lg-12" id="product_sizes_section">
    <div class="card shadow--card-2 border-0">
        <div class="card-header flex-wrap">
            <h5 class="card-title mb-0">
                <span class="card-header-icon mr-2"><i class="tio-layers-outlined"></i></span>
                <span>الأحجام / {{ translate('messages.price') }}</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label class="form-check form--check">
                    <input type="checkbox" class="form-check-input" name="has_sizes" id="has_sizes" value="1"
                        {{ $hasSizesChecked ? 'checked' : '' }}>
                    <span class="form-check-label">المنتج له أحجام (Sizes)</span>
                </label>
            </div>
            <div id="size_options_wrapper" class="{{ $hasSizesChecked ? '' : 'd-none' }}">
                <div class="table-responsive">
                    <table class="table table-borderless table-thead-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>الحجم</th>
                                <th>{{ translate('messages.price') }}</th>
                                <th class="text-center" style="width: 80px;"></th>
                            </tr>
                        </thead>
                        <tbody id="size_options_rows">
                            @forelse($sizeRows as $idx => $row)
                                <tr class="size-option-row">
                                    <td>
                                        <input type="text" class="form-control" name="size_options[{{ $idx }}][label]"
                                            value="{{ $row['label'] ?? '' }}" placeholder="{{ translate('messages.Ex:') }} Small" required>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="size_options[{{ $idx }}][price]"
                                            value="{{ $row['price'] ?? 0 }}" min="0" step="0.01" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-size-row" title="{{ translate('messages.Delete') }}">
                                            <i class="tio-delete-outlined"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="size-option-row">
                                    <td>
                                        <input type="text" class="form-control" name="size_options[0][label]"
                                            placeholder="{{ translate('messages.Ex:') }} Small">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="size_options[0][price]"
                                            value="0" min="0" step="0.01">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm remove-size-row" title="{{ translate('messages.Delete') }}">
                                            <i class="tio-delete-outlined"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn--primary btn-sm" id="add_size_row">
                    <i class="tio-add"></i> {{ translate('messages.add_new_option') }}
                </button>
                <small class="d-block text-muted mt-2">
                    سعر المنتج الأساسي في النظام = أقل سعر بين الأحجام (للتوافق مع التطبيق).
                </small>
            </div>
        </div>
    </div>
</div>
