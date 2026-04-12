@extends('layouts.admin.app')

@section('title',translate('messages.app_settings'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/admin/img/setting.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.app_settings')}}
                </span>
            </h1>
            {{-- <div class="text--primary-2 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#how-it-works">
                <strong class="mr-2">{{translate('See_how_it_works!')}}</strong>
                <div class="blinkings">
                    <i class="tio-info-outined"></i>
                </div>
            </div> --}}
        </div>
        <!-- End Page Header -->

        @php($app_minimum_version_android=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_android'])->first())
        @php($app_minimum_version_android=$app_minimum_version_android?$app_minimum_version_android->value:null)

        @php($app_url_android=\App\Models\BusinessSetting::where(['key'=>'app_url_android'])->first())
        @php($app_url_android=$app_url_android?$app_url_android->value:null)

        @php($app_minimum_version_ios=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_ios'])->first())
        @php($app_minimum_version_ios=$app_minimum_version_ios?$app_minimum_version_ios->value:null)

        @php($app_url_ios=\App\Models\BusinessSetting::where(['key'=>'app_url_ios'])->first())
        @php($app_url_ios=$app_url_ios?$app_url_ios->value:null)

        @php($app_minimum_version_android_store=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_android_store'])->first())
        @php($app_minimum_version_android_store=$app_minimum_version_android_store?$app_minimum_version_android_store->value:null)
        @php($app_url_android_store=\App\Models\BusinessSetting::where(['key'=>'app_url_android_store'])->first())
        @php($app_url_android_store=$app_url_android_store?$app_url_android_store->value:null)

        @php($app_minimum_version_ios_store=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_ios_store'])->first())
        @php($app_minimum_version_ios_store=$app_minimum_version_ios_store?$app_minimum_version_ios_store->value:null)
        @php($app_url_ios_store=\App\Models\BusinessSetting::where(['key'=>'app_url_ios_store'])->first())
        @php($app_url_ios_store=$app_url_ios_store?$app_url_ios_store->value:null)

        @php($app_minimum_version_android_deliveryman=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_android_deliveryman'])->first())
        @php($app_minimum_version_android_deliveryman=$app_minimum_version_android_deliveryman?$app_minimum_version_android_deliveryman->value:null)
        @php($app_url_android_deliveryman=\App\Models\BusinessSetting::where(['key'=>'app_url_android_deliveryman'])->first())
        @php($app_url_android_deliveryman=$app_url_android_deliveryman?$app_url_android_deliveryman->value:null)

        @php($app_minimum_version_ios_deliveryman=\App\Models\BusinessSetting::where(['key'=>'app_minimum_version_ios_deliveryman'])->first())
        @php($app_minimum_version_ios_deliveryman=$app_minimum_version_ios_deliveryman?$app_minimum_version_ios_deliveryman->value:null)
        @php($app_url_ios_deliveryman=\App\Models\BusinessSetting::where(['key'=>'app_url_ios_deliveryman'])->first())
        @php($app_url_ios_deliveryman=$app_url_ios_deliveryman?$app_url_ios_deliveryman->value:null)

        @php($user_app_maintenance_mode=\App\Models\BusinessSetting::where(['key'=>'user_app_maintenance_mode'])->first()?->value ?? 0)
        @php($user_app_maintenance_message=\App\Models\BusinessSetting::where(['key'=>'user_app_maintenance_message'])->first()?->value ?? '')
        @php($user_android_latest_version=\App\Models\BusinessSetting::where(['key'=>'user_android_latest_version'])->first())
        @php($user_android_latest_version=$user_android_latest_version?$user_android_latest_version->value:null)
        @php($user_ios_latest_version=\App\Models\BusinessSetting::where(['key'=>'user_ios_latest_version'])->first())
        @php($user_ios_latest_version=$user_ios_latest_version?$user_ios_latest_version->value:null)
        @php($user_android_force_update=\App\Models\BusinessSetting::where(['key'=>'user_android_force_update'])->first()?->value ?? 0)
        @php($user_ios_force_update=\App\Models\BusinessSetting::where(['key'=>'user_ios_force_update'])->first()?->value ?? 0)
        @php($user_android_blocked_versions=\App\Models\BusinessSetting::where(['key'=>'user_android_blocked_versions'])->first()?->value ?? '[]')
        @php($user_ios_blocked_versions=\App\Models\BusinessSetting::where(['key'=>'user_ios_blocked_versions'])->first()?->value ?? '[]')

        @php($store_app_maintenance_mode=\App\Models\BusinessSetting::where(['key'=>'store_app_maintenance_mode'])->first()?->value ?? 0)
        @php($store_app_maintenance_message=\App\Models\BusinessSetting::where(['key'=>'store_app_maintenance_message'])->first()?->value ?? '')
        @php($store_android_latest_version=\App\Models\BusinessSetting::where(['key'=>'store_android_latest_version'])->first())
        @php($store_android_latest_version=$store_android_latest_version?$store_android_latest_version->value:null)
        @php($store_ios_latest_version=\App\Models\BusinessSetting::where(['key'=>'store_ios_latest_version'])->first())
        @php($store_ios_latest_version=$store_ios_latest_version?$store_ios_latest_version->value:null)
        @php($store_android_force_update=\App\Models\BusinessSetting::where(['key'=>'store_android_force_update'])->first()?->value ?? 0)
        @php($store_ios_force_update=\App\Models\BusinessSetting::where(['key'=>'store_ios_force_update'])->first()?->value ?? 0)
        @php($store_android_blocked_versions=\App\Models\BusinessSetting::where(['key'=>'store_android_blocked_versions'])->first()?->value ?? '[]')
        @php($store_ios_blocked_versions=\App\Models\BusinessSetting::where(['key'=>'store_ios_blocked_versions'])->first()?->value ?? '[]')

        @php($deliveryman_app_maintenance_mode=\App\Models\BusinessSetting::where(['key'=>'deliveryman_app_maintenance_mode'])->first()?->value ?? 0)
        @php($deliveryman_app_maintenance_message=\App\Models\BusinessSetting::where(['key'=>'deliveryman_app_maintenance_message'])->first()?->value ?? '')
        @php($deliveryman_android_latest_version=\App\Models\BusinessSetting::where(['key'=>'deliveryman_android_latest_version'])->first())
        @php($deliveryman_android_latest_version=$deliveryman_android_latest_version?$deliveryman_android_latest_version->value:null)
        @php($deliveryman_ios_latest_version=\App\Models\BusinessSetting::where(['key'=>'deliveryman_ios_latest_version'])->first())
        @php($deliveryman_ios_latest_version=$deliveryman_ios_latest_version?$deliveryman_ios_latest_version->value:null)
        @php($deliveryman_android_force_update=\App\Models\BusinessSetting::where(['key'=>'deliveryman_android_force_update'])->first()?->value ?? 0)
        @php($deliveryman_ios_force_update=\App\Models\BusinessSetting::where(['key'=>'deliveryman_ios_force_update'])->first()?->value ?? 0)
        @php($deliveryman_android_blocked_versions=\App\Models\BusinessSetting::where(['key'=>'deliveryman_android_blocked_versions'])->first()?->value ?? '[]')
        @php($deliveryman_ios_blocked_versions=\App\Models\BusinessSetting::where(['key'=>'deliveryman_ios_blocked_versions'])->first()?->value ?? '[]')

        <form action="{{route('admin.business-settings.app-settings')}}" method="post"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="user_app" >
            <h5 class="card-title mb-3 pt-3">
                <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{ translate('User_App_Version_Control') }}</span>
            </h5>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <span class="card-header-icon mr-2"><i class="tio-build"></i></span> {{ translate('Maintenance_Mode') }}
                    </h5>
                    <div class="__bg-F8F9FC-card">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Maintenance_Status') }}</label>
                                    <select name="user_app_maintenance_mode" class="form-control">
                                        <option value="1" {{ $user_app_maintenance_mode == 1 ? 'selected' : '' }}>{{ translate('messages.true') }}</option>
                                        <option value="0" {{ $user_app_maintenance_mode == 0 ? 'selected' : '' }}>{{ translate('messages.false') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Maintenance_Message') }}</label>
                                    <input type="text" name="user_app_maintenance_message" class="form-control" value="{{ $user_app_maintenance_message }}" placeholder="{{ translate('App_is_under_maintenance') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">
                                <img src="{{asset('public/admin/img/andriod.png')}}" class="mr-2" alt="">
                                {{ translate('For android') }}
                            </h5>
                            <div class="__bg-F8F9FC-card">
                                <div class="form-group">
                                    <label  for="app_minimum_version_android" class="form-label">
                                        {{translate('Minimum_User_App_Version')}} ({{translate('messages.android')}})
                                    </label>
                                    <input id="app_minimum_version_android" type="text" placeholder="1.0.0" class="form-control" name="app_minimum_version_android" pattern="^\d\.\d\.\d$"
                                        value="{{env('APP_MODE')!='demo'?$app_minimum_version_android??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Latest_User_App_Version') }} ({{translate('messages.android')}})</label>
                                    <input type="text" name="user_android_latest_version" class="form-control" value="{{env('APP_MODE')!='demo'?$user_android_latest_version??'':''}}" placeholder="1.0.0" pattern="^\d\.\d\.\d$">
                                </div>
                                <div class="form-group">
                                    <label for="app_url_android" class="form-label text-capitalize">
                                        {{translate('Download_URL_for_User_App')}} ({{translate('messages.android')}})
                                    </label>
                                    <input id="app_url_android" type="text" placeholder="{{translate('messages.app_url')}}" class="form-control" name="app_url_android"
                                        value="{{env('APP_MODE')!='demo'?$app_url_android??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Force_Update') }}</label>
                                    <select name="user_android_force_update" class="form-control">
                                        <option value="1" {{ $user_android_force_update == 1 ? 'selected' : '' }}>{{ translate('messages.yes') }}</option>
                                        <option value="0" {{ $user_android_force_update == 0 ? 'selected' : '' }}>{{ translate('messages.no') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label text-capitalize">{{ translate('Blocked_Versions') }}</label>
                                    <textarea name="user_android_blocked_versions" class="form-control" placeholder="1.0.0, 1.0.1">{{ implode(',', json_decode($user_android_blocked_versions, true) ?? []) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">
                                <img src="{{asset('public/admin/img/ios.png')}}" class="mr-2" alt="">
                                {{ translate('For iOS') }}
                            </h5>
                            <div class="__bg-F8F9FC-card">
                                <div class="form-group">
                                    <label  for="app_minimum_version_ios" class="form-label">{{translate('Minimum_User_App_Version')}} ({{translate('messages.ios')}})</label>
                                    <input id="app_minimum_version_ios" type="text" placeholder="1.0.0" class="form-control" name="app_minimum_version_ios" pattern="^\d\.\d\.\d$"
                                        value="{{env('APP_MODE')!='demo'?$app_minimum_version_ios??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Latest_User_App_Version') }} ({{translate('messages.ios')}})</label>
                                    <input type="text" name="user_ios_latest_version" class="form-control" value="{{env('APP_MODE')!='demo'?$user_ios_latest_version??'':''}}" placeholder="1.0.0" pattern="^\d\.\d\.\d$">
                                </div>
                                <div class="form-group">
                                    <label for="app_url_ios" class="form-label text-capitalize">
                                        {{translate('Download_URL_for_User_App')}} ({{translate('messages.ios')}})
                                    </label>
                                    <input id="app_url_ios" type="text" placeholder="{{translate('messages.app_url')}}" class="form-control" name="app_url_ios"
                                        value="{{env('APP_MODE')!='demo'?$app_url_ios??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Force_Update') }}</label>
                                    <select name="user_ios_force_update" class="form-control">
                                        <option value="1" {{ $user_ios_force_update == 1 ? 'selected' : '' }}>{{ translate('messages.yes') }}</option>
                                        <option value="0" {{ $user_ios_force_update == 0 ? 'selected' : '' }}>{{ translate('messages.no') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label text-capitalize">{{ translate('Blocked_Versions') }}</label>
                                    <textarea name="user_ios_blocked_versions" class="form-control" placeholder="1.0.0, 1.0.1">{{ implode(',', json_decode($user_ios_blocked_versions, true) ?? []) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"  class="btn btn--primary mb-2 call-demo">{{translate('messages.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>
                    

        <form action="{{route('admin.business-settings.app-settings')}}" method="post"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="store_app" >
            <h5 class="card-title mb-3 pt-4">
                <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{ translate('Store_App_Version_Control') }}</span>
            </h5>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <span class="card-header-icon mr-2"><i class="tio-build"></i></span> {{ translate('Maintenance_Mode') }}
                    </h5>
                    <div class="__bg-F8F9FC-card">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Maintenance_Status') }}</label>
                                    <select name="store_app_maintenance_mode" class="form-control">
                                        <option value="1" {{ $store_app_maintenance_mode == 1 ? 'selected' : '' }}>{{ translate('messages.true') }}</option>
                                        <option value="0" {{ $store_app_maintenance_mode == 0 ? 'selected' : '' }}>{{ translate('messages.false') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Maintenance_Message') }}</label>
                                    <input type="text" name="store_app_maintenance_message" class="form-control" value="{{ $store_app_maintenance_message }}" placeholder="{{ translate('App_is_under_maintenance') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">
                                <img src="{{asset('public/admin/img/andriod.png')}}" class="mr-2" alt="">
                                {{ translate('For android') }}
                            </h5>
                            <div class="__bg-F8F9FC-card">
                                <div class="form-group">
                                    <label  for="app_minimum_version_android_store" class="form-label text-capitalize">{{translate('Minimum_Store_App_Version_for_store')}} ({{translate('messages.android')}})
                                    </label>
                                    <input id="app_minimum_version_android_store" type="text" placeholder="1.0.0" class="form-control h--45px" name="app_minimum_version_android_store" pattern="^\d\.\d\.\d$"
                                        value="{{env('APP_MODE')!='demo'?$app_minimum_version_android_store??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Latest_Store_App_Version') }} ({{translate('messages.android')}})</label>
                                    <input type="text" name="store_android_latest_version" class="form-control h--45px" value="{{env('APP_MODE')!='demo'?$store_android_latest_version??'':''}}" placeholder="1.0.0" pattern="^\d\.\d\.\d$">
                                </div>
                                <div class="form-group">
                                    <label for="app_url_android_store" class="form-label text-capitalize">
                                        {{translate('Download_URL_for_Store_App')}} ({{translate('messages.android')}})
                                    </label>
                                    <input id="app_url_android_store" type="text" placeholder="{{translate('messages.download_url')}}" class="form-control h--45px" name="app_url_android_store"
                                        value="{{env('APP_MODE')!='demo'?$app_url_android_store??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Force_Update') }}</label>
                                    <select name="store_android_force_update" class="form-control h--45px">
                                        <option value="1" {{ $store_android_force_update == 1 ? 'selected' : '' }}>{{ translate('messages.yes') }}</option>
                                        <option value="0" {{ $store_android_force_update == 0 ? 'selected' : '' }}>{{ translate('messages.no') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label text-capitalize">{{ translate('Blocked_Versions') }}</label>
                                    <textarea name="store_android_blocked_versions" class="form-control" placeholder="1.0.0, 1.0.1">{{ implode(',', json_decode($store_android_blocked_versions, true) ?? []) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">
                                <img src="{{asset('public/admin/img/ios.png')}}" class="mr-2" alt="">
                                {{ translate('For iOS') }}
                            </h5>
                            <div class="__bg-F8F9FC-card">
                                <div class="form-group">
                                    <label for="app_minimum_version_ios_store" class="form-label text-capitalize">{{translate('Minimum_Store_App_Version')}} ({{translate('messages.ios')}})
                                    </label>
                                    <input id="app_minimum_version_ios_store" type="text" placeholder="1.0.0" class="form-control h--45px" name="app_minimum_version_ios_store" pattern="^\d\.\d\.\d$"
                                    value="{{env('APP_MODE')!='demo'?$app_minimum_version_ios_store??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Latest_Store_App_Version') }} ({{translate('messages.ios')}})</label>
                                    <input type="text" name="store_ios_latest_version" class="form-control h--45px" value="{{env('APP_MODE')!='demo'?$store_ios_latest_version??'':''}}" placeholder="1.0.0" pattern="^\d\.\d\.\d$">
                                </div>
                                <div class="form-group mb-md-0">
                                    <label for="app_url_ios_store" class="form-label text-capitalize">
                                        {{translate('Download_URL_for_Store_App')}} ({{translate('messages.ios')}})
                                    </label>
                                    <input id="app_url_ios_store" type="text" placeholder="{{translate('messages.download_url')}}" class="form-control h--45px" name="app_url_ios_store"
                                    value="{{env('APP_MODE')!='demo'?$app_url_ios_store??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Force_Update') }}</label>
                                    <select name="store_ios_force_update" class="form-control h--45px">
                                        <option value="1" {{ $store_ios_force_update == 1 ? 'selected' : '' }}>{{ translate('messages.yes') }}</option>
                                        <option value="0" {{ $store_ios_force_update == 0 ? 'selected' : '' }}>{{ translate('messages.no') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label text-capitalize">{{ translate('Blocked_Versions') }}</label>
                                    <textarea name="store_ios_blocked_versions" class="form-control" placeholder="1.0.0, 1.0.1">{{ implode(',', json_decode($store_ios_blocked_versions, true) ?? []) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"  class="btn btn--primary mb-2 call-demo"  >{{translate('messages.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>


        <form action="{{route('admin.business-settings.app-settings')}}" method="post"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="type" value="deliveryman_app" >
            <h5 class="card-title mb-3 pt-4">
                <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{ translate('Deliveryman_App_Version_Control') }}</span>
            </h5>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <span class="card-header-icon mr-2"><i class="tio-build"></i></span> {{ translate('Maintenance_Mode') }}
                    </h5>
                    <div class="__bg-F8F9FC-card">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Maintenance_Status') }}</label>
                                    <select name="deliveryman_app_maintenance_mode" class="form-control">
                                        <option value="1" {{ $deliveryman_app_maintenance_mode == 1 ? 'selected' : '' }}>{{ translate('messages.true') }}</option>
                                        <option value="0" {{ $deliveryman_app_maintenance_mode == 0 ? 'selected' : '' }}>{{ translate('messages.false') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Maintenance_Message') }}</label>
                                    <input type="text" name="deliveryman_app_maintenance_message" class="form-control" value="{{ $deliveryman_app_maintenance_message }}" placeholder="{{ translate('App_is_under_maintenance') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">
                                <img src="{{asset('public/admin/img/andriod.png')}}" class="mr-2" alt="">
                                {{ translate('For android') }}
                            </h5>
                            <div class="__bg-F8F9FC-card">
                                <div class="form-group">
                                    <label for="app_minimum_version_android_deliveryman" class="form-label text-capitalize">{{translate('Minimum_Deliveryman_App_Version')}} ({{translate('messages.android')}})
                                    </label>
                                    <input type="text" id="app_minimum_version_android_deliveryman" placeholder="1.0.0" class="form-control h--45px" name="app_minimum_version_android_deliveryman" pattern="^\d\.\d\.\d$"
                                        value="{{env('APP_MODE')!='demo'?$app_minimum_version_android_deliveryman??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Latest_Deliveryman_App_Version') }} ({{translate('messages.android')}})</label>
                                    <input type="text" name="deliveryman_android_latest_version" class="form-control h--45px" value="{{env('APP_MODE')!='demo'?$deliveryman_android_latest_version??'':''}}" placeholder="1.0.0" pattern="^\d\.\d\.\d$">
                                </div>
                                <div class="form-group">
                                    <label for="app_url_android_deliveryman"  class="form-label text-capitalize">
                                        {{translate('Download_URL_for_Deliveryman_App')}} ({{translate('messages.android')}})
                                    </label>
                                    <input type="text" id="app_url_android_deliveryman" placeholder="{{translate('messages.download_url')}}" class="form-control h--45px" name="app_url_android_deliveryman"
                                    value="{{env('APP_MODE')!='demo'?$app_url_android_deliveryman??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Force_Update') }}</label>
                                    <select name="deliveryman_android_force_update" class="form-control h--45px">
                                        <option value="1" {{ $deliveryman_android_force_update == 1 ? 'selected' : '' }}>{{ translate('messages.yes') }}</option>
                                        <option value="0" {{ $deliveryman_android_force_update == 0 ? 'selected' : '' }}>{{ translate('messages.no') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label text-capitalize">{{ translate('Blocked_Versions') }}</label>
                                    <textarea name="deliveryman_android_blocked_versions" class="form-control" placeholder="1.0.0, 1.0.1">{{ implode(',', json_decode($deliveryman_android_blocked_versions, true) ?? []) }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title mb-3">
                                <img src="{{asset('public/admin/img/ios.png')}}" class="mr-2" alt="">
                                {{ translate('For iOS') }}
                            </h5>
                            <div class="__bg-F8F9FC-card">
                                <div class="form-group">
                                    <label  for="app_minimum_version_ios_deliveryman" class="form-label text-capitalize">{{translate('Minimum_Deliveryman_App_Version')}} ({{translate('messages.ios')}})
                                    </label>
                                    <input id="app_minimum_version_ios_deliveryman" type="text" placeholder="1.0.0" class="form-control h--45px" name="app_minimum_version_ios_deliveryman" pattern="^\d\.\d\.\d$"
                                    value="{{env('APP_MODE')!='demo'?$app_minimum_version_ios_deliveryman??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Latest_Deliveryman_App_Version') }} ({{translate('messages.ios')}})</label>
                                    <input type="text" name="deliveryman_ios_latest_version" class="form-control h--45px" value="{{env('APP_MODE')!='demo'?$deliveryman_ios_latest_version??'':''}}" placeholder="1.0.0" pattern="^\d\.\d\.\d$">
                                </div>
                                <div class="form-group">
                                    <label for="app_url_ios_deliveryman" class="form-label text-capitalize">
                                        {{translate('Download_URL_for_Deliveryman_App')}} ({{translate('messages.ios')}})
                                    </label>
                                    <input id="app_url_ios_deliveryman" type="text" placeholder="{{translate('messages.download_url')}}" class="form-control h--45px" name="app_url_ios_deliveryman"
                                    value="{{env('APP_MODE')!='demo'?$app_url_ios_deliveryman??'':''}}">
                                </div>
                                <div class="form-group">
                                    <label class="form-label text-capitalize">{{ translate('Force_Update') }}</label>
                                    <select name="deliveryman_ios_force_update" class="form-control h--45px">
                                        <option value="1" {{ $deliveryman_ios_force_update == 1 ? 'selected' : '' }}>{{ translate('messages.yes') }}</option>
                                        <option value="0" {{ $deliveryman_ios_force_update == 0 ? 'selected' : '' }}>{{ translate('messages.no') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="form-label text-capitalize">{{ translate('Blocked_Versions') }}</label>
                                    <textarea name="deliveryman_ios_blocked_versions" class="form-control" placeholder="1.0.0, 1.0.1">{{ implode(',', json_decode($deliveryman_ios_blocked_versions, true) ?? []) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"  class="btn btn--primary mb-2 call-demo">{{translate('messages.submit')}}</button>
                    </div>
                </div>
            </div>
        </form>

    </div>


@endsection
