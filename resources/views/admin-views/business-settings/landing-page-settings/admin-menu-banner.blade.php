@extends('layouts.admin.app')

@section('title', translate('messages.menu_banner'))

@section('content')
<div class="content container-fluid">
    <div class="page-header pb-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/banner.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{ translate('messages.admin_landing_pages') }}
                </span>
            </h1>
            <div>
                <a href="{{ route('menu.index') }}" target="_blank" class="btn btn-sm btn--secondary">
                    <i class="tio-globe"></i> {{ translate('messages.view_menu_page') }}
                </a>
            </div>
        </div>
    </div>

    <div class="mb-4 mt-2">
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.admin-landing-page-links')
        </div>
    </div>

    @php
        $current_banner_url = null;
        if (isset($menu_banner) && $menu_banner->value) {
            $current_banner_url = \App\CentralLogics\Helpers::get_full_url('banner', $menu_banner->value, $menu_banner->storage[0]->value ?? 'public');
        } else {
            $current_banner_url = 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80';
        }
    @endphp

    <div class="card my-2">
        <div class="card-header">
            <h5 class="card-title d-flex align-items-center">
                <i class="tio-image mr-2 text--primary"></i>
                <span>{{ translate('messages.menu_page_banner') }}</span>
            </h5>
            <small class="text-muted">{{ translate('messages.recommended_size') }}: 1920x450 px (4:1 Ratio)</small>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.menu.banner.update') }}"
                  method="POST" enctype="multipart/form-data" id="menu-banner-form">
                @csrf

                <div class="row g-3 justify-content-center">
                    <div class="col-md-10">
                        <label class="form-label font-weight-bold d-block text-center mb-3">
                            {{ translate('messages.upload_menu_banner_image') }}
                        </label>

                        <div class="text-center mb-3">
                            <div class="position-relative d-inline-block w-100" style="max-width: 800px; border-radius: 12px; overflow: hidden; border: 2px dashed #e7eaf3; background: #f8fafc; padding: 10px;">
                                <img id="banner-preview"
                                     src="{{ $current_banner_url }}"
                                     data-onerror-image="{{ asset('public/assets/admin/img/upload-4.png') }}"
                                     class="img-fluid w-100"
                                     style="max-height: 280px; object-fit: cover; border-radius: 8px;"
                                     alt="Menu Banner">
                                <label for="menu_banner_input" class="btn btn-sm btn--primary position-absolute" style="bottom: 20px; right: 20px; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                                    <i class="tio-camera"></i> {{ translate('messages.choose_image') }}
                                </label>
                                <input type="file" name="menu_banner" id="menu_banner_input" accept="image/*" class="d-none" onchange="previewBannerImage(this)">
                            </div>
                        </div>

                        <div class="alert alert-soft-info text-center mt-3" role="alert">
                            <i class="tio-info mr-1"></i>
                            {{ translate('messages.menu_banner_note') ?? 'هذا البانر سيتم عرضه في أعلى صفحة المنيو (/menu) بدون أي نصوص فوقه.' }}
                        </div>
                    </div>
                </div>

                <div class="btn--container justify-content-end mt-4">
                    @if(isset($menu_banner) && $menu_banner->value)
                        <button type="submit" name="reset_banner" value="1" class="btn btn--reset mr-2" onclick="return confirm('{{ translate('messages.are_you_sure_to_reset_banner') }}')">
                            <i class="tio-refresh"></i> {{ translate('messages.reset_to_default') }}
                        </button>
                    @endif
                    <button type="submit" class="btn btn--primary">
                        <i class="tio-save"></i> {{ translate('messages.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewBannerImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('banner-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
