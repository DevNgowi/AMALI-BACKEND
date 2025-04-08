@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Item') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @can('can create items')
                        <div class="add_btn justify-content-end pb-2 d-flex">
                            <a href="{{ route('create_item') }}" class="btn btn-secondary btn-sm"> Add New Item</a>
                        </div>
                    @endcan
                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                                <th class="pl-4">#</th>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Barcode</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $key => $item)
                                <tr>
                                    <td class="pl-4">{{ $key + 1 }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ optional($item->units->first())->name ?? 'N/A' }}</td> 
                                    <td>{{ optional($item->barcodes->first())->code ?? 'N/A' }}</td>
                                    <td>{{ $item->category ? $item->category->name : 'N/A' }}
                                    <td>
                                        @can('can edit items')
                                            <a href="{{ route('edit_item', $item->id) }}" class="btn btn-defaults">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @endcan
                                        @can('can delete items')
                                            <span data-toggle="modal" data-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_item', $item->id) }}', 'Are you sure you want to delete this item?')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </span>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No items available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>
@endsection
