$(document).ready(function() {
    $('#add-item-row-btn').on('click', function() {
        filterItemDropdown();
        $('#add-item-row').show();
        $(this).hide();
        calculateNewItemTotal();
    });

    $('#items-table').on('click', '.remove-row', function() {
        $(this).closest('tr').hide();
        $('#add-item-row-btn').show();
        resetAddItemRow();
        filterItemDropdown();
    });
});

function resetAddItemRow() {
    $('#addItemProductId').val('');
    $('#addItemUnitId').val('');
    $('#addItemQuantity').val('');
    $('#addItemUnitPrice').val('');
    $('#addItemDiscount').val('0.00');
    $('#addItemTaxId').val('');
    $('#addItemTotalPrice').val('');

    $('.item-error').text('');
}

function updateItemDetails(selectElement) {
    var itemId = selectElement.value;
    var selectedItem = itemsData.find(item => item.id == itemId);

    if (selectedItem) {
        $('#addItemUnitId').val(selectedItem.item_units[0].unit_id);
        $('#addItemUnitPrice').val(selectedItem.item_costs[0].amount);
    } else {
        $('#addItemUnitId').val('');
        $('#addItemUnitPrice').val('');
    }
    calculateNewItemTotal();
}

function calculateNewItemTotal() {
    var quantity = parseFloat($('#addItemQuantity').val()) || 0;
    var unitPrice = parseFloat($('#addItemUnitPrice').val()) || 0;
    var discount = parseFloat($('#addItemDiscount').val()) || 0;
    var taxId = $('#addItemTaxId').val();
    var taxRate = 0;
    var taxMode = '';

    if (taxId) {
        var selectedTax = $('#addItemTaxId option:selected');
        taxMode = selectedTax.data('tax-mode');
        taxRate = parseFloat(selectedTax.data('tax-value')) || 0;
    }

    var subTotal = quantity * unitPrice;
    var itemDiscount = discount;
    var taxAmount = 0;
    var itemTotal = subTotal - itemDiscount;

    if (taxId) {
        if (taxMode === 'percentage') {
            taxAmount = itemTotal * (taxRate / 100);
        } else if (taxMode === 'fixed') {
            taxAmount = taxRate;
        }
        itemTotal += taxAmount;
    }

    $('#addItemTotalPrice').val(itemTotal.toFixed(2));
}


function filterItemDropdown() {
    var existingItemNames = [];
    $('#items-table tbody tr:not(#add-item-row)').each(function() {
        var itemName = $(this).find('td:nth-child(2)').text().trim();
        existingItemNames.push(itemName);
    });

    var addItemProductSelect = $('#addItemProductId');
    addItemProductSelect.empty();

    addItemProductSelect.append(new Option("Select Product", "", true, true));

    $.each(itemsData, function(index, item) {
        if (!existingItemNames.includes(item.name)) {
            addItemProductSelect.append(new Option(item.name, item.id, false, false));
        }
    });
}


function saveNewItem() {
    var purchaseOrderId = $('#purchase-order-id').val();
    var newItemId = $('#addItemProductId').val();

    var newItemData = {
        purchase_order_id: purchaseOrderId,
        item_id: newItemId,
        unit_id: $('#addItemUnitId').val(),
        quantity: $('#addItemQuantity').val(),
        unit_price: $('#addItemUnitPrice').val(),
        discount: $('#addItemDiscount').val(),
        tax_id: $('#addItemTaxId').val(),
        total_price: $('#addItemTotalPrice').val(),
    };

    $('.item-error').text('');

    $.ajax({
        url: '/purchases/po/new_item',
        type: 'POST',
        data: newItemData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            Swal.fire('Saved!', response.message, 'success').then((result) => {
                if (result.isConfirmed || result.isDismissed) {
                    location.reload();
                }
            });
        },
        error: function (xhr, status, error) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('.item-' + key.replace('.', '-') + '-error').text(value[0]);
                });
            } else {
                Swal.fire('Error!', 'Failed to save item.', 'error');
            }
        }
    });
}


function showEditModal(item) {
    $('#editItemId').val(item.id);
    $('#editItemQuantity').val(item.quantity);
    $('#editItemUnitPrice').val(item.unit_price);
    $('#editItemDiscount').val(item.discount);

    $('#editItemName').val(item.item_id);
    $('#editItemTax').val(item.tax_id);

    $('#editItemModal').modal('show');
}
function updateItem() {
    const id = $('#editItemId').val();
    const data = {
        item_id: $('#editItemName').val(),
        quantity: $('#editItemQuantity').val(),
        unit_price: $('#editItemUnitPrice').val(),
        discount: $('#editItemDiscount').val(),
        tax_id: $('#editItemTax').val(),
    };

    $.ajax({
        url: '/purchases/po/item/' + id,
        type: 'PUT',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            Swal.fire('Updated!', response.message, 'success');
            $('#editItemModal').modal('hide');
            location.reload();
        },
        error: function (xhr) {
            Swal.fire('Error!', xhr.responseJSON.message, 'error');
        }
    });
}
function showConfirmationModal(url, successMessage, errorMessage, itemName) {
    $('#confirmationMessage').text(successMessage);

    $('#confirmDeleteButton').off('click').on('click', function () {
        $.ajax({
            url: url,
            type: 'DELETE',
            success: function (response) {
                Swal.fire('Success!', response.message || successMessage, 'success');
                $('#confirmationModal').modal('hide');
                location.reload();
            },
            error: function (xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage || `${itemName} could not be processed.`,
                    icon: 'warning',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
}

function approvePO(id) {
    Swal.fire({
        title: 'Approve Purchase Order',
        text: 'Are you sure you want to approve this purchase order?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/po/status/' + id,
                type: 'GET',
                data: { status: 'Approved' },
                success: function (response) {
                    Swal.fire('Approved!', response.message, 'success');
                    location.reload();
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}

function cancelPO(id) {
    Swal.fire({
        title: 'Cancel Purchase Order',
        text: 'Are you sure you want to cancel this purchase order?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Cancel!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/po/status/' + id,
                type: 'GET',
                data: { status: 'Cancel' },
                success: function (response) {
                    Swal.fire('Cancel!', response.message, 'success');
                    location.reload();
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}

function rejectPO(id) {
    Swal.fire({
        title: 'Reject Purchase Order',
        text: 'Are you sure you want to reject this purchase order?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reject!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/po/status/' + id,
                type: 'GET',
                data: { status: 'Rejected' },
                success: function (response) {
                    Swal.fire('Rejected!', response.message, 'success');
                    location.reload();
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}

function receivePO(id) {
    Swal.fire({
        title: 'Receive Purchase Order',
        text: 'Mark this purchase order as received?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Receive!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/po/status/' + id,
                type: 'GET',
                data: { status: 'Partially_received' },
                success: function (response) {
                    Swal.fire('Received!', response.message, 'success');
                    location.reload();
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}

function completePO(id) {
    Swal.fire({
        title: 'Complete Purchase Order',
        text: 'Are you sure you want to mark this purchase order as complete?',
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#6c757d',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Complete!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/po/status/' + id,
                type: 'GET',
                data: { status: 'Completed' },
                success: function (response) {
                    Swal.fire('Completed!', response.message, 'success');
                    location.reload();
                },
                error: function (xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        }
    });
}