@extends('layouts.app')
@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('Currency') }}</h1>
                </div>
            </div>
        </div>
    </div>

    @include('message')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="add_btn justify-content-end pb-2 d-flex">
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#registerCurrencyModal">
                            Add Currency
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Country</th>
                                    <th>Name</th>
                                    <th>Sign</th>
                                    <th>Sign Placement</th>
                                    <th>Currency Name in Words</th>
                                    <th>Digits After Decimal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($currencies as $key => $currency)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $currency->country->name }}</td>
                                        <td>{{ $currency->name }}</td>
                                        <td>{{ $currency->sign }}</td>
                                        <td>{{ ucfirst($currency->sign_placement) }}</td>
                                        <td>{{ $currency->currency_name_in_words }}</td>
                                        <td>{{ $currency->digits_after_decimal }}</td>
                                        <td>
                                            <span class="text-secondary pr-3" data-bs-toggle="modal"
                                                data-bs-target="#editCurrencyModal"
                                                onclick="populateEditCurrencyModal({{ json_encode($currency) }})">
                                                <i class="fas fa-pen"></i>
                                            </span>
                                            <span class="text-danger" data-toggle="modal"
                                                data-target="#confirmationModal"
                                                onclick="showConfirmationModal('{{ route('delete_currency', $currency->id) }}', 'Are you sure you want to delete this currency?')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No currencies available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="registerCurrencyModal" tabindex="-1" aria-labelledby="registerCurrencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form action="{{ route('store_currency') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerCurrencyModalLabel">Add New Currency</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3"> {{-- Row for the Country field group --}}
                            <div class="col-md-3"> {{-- col-md-3 for the label --}}
                                <label for="country_id" class="form-label">Country</label>
                            </div>
                            <div class="col-md-3"> {{-- col-md-3 for the field --}}
                                <select name="country_id" id="country_id" class="form-select" required>
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <div class="col-md-3"> 
                                <label for="currency_code" class="form-label">Currency Code</label>
                            </div>
                            <div class="col-md-3 mb-3"> {{-- col-md-3 for the field --}}
                                <input type="text" name="currency_code" id="currency_code" class="form-control" required>
                                @error('currency_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <div class="col-md-3"> {{-- col-md-3 for the label --}}
                                <label for="name" class="form-label">Currency Name</label>
                            </div>
                            <div class="col-md-3"> {{-- col-md-3 for the field --}}
                                <input type="text" name="name" id="name" class="form-control" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <div class="col-md-3"> {{-- col-md-3 for the label --}}
                                <label for="sign" class="form-label">Currency Sign</label>
                            </div>
                            <div class="col-md-3 mb-3"> {{-- col-md-3 for the field --}}
                                <input type="text" name="sign" id="sign" class="form-control" required>
                                @error('sign')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                      
                            <div class="col-md-3"> {{-- col-md-3 for the label --}}
                                <label for="sign_placement" class="form-label">Sign Placement</label>
                            </div>
                            <div class="col-md-3 mb-3"> {{-- col-md-3 for the field --}}
                                <select name="sign_placement" id="sign_placement" class="form-select" required>
                                    <option value="before">Before</option>
                                    <option value="after">After</option>
                                </select>
                                @error('sign_placement')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <div class="col-md-3"> {{-- col-md-3 for the label --}}
                                <label for="currency_name_in_words" class="form-label">Currency Name in Words</label>
                            </div>
                            <div class="col-md-3 mb-3"> {{-- col-md-3 for the field --}}
                                <input type="text" name="currency_name_in_words" id="currency_name_in_words"
                                    class="form-control" required>
                                @error('currency_name_in_words')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        
                            <div class="col-md-3"> {{-- col-md-3 for the label --}}
                                <label for="digits_after_decimal" class="form-label">Digits After Decimal</label>
                            </div>
                            <div class="col-md-3 mb-3"> {{-- col-md-3 for the field --}}
                                <input type="number" name="digits_after_decimal" id="digits_after_decimal"
                                    class="form-control" required>
                                @error('digits_after_decimal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
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

    <div class="modal fade" id="editCurrencyModal" tabindex="-1" aria-labelledby="editCurrencyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="editCurrencyForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCurrencyModalLabel">Edit Currency</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="edit_country_id" class="form-label">Country</label>
                        </div>
                        <div class="col-md-3">
                            <select name="country_id" id="edit_country_id" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" id="edit_country_{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_currency_code" class="form-label">Currency Code</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="currency_code" id="edit_currency_code" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="edit_name" class="form-label">Currency Name</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_sign" class="form-label">Currency Sign</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="sign" id="edit_sign" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_sign_placement" class="form-label">Sign Placement</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <select name="sign_placement" id="edit_sign_placement" class="form-select" required>
                                <option value="before">Before</option>
                                <option value="after">After</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="edit_currency_name_in_words" class="form-label">Currency Name in Words</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="text" name="currency_name_in_words" id="edit_currency_name_in_words"
                                class="form-control" required>
                            @error('currency_name_in_words')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="edit_digits_after_decimal" class="form-label">Digits After Decimal</label>
                        </div>
                        <div class="col-md-3 mb-3">
                            <input type="number" name="digits_after_decimal" id="edit_digits_after_decimal"
                                class="form-control" required>
                            @error('digits_after_decimal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
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
    function populateEditCurrencyModal(currency) {
        const form = document.getElementById('editCurrencyForm');
        form.action = `/vendors_finance/currencies/update/${currency.id}`;
        document.getElementById('edit_country_id').value = currency.country_id;
        document.getElementById('edit_currency_code').value = currency.currency_code; // **ADD THIS LINE**
        document.getElementById('edit_name').value = currency.name;
        document.getElementById('edit_sign').value = currency.sign;
        document.getElementById('edit_sign_placement').value = currency.sign_placement;
        document.getElementById('edit_currency_name_in_words').value = currency.currency_name_in_words;
        document.getElementById('edit_digits_after_decimal').value = currency.digits_after_decimal;
    }
</script>
@endsection
