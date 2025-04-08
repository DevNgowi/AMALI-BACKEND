@extends('layouts.app')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Unit') }}</h1>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex justify-content-end pb-2">
                        @can('can create unit')
                          <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerUnitModal">
                                Add New Unit
                            </button>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                              <tr>
                                  <th>#</th>
                                  <th>Name</th>
                                  <th>Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              @forelse ($units as $key => $unit)
                                  <tr>
                                      <td>{{ $key + 1 }}</td>
                                      <td>{{ $unit->name }}</td>
                                      <td>
                                          @can('can edit unit') <span class="pr-3" data-bs-toggle="modal"
                                                  data-bs-target="#editUnitModal"
                                                  onclick="populateEditUnitModal({{ json_encode($unit) }})"> <i class="fas fa-pen"></i> </span>
                                          @endcan
  
                                          @can('can delete unit')
                                           <span class="text-danger" data-bs-toggle="modal"
                                                  data-bs-target="#confirmationModal"
                                                  onclick="showConfirmationModal('{{ route('delete_unit', $unit->id) }}', 'Are you sure you want to delete this unit?')">
                                                  <i class="fas fa-trash"></i>
                                              </span>
                                          @endcan
                                      </td>
                                  </tr>
                              @empty
                                  <tr>
                                      <td colspan="3" class="text-center">No Unit available.</td>
                                  </tr>
                              @endforelse
                          </tbody>
                      </table>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerUnitModal" tabindex="-1" aria-labelledby="registerUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('store_unit') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerUnitModalLabel">Add New Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Unit Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="name" class="form-control" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editUnitModal" tabindex="-1" aria-labelledby="editUnitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editUnitForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUnitModalLabel">Edit Unit</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Unit Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function populateEditUnitModal(unit) {
            document.getElementById('editUnitForm').action = `/inventory/unit/update/${unit.id}`;
            document.getElementById('edit_name').value = unit.name;
        }

      
    </script>
@endsection