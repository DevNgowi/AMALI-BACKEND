$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

const noItemsFoundMessage = "{{ __('No items found for this store') }}";
const errorLoadingDataMessage = "{{ __('Error loading data.') }}";

$(document).on('input', '.stock-quantity', function () {
    const stockValue = $(this).val();
    const row = $(this).closest('tr');
    row.find('.max-quantity').val(stockValue);
});

$(document).ready(function () {
    const fetchCostStockUrl = $('#fetch-cost-stock-url').val();
    const updateCostStockUrl = $('#update-cost-stock-url').val();
    const searchInput = $('.search-items input[type="text"]');

    function fetchCostStock(storeId) {
        if (!fetchCostStockUrl) { // Check if view permission exists (URL is present)
            $('#cost-stock-body').empty().html('<tr><td colspan="8" class="text-center">{{ __("No permission to view data.") }}</td></tr>');
            $('.search-items').hide();
            return;
        }

        $('#update-btn').prop('disabled', true);
        $('#cost-stock-body').empty().html('<tr><td colspan="8" class="text-center">Loading...</td></tr>');
        $('.search-items').show();

        $.ajax({
            url: fetchCostStockUrl,
            method: 'GET',
            data: {
                store_id: storeId
            },
            success: function (response) {
                $('#cost-stock-body').empty();

                if (response.length > 0) {
                    response.forEach(function (item) {
                        let taxOptions = '<option value="">---Select---</option>';
                        taxes.forEach(function (tax) {
                            let selected = item.tax_id == tax.id ? 'selected' : '';
                            taxOptions += `<option value="${tax.id}" ${selected}>${tax.name} (${tax.tax_type} - ${tax.tax_percentage})</option>`;
                        });

                        let unitOptionsBuying = '<option value="">---Select---</option>';
                        let unitOptionsSelling = '<option value="">---Select---</option>';

                        units.forEach(function (unit) {
                            let selectedBuying = item.item_buying_unit_id == unit.id ? 'selected' : '';
                            let selectedSelling = item.item_selling_unit_id == unit.id ? 'selected' : '';
                            unitOptionsBuying += `<option value="${unit.id}" ${selectedBuying}>${unit.name}</option>`;
                            unitOptionsSelling += `<option value="${unit.id}" ${selectedSelling}>${unit.name}</option>`;
                        });

                        const row = `
                            <tr>
                                <td><input type="checkbox" class="item-checkbox" data-item-id="${item.item_id}" ${!updateCostStockUrl ? 'disabled' : ''}></td>
                                <td>${item.item_name}</td>
                                <td><input type="number" class="form-control stock-quantity" data-item-id="${item.item_id}" value="${item.stock_quantity}" ${!updateCostStockUrl ? 'readonly' : ''}></td>
                                <td><input type="number" class="form-control min-quantity" data-item-id="${item.item_id}" value="${item.min_item_quantity}" ${!updateCostStockUrl ? 'readonly' : ''}></td>
                                <td><input type="number" class="form-control max-quantity" data-item-id="${item.item_id}" value="${item.max_item_quantity}" ${!updateCostStockUrl ? 'readonly' : ''}></td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" class="form-control buying-cost" data-item-id="${item.item_id}" value="${item.item_buying_cost}" ${!updateCostStockUrl ? 'readonly' : ''}>
                                        <select class="form-control buying-quantity" ${!updateCostStockUrl ? 'disabled' : ''}>${unitOptionsBuying}</select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="number" class="form-control selling-price" data-item-id="${item.item_id}" value="${item.item_selling_price}" ${!updateCostStockUrl ? 'readonly' : ''}>
                                        <select class="form-control selling-quantity" ${!updateCostStockUrl ? 'disabled' : ''}>${unitOptionsSelling}</select>
                                    </div>
                                </td>
                                <td><select class="form-control tax-select" data-item-id="${item.item_id}" ${!updateCostStockUrl ? 'disabled' : ''}>${taxOptions}</select></td>
                            </tr>`;

                        $('#cost-stock-body').append(row);
                    });

                    if (updateCostStockUrl) { // Enable button only if edit permission exists
                        $('#update-btn').prop('disabled', false);
                    } else {
                        $('#update-btn').prop('disabled', true); // Ensure disabled if no edit permission, even if table loads
                    }
                } else {
                    $('#cost-stock-body').html('<tr><td colspan="8" class="text-center">' + noItemsFoundMessage + '</td></tr>');
                    $('.search-items').hide();
                    searchInput.val('');
                }
            },
            error: function () {
                $('#cost-stock-body').html('<tr><td colspan="8" class="text-center">' + errorLoadingDataMessage + '</td></tr>');
            }
        });
    }

    $('#selected_store_id').on('change', function () {
        const storeId = $(this).val();
        if (storeId) fetchCostStock(storeId);
    });

    searchInput.on('keyup', function () {
        const searchTerm = $(this).val().toLowerCase();
        $('#cost-stock-body tr').each(function () {
            const itemName = $(this).find('td:nth-child(2)').text().toLowerCase();
            $(this).toggle(itemName.includes(searchTerm));
        });
    });

    $('#update-btn').on('click', function () {
        const updatedItems = [];
        $('#cost-stock-body tr').each(function () {
            const $checkbox = $(this).find('.item-checkbox');
            if ($checkbox.is(':checked')) {
                updatedItems.push({
                    item_id: $checkbox.data('item-id'),
                    stock_quantity: $(this).find('.stock-quantity').val(),
                    min_quantity: $(this).find('.min-quantity').val(),
                    max_quantity: $(this).find('.max-quantity').val(),
                    buying_cost: $(this).find('.buying-cost').val(),
                    selling_price: $(this).find('.selling-price').val(),
                    buying_unit_id: $(this).find('.buying-quantity').val(),
                    selling_unit_id: $(this).find('.selling-quantity').val(),
                    tax_id: $(this).find('.tax-select').val()
                });
            }
        });

        if (updatedItems.length > 0) {
            if (updateCostStockUrl) { // Double check edit permission in JS before update
                $.ajax({
                    url: updateCostStockUrl,
                    method: 'POST',
                    data: {
                        items: updatedItems
                    },
                    success: function () {
                        Swal.fire('Success!', 'Items updated successfully.', 'success').then(() => {
                            $('.item-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                        });
                    },
                    error: function (xhr) {
                        alert('Error updating items: ' + xhr.responseJSON.message);
                    }
                });
            } else {
                Swal.fire('Warning', 'You Don\'t have permission to update Cost & stock.', 'warning');
            }
        } else {
            alert('No items selected for update.');
        }
    });
});