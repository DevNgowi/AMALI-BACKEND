@extends('layouts.app')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Permissions') }}</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Permission Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                                @foreach ($permissions as $key => $permission)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <a href="">
                                            <i class="fas fa-pen"></i>
                                        </a>

                                        <a href=""></a>


                                    </td>
                                </tr>
                                @endforeach
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @endsection