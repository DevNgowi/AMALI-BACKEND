@extends('layouts.app')

@section('content')
    @include('message')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Virtual Devices') }}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid pt-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#registerDeviceModal">
                            Add New Device
                        </button>
                    </div>

                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virtual_devices as $key => $virtual_device)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $virtual_device->name }}</td>
                                    <td>

                                        <span class="mr-3 bg-none"
                                            onclick="openEditModal({{ $virtual_device->id }}, '{{ $virtual_device->name }}')">
                                            <i class="fas fa-pen"></i>
                                        </span>

                                        <a href="javascript:void(0);" class="text-danger"
                                            onclick="showConfirmationModal('{{ route('delete_virtual_devices', $virtual_device->id) }}', 'Are you sure you want to delete this virtual device?', 'This virtual device could not be deleted.', '{{ $virtual_device->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="editDeviceForm" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Device</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="deviceName" class="form-label">Device Name</label>
                                        <input type="text" class="form-control" id="deviceName" name="name"
                                            required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="registerDeviceModal" tabindex="-1" aria-labelledby="registerDeviceModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('store_virtual_devices') }}" method="POST" id="registerDeviceForm">
                                @csrf
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="registerDeviceModalLabel">Register New Device</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="deviceName" class="form-label">Device Name</label>
                                        <input type="text" class="form-control" id="deviceName" name="name"
                                            placeholder="Enter device name" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save Device</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(deviceId, deviceName) {
            document.getElementById('editDeviceForm').action = `/settings/virtual_devices/update/${deviceId}`;
            document.getElementById('deviceName').value = deviceName;
            $('#editModal').modal('show');
        }
    </script>
@endsection
