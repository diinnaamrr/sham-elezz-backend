@extends('layouts.admin.app')

@section('title', 'تعديل صنف المنيو')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-edit nav-icon mr-1"></i> تعديل صنف: {{ $item->name }}
                </h1>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-secondary" href="{{ route('admin.menu.items.index') }}">
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
            <form action="{{ route('admin.menu.items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="category_id">القسم <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">-- اختر القسم --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="name">اسم الصنف <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $item->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="price">السعر (ج.م)</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $item->price) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="status">الحالة <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ old('status', $item->status) == 1 ? 'selected' : '' }}>مفعل</option>
                                <option value="0" {{ old('status', $item->status) == 0 ? 'selected' : '' }}>غير مفعل</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="input-label" for="description">الوصف</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $item->description) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="input-label">صورة الصنف</label>
                            @if($item->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/'.$item->image) }}" class="avatar avatar-xl rounded" alt="{{ $item->name }}">
                                </div>
                            @endif
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="btn--container justify-content-end mt-4">
                    <button type="submit" class="btn btn--primary">تحديث الصنف</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
