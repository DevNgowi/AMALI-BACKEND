
let selectedStoreIds = new Set();

$(document).ready(function () {
    let counter = $('#editTable tbody tr').length; 
   

    function generateTaxOptions(taxes, oldTaxIds, itemTaxes) {
        let options = '<option value="">Select Tax</option>';
        taxes.forEach(tax => {
            let selected = '';

            // Improved selected logic
            if (oldTaxIds && Array.isArray(oldTaxIds)) { // Check if oldTaxIds exists and is an array
                if (oldTaxIds.includes(String(tax.id))) {
                    selected = 'selected';
                }
            } else if (itemTaxes && itemTaxes.length > 0) { // Check if itemTaxes exists and is not empty
                if (itemTaxes.some(itemTax => itemTax.tax_id == tax.id)) {
                    selected = 'selected';
                }
            }

            options += `<option value="${tax.id}" ${selected}>${tax.name} (${tax.tax_type} - `;
            if (tax.tax_mode === 'percentage') {
                options += `${tax.tax_percentage || 'N/A'}%`;
            } else {
                options += `${(tax.tax_amount || 0).toFixed(2)}`;
            }
            options += `)</option>`;
        });
        return options;
    }

    function updateAvailableStores() {
        $.ajax({
            url: '/stores/list_option',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                const availableStores = response.filter(store => !selectedStoreIds.has(String(store.id))); // Use the global one
                $('#editTable tbody tr').each(function () {
                    const select = $(this).find('.store-select');
                    const currentVal = select.val();

                    select.empty();
                    select.append('<option value="">Select Store</option>');

                    availableStores.forEach(store => {
                        select.append(`<option value="${store.id}">${store.name}</option>`);
                    });
                    select.val(currentVal); // Restore selection if still available
                });

                if (availableStores.length === 0) {
                    $('#addRowBtn').prop('disabled', true);
                } else {
                    $('#addRowBtn').prop('disabled', false);
                }
            },
            error: function (error) {
                console.error('Error fetching stores:', error);
                Swal.fire('Error', 'Error fetching stores. Please try again.', 'error');
            }
        });
    }

    $('#addRowBtn').click(function () {
        $.ajax({
            url: '/stores/list_option',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                const availableStores = response.filter(store => !selectedStoreIds.has(String(store.id))); // Use the global one
                if (availableStores.length > 0) {
                    let storeOptions = '<option value="">Select Store</option>';
                    availableStores.forEach(store => {
                        storeOptions += `<option value="${store.id}">${store.name}</option>`;
                    });

                    let newRow = `
                        <tr>
                            <td>
                                <select name="store_id[]" class="form-control store-select">
                                    ${storeOptions}
                                </select>
                                <input type="hidden" name="stock_id[]" value="">
                            </td>
                            <td><input type="number" name="min_quantity[]" class="form-control" min="0"></td>
                            <td><input type="number" name="max_quantity[]" class="form-control" min="0"></td>
                            <td><input type="number" name="stock_quantity[]" class="form-control" min="0"></td>
                            <td><input type="number" name="purchase_rate[]" class="form-control" step="0.01" min="0"></td>
                            <td><input type="number" name="selling_price[]" class="form-control" step="0.01" min="0"></td>
                            <td>
                                <select name="tax_id[]" class="form-control">
                                    ${generateTaxOptions(taxes, oldTaxIds, itemTaxes)}
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </td>
                        </tr>`;

                    $('#editTable tbody').append(newRow);
                    counter++;

                    const newSelect = $('#editTable tbody tr:last .store-select');
                newSelect.change(function () {
                    const selectedStoreId = $(this).val();
                    if (selectedStoreId) {
                        selectedStoreIds.add(selectedStoreId); // Update the global one
                        updateAvailableStores();
                    }
                });
                } else {
                    Swal.fire('Warning', 'No more stores available to add.', 'warning');
                }
            },
            error: function (error) {
                console.error('Error fetching stores:', error);
                Swal.fire('Error', 'Error fetching stores. Please try again.', 'error');
            }
        });
    });

    $('#editTable').on('change', '.store-select', function () {
        const selectedStoreId = $(this).val();
        const previousSelectedId = $(this).data('previous-value');

        if (previousSelectedId) {
            selectedStoreIds.delete(previousSelectedId); // Update the global one
        }

        if (selectedStoreId) {
            if (selectedStoreIds.has(selectedStoreId)) { // Use the global one                Swal.fire('Warning', 'This store is already added.', 'warning');
                $(this).val('');
            } else {
                selectedStoreIds.add(selectedStoreId); // Update the global one
                $(this).data('previous-value', selectedStoreId);
                updateAvailableStores();
            }
        } else {
            $(this).data('previous-value', null);
            updateAvailableStores();
        }
    });

    $('#editTable').on('click', '.delete-row', function () {
        const storeId = $(this).closest('tr').find('.store-select').val();
        if (storeId) {
            selectedStoreIds.delete(storeId); // Update the global one
        }
        $(this).closest('tr').remove();
        updateAvailableStores();
    
    });

    updateAvailableStores();
});