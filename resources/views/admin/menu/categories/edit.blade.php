@extends('layouts.admin.app')

@section('title', 'تعديل قسم المنيو')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-edit nav-icon mr-1"></i> تعديل قسم المنيو: {{ $category->name }}
                </h1>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-secondary" href="{{ route('admin.menu.categories.index') }}">
                    <i class="tio-back-ui"></i> رجوع
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.menu.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="name">اسم القسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="status">الحالة <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ old('status', $category->status) == 1 ? 'selected' : '' }}>مفعل</option>
                                <option value="0" {{ old('status', $category->status) == 0 ? 'selected' : '' }}>غير مفعل</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="input-label">صورة القسم</label>
                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$category->image) }}" class="avatar avatar-xl rounded" alt="{{ $category->name }}">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="btn--container justify-content-end mt-4">
                    <button type="submit" class="btn btn--primary">تحديث القسم</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
