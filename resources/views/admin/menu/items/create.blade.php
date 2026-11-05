@extends('layouts.admin.app')

@section('title', 'إضافة صنف طعام جديد')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-add-circle-outlined nav-icon mr-1"></i> إضافة صنف طعام جديد
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
            <form action="{{ route('admin.menu.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="category_id">القسم <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">-- اختر القسم --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="name">اسم الصنف <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="مثال: كباب، مسحب دجاج" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="price">السعر (ج.م)</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="input-label" for="status">الحالة <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>مفعل</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>غير مفعل</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="input-label" for="description">الوصف</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="وصف الصنف والمكونات">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="input-label">صورة الصنف</label>
                            <input type="file" name="image" class="form-control-file" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="btn--container justify-content-end mt-4">
                    <button type="reset" class="btn btn-secondary mr-2">إعادة ضبط</button>
                    <button type="submit" class="btn btn--primary">حفظ الصنف</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
