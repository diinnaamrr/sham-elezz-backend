@extends('layouts.admin.app')

@section('title', translate('messages.menu_items'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <i class="tio-restaurant nav-icon mr-1"></i> {{ translate('messages.menu_items') }}
                </h1>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn--primary" href="{{ route('admin.menu.items.create') }}">
                    <i class="tio-add"></i> {{ translate('messages.add_new_item') }}
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
            <h5 class="card-title">{{ translate('messages.menu_items') }} <span class="badge badge-soft-dark ml-2">{{ count($items) }}</span></h5>
        </div>
        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ translate('messages.image') }}</th>
                        <th>{{ translate('messages.item_name') }}</th>
                        <th>{{ translate('messages.category') }}</th>
                        <th>{{ translate('messages.price') }}</th>
                        <th>{{ translate('messages.status') }}</th>
                        <th class="text-center">{{ translate('messages.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}" class="avatar avatar-lg rounded" alt="{{ $item->name }}" style="object-fit: cover;">
                                @else
                                    <span class="badge badge-soft-secondary">{{ translate('messages.no_image') }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                @if($item->description)
                                    <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-soft-info">{{ $item->category->name ?? translate('messages.unassigned') }}</span>
                            </td>
                            <td>
                                <strong>{{ number_format($item->price, 2) }}</strong>
                            </td>
                            <td>
                                @if($item->status)
                                    <span class="badge badge-soft-success">{{ translate('messages.active') }}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{ translate('messages.inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-white text-primary" href="{{ route('admin.menu.items.edit', $item->id) }}" title="{{ translate('messages.edit') }}">
                                    <i class="tio-edit"></i> {{ translate('messages.edit') }}
                                </a>
                                <form action="{{ route('admin.menu.items.destroy', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ translate('messages.want_to_delete_this') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white text-danger" title="{{ translate('messages.delete') }}">
                                        <i class="tio-delete"></i> {{ translate('messages.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">{{ translate('messages.no_data_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
