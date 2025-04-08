// Function to create new row with proper formatting and event handlers
function addNewRow() {
    const tbody = document.querySelector('#items-table tbody');
    const rowCount = tbody.children.length + 1;

    // Create new row element
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>${rowCount}</td>
        <td>
            <select name="items[${rowCount - 1}][product_id]" class="form-select product-select" required>
                <option value="" disabled selected>Select Product</option>
                ${generateProductOptions()}
            </select>
            <div class="invalid-feedback">Please select a product</div>
        </td>
        <td>
            <select name="items[${rowCount - 1}][unit_id]" class="form-select unit-select" required>
                <option value="" disabled selected>Select UOM</option>
                ${generateUnitOptions()}
            </select>
            <div class="invalid-feedback">Please select a unit</div>
        </td>
        <td>
            <input type="number" name="items[${rowCount - 1}][quantity]" 
                class="form-control quantity-input" min="1" required>
            <div class="invalid-feedback">Please enter a valid quantity</div>
        </td>
        <td>
            <input type="number" name="items[${rowCount - 1}][unit_price]" 
                class="form-control price-input" step="0.01" required>
            <div class="invalid-feedback">Please enter a valid price</div>
        </td>
        <td>
            <input type="number" name="items[${rowCount - 1}][discount]" 
                class="form-control discount-input" step="0.01" value="0">
        </td>
        <td>
            <select name="items[${rowCount - 1}][tax_id]" class="form-select tax-select">
                <option value="">None</option>
                ${generateTaxOptions()}
            </select>
        </td>
        <td>
            <input type="number" name="items[${rowCount - 1}][total_price]" 
                class="form-control total-input" readonly>
        </td>
        <td>
            <div class="btn-group">
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    `;

    // Check if the product is already selected
    const productSelect = newRow.querySelector('.product-select');
    productSelect.addEventListener('change', function() {
        const selectedProductId = this.value;
        const existingProductIds = getSelectedProductIds();

        if (existingProductIds.has(selectedProductId)) {
            alert('This product is already added to the purchase order.');
            this.value = ''; // Reset the selection
            return; // Stop further processing of this change event
        }
    });

    // Add the new row to the table
    tbody.appendChild(newRow);

    // Initialize any necessary plugins or event handlers for the new row
    initializeRowHandlers(newRow);
    
    // Update all product dropdowns to reflect the latest selections
    updateAllProductDropdowns(); 

}

// Function to get all currently selected product IDs
function getSelectedProductIds() {
    const selectedProducts = new Set();
    document.querySelectorAll('.product-select').forEach(select => {
        if (select.value) {
            selectedProducts.add(select.value);
        }
    });
    return selectedProducts;
}

function generateProductOptions(currentValue = '') {
    const selectedProducts = getSelectedProductIds(); // Get selected products *before* generating options

    return Array.from(document.querySelectorAll('.product-select option')) // Iterate over *all* options
        .map(option => {
            if (option.disabled && !option.value) { // Keep the default "Select Product" option
                return `<option value="" disabled selected>Select Product</option>`;
            }

            const isSelected = option.value === currentValue;
            if (option.value && !isSelected && selectedProducts.has(option.value)) {
                return ''; // Skip already selected products
            }

            const defaultUnit = option.dataset.defaultUnit || '';
            const defaultCost = option.dataset.defaultCost || '0';
            return `<option value="${option.value}" 
                data-default-unit="${defaultUnit}" 
                data-default-cost="${defaultCost}"
                ${isSelected ? 'selected' : ''}>
                ${option.text}
            </option>`;
        })
        .join('');
}


// Function to update *all* product dropdowns
function updateAllProductDropdowns() {
    document.querySelectorAll('.product-select').forEach(select => {
        const currentValue = select.value;
        select.innerHTML = generateProductOptions(currentValue);
    });
}

// Function to generate unit options
function generateUnitOptions() {
    const unitSelect = document.querySelector('.unit-select');
    return Array.from(unitSelect.options)
        .map(option => `<option value="${option.value}" ${option.disabled ? 'disabled' : ''}>
            ${option.text}
        </option>`)
        .join('');
}

// Function to generate tax options
function generateTaxOptions() {
    const taxSelect = document.querySelector('.tax-select');
    return Array.from(taxSelect.options)
        .map(option => {
            const taxMode = option.dataset.taxMode || '';
            const taxValue = option.dataset.taxValue || '';
            return `<option value="${option.value}" 
                data-tax-mode="${taxMode}" 
                data-tax-value="${taxValue}"
                ${option.disabled ? 'disabled' : ''}>
                ${option.text}
            </option>`;
        })
        .join('');
}

// Function to initialize event handlers for a new row
function initializeRowHandlers(row) {
    // Add event listeners for calculations
    const inputs = row.querySelectorAll('.quantity-input, .price-input, .discount-input, .tax-select');
    inputs.forEach(input => {
        input.addEventListener('change', () => calculateRowTotal(row));
    });

    // Add product select handler
    const productSelect = row.querySelector('.product-select');
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const defaultUnit = selectedOption.dataset.defaultUnit;
        const defaultCost = selectedOption.dataset.defaultCost;

        // Set default unit if available
        if (defaultUnit) {
            const unitSelect = row.querySelector('.unit-select');
            unitSelect.value = defaultUnit;
        }

        // Set default cost if available
        if (defaultCost) {
            const priceInput = row.querySelector('.price-input');
            priceInput.value = defaultCost;
        }

        calculateRowTotal(row);
        updateAllProductDropdowns(); // Update all dropdowns when a selection changes
    });

    // Add remove row handler
    const removeButton = row.querySelector('.remove-row');
    removeButton.addEventListener('click', function() {
        row.remove();
        updateRowNumbers();
        updateAllProductDropdowns(); // Update all dropdowns when a row is removed
    });
}

// Add event listener to the add row button
document.querySelector('.add-row').addEventListener('click', addNewRow);

// Update row numbers when rows are removed
function updateRowNumbers() {
    const rows = document.querySelectorAll('#items-table tbody tr');
    rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;
        
        // Update input names to maintain proper indexing
        row.querySelectorAll('[name^="items["]').forEach(element => {
            const name = element.getAttribute('name');
            const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
            element.setAttribute('name', newName);
        });
    });
}
function registerItem() {
    // Get all rows from the table
    const rows = document.querySelectorAll('#items-table tbody tr');
    const items = [];
    let isValid = true;

    // Validate and collect data from each row
    rows.forEach((row, index) => {
        const productSelect = row.querySelector('select[name^="items"][name$="[product_id]"]');
        const unitSelect = row.querySelector('select[name^="items"][name$="[unit_id]"]');
        const quantityInput = row.querySelector('input[name^="items"][name$="[quantity]"]');
        const priceInput = row.querySelector('input[name^="items"][name$="[unit_price]"]');
        const discountInput = row.querySelector('input[name^="items"][name$="[discount]"]');
        const taxSelect = row.querySelector('select[name^="items"][name$="[tax_id]"]');
        const totalInput = row.querySelector('input[name^="items"][name$="[total_price]"]');

        // Validate required fields
        if (!productSelect.value || !unitSelect.value || !quantityInput.value || !priceInput.value) {
            Swal.fire({
                title: 'Validation Error!',
                text: `Please fill in all required fields in row ${index + 1}`,
                icon: 'error',
                confirmButtonText: 'OK'
            });
            isValid = false;
            return;
        }

        // Create item object
        const item = {
            product_id: parseInt(productSelect.value),
            unit_id: parseInt(unitSelect.value),
            quantity: parseFloat(quantityInput.value),
            unit_price: parseFloat(priceInput.value),
            discount: discountInput.value ? parseFloat(discountInput.value) : 0,
            tax_id: taxSelect.value || null,
            total_price: parseFloat(totalInput.value)
        };

        items.push(item);
    });

    if (!isValid || items.length === 0) {
        Swal.fire({
            title: 'Validation Error!',
            text: 'Please add at least one valid item',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Get purchase order ID from the page
    const purchaseOrderId = document.querySelector('#purchase-order-id').value;

    // Show loading state
    Swal.fire({
        title: 'Processing...',
        text: 'Registering purchase order items',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Send data to server
    fetch(`/purchases/po/store/${purchaseOrderId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ items: items })
    })
    .then(response => {
        console.log("Response status:", response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.text();
    })
    .then(text => {
        console.log("Raw response:", text);
        return JSON.parse(text);
    })
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('registerItemModal'));
            modal.hide();
    
            // Show success message
            Swal.fire({
                title: 'Success!',
                text: 'Purchase order items registered successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Error registering items',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Error registering purchase order items',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}    

// Function to reset a row to its initial state
function resetRow(row) {
    const selects = row.querySelectorAll('select');
    const inputs = row.querySelectorAll('input:not([readonly])');
    
    selects.forEach(select => {
        select.value = '';
    });
    
    inputs.forEach(input => {
        input.value = '';
    });

    // Reset total price
    const totalInput = row.querySelector('.total-input');
    if (totalInput) {
        totalInput.value = '';
    }

    // Update product dropdowns
    updateAllProductDropdowns();
}

// Add event listener for reset buttons
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('reset-row')) {
        const row = e.target.closest('tr');
        resetRow(row);
    }
});
// Calculate total price for a row
function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.price-input').value) || 0;
    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
    const taxSelect = row.querySelector('.tax-select');
    
    let subtotal = quantity * unitPrice;
    subtotal -= discount;

    if (taxSelect.value) {
        const selectedOption = taxSelect.options[taxSelect.selectedIndex];
        const taxMode = selectedOption.dataset.taxMode;
        const taxValue = parseFloat(selectedOption.dataset.taxValue) || 0;

        if (taxMode === 'percentage') {
            subtotal += (subtotal * (taxValue / 100));
        } else {
            subtotal += taxValue;
        }
    }

    row.querySelector('.total-input').value = subtotal.toFixed(2);
}

// Add event listeners for real-time calculation
document.addEventListener('change', function(e) {
    if (e.target.matches('.quantity-input, .price-input, .discount-input, .tax-select')) {
        calculateRowTotal(e.target.closest('tr'));
    }
});

// Add event listener for product selection
document.addEventListener('change', function(e) {
    if (e.target.matches('.product-select')) {
        const row = e.target.closest('tr');
        const selectedOption = e.target.options[e.target.selectedIndex];
        const defaultUnit = selectedOption.dataset.defaultUnit;
        const defaultCost = selectedOption.dataset.defaultCost;

        if (defaultUnit) {
            const unitSelect = row.querySelector('.unit-select');
            unitSelect.value = defaultUnit;
        }

        if (defaultCost) {
            const priceInput = row.querySelector('.price-input');
            priceInput.value = defaultCost;
        }

        calculateRowTotal(row);
    }
});