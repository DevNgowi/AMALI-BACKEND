@extends('layouts.app')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Expenses') }}</h1>
                </div>
                <div class="col-sm-6 d-none">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Expenses') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="justify-content-end pb-2 d-flex">
                        <a href="{{ route('create_expenses') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> {{ __('Create New Expense') }}
                        </a>
                    </div>
                    <form class="d-none" action="{{ route('list_expenses') }}" method="GET">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-category">{{ __('Category') }}</label>
                                    <select class="form-control select2" id="filter-category" name="filter_category">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach($expenseCategories as $cat)
                                            <option value="{{$cat->id}}" {{ request()->get('filter_category') == $cat->id ? 'selected' : '' }}>{{$cat->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-reimbursable">{{ __('Reimbursable Status') }}</label>
                                    <select class="form-control" id="filter-reimbursable" name="filter_reimbursable">
                                        <option value="">{{ __('All Statuses') }}</option>
                                        <option value="1" {{ request()->get('filter_reimbursable') === '1' ? 'selected' : '' }}>{{ __('Reimbursable') }}</option>
                                        <option value="0" {{ request()->get('filter_reimbursable') === '0' ? 'selected' : '' }}>{{ __('Not Reimbursable') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-date-range">{{ __('Date Range') }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control float-right datepicker" id="filter-date-range"  name="filter_date_range" placeholder="Select date range" value="{{ request()->get('filter_date_range') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-search">{{ __('Search') }}</label>
                                    <input type="text" class="form-control" id="filter-search" name="filter_search" placeholder="{{ __('Search...') }}" value="{{ request()->get('filter_search') }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="d-none" id="filter-button">Filter</button> {{-- Hidden submit button to trigger form submission --}}
                    </form>
                    
                        <table class="table table-bordered table-striped" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Payment Method') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th style="width: 120px">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenses as $key => $expense) 
                                    <tr>
                                        <td>{{ $key+1}}</td> 
                                        <td>{{ $expense->expense_date }}</td> 
                                        <td>{{ $expense->expenseCategory->name ?? 'N/A' }}</td> 
                                        <td>{{ $expense->amount }} {{ $expense->currency }}</td> 
                                        <td>{{ $expense->paymentType->name ?? 'N/A' }}</td> 
                                        <td>{{ $expense->is_reimbursable ? __('Reimbursable') : __('Not Reimbursable') }}</td> 
                                        <td>{{ $expense->description }}</td> 
                                        <td class="text-center">
                                             @can('can edit expenses')
                                        <button type="button" class="btn btn-sm bg-none mr-1"
                                            data-bs-toggle="modal" data-bs-target="#editExpenseModal"
                                            onclick="populateEditExpenseModal({{ json_encode($expense) }})">

                                        </button>

                                        <a href="{{ route('edit_expenses', $expense->id) }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    @endcan
                                    @can('can delete expenses')
                                        <button type="button" class="btn btn-sm bg-none"
                                            data-bs-toggle="modal" data-bs-target="#confirmationModal"
                                            onclick="showConfirmationModal('{{ route('delete_expenses', $expense->id) }}', '{{ __('Are you sure you want to delete this expense?') }}')">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No Expenses records found.') }}</td> {{-- Display message if no expenses --}}
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="float-right">
                            {{ $expenses->links() }} {{-- Pagination links --}}
                        </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                theme: 'bootstrap4',
            });

            $('.datepicker').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            $('#filter-date-range').daterangepicker({
                opens: 'left',
                locale: {
                    format: 'YYYY-MM-DD' // consistent format
                },
                ranges: {
                   'Today': [moment(), moment()],
                   'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                   'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                   'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                   'This Month': [moment().startOf('month'), moment().endOf('month')],
                   'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, function(start, end, label) {
                //console.log("A new date range was picked: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                $('#filter-date-range').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
                $('#filter-button').click(); // Trigger form submission on date range change
            });


            // Event listeners for other filters to submit the form
            $('#filter-category, #filter-reimbursable, #filter-search').on('change', function() {
                $('#filter-button').click();
            });
            $('#filter-search').on('keypress', function (e) {
                if (e.key === 'Enter') {
                    $('#filter-button').click();
                    e.preventDefault(); // Prevent default form submission on Enter in search
                }
            });
        });
    </script>
@endpush
