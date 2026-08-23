@extends('layouts.admin.app')

@section('title', 'أقسام المنيو - قائمة الطعام')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-category nav-icon mr-1"></i> أقسام المنيو (قائمة الطعام)
                </h1>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn--primary" href="{{ route('admin.menu.categories.create') }}">
                    <i class="tio-add"></i> إضافة قسم جديد
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-0 py-2">
            <h5 class="card-title">قائمة الأقسام <span class="badge badge-soft-dark ml-2">{{ count($categories) }}</span></h5>
        </div>
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>صورة القسم</th>
                        <th>اسم القسم</th>
                        <th>الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $key => $category)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ asset('storage/'.$category->image) }}" class="avatar avatar-lg rounded" alt="{{ $category->name }}" style="object-fit: cover;">
                                @else
                                    <span class="badge badge-soft-secondary">بدون صورة</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>
                                @if($category->status)
                                    <span class="badge badge-soft-success">مفعل</span>
                                @else
                                    <span class="badge badge-soft-danger">غير مفعل</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-white text-primary" href="{{ route('admin.menu.categories.edit', $category->id) }}" title="تعديل">
                                    <i class="tio-edit"></i> تعديل
                                </a>
                                <form action="{{ route('admin.menu.categories.destroy', $category->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت تأكد من حذف هذا القسم؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white text-danger" title="حذف">
                                        <i class="tio-delete"></i> حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">لا توجد أقسام مضافة بعد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
