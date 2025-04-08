class StoreTableManager {
    constructor() {
        // Main table elements
        this.storeTable = document.getElementById('storeTable')?.getElementsByTagName('tbody')[0];
        this.addStoreButton = document.getElementById('addStoreRow');
        
        // Form selects
        this.vendorSelect = document.getElementById('vendorSelect');
        this.buyingUnitSelect = document.getElementById('buying_unit_id');
        this.sellingUnitSelect = document.getElementById('selling_unit_id');

        // Data storage
        this.taxOptions = [];
        this.stores = [];

        // Initialize
        this.initializeEventListeners();
        this.loadInitialData();
    }

    initializeEventListeners() {
        // Vendor select event
        if (this.vendorSelect) {
            this.vendorSelect.addEventListener('change', () => this.fetchStoresForVendor());
        }

        // Add row button event
        if (this.addStoreButton) {
            this.addStoreButton.addEventListener('click', () => this.addStoreRow());
        }

        // Table delete row event
        if (this.storeTable) {
            this.storeTable.addEventListener('click', (e) => {
                if (e.target.classList.contains('delete-store-row')) {
                    this.deleteStoreRow(e.target);
                }
            });
        }

        // Unit select events
        if (this.buyingUnitSelect) {
            this.buyingUnitSelect.addEventListener('change', () => this.updatePurchaseRateLabels());
        }
        if (this.sellingUnitSelect) {
            this.sellingUnitSelect.addEventListener('change', () => this.updateSellingPriceLabels());
        }
    }

    async loadInitialData() {
        try {
            await Promise.all([
                this.loadTaxOptions(),
                this.loadStores()
            ]);
            // Add initial row after data is loaded
            this.addStoreRow();
        } catch (error) {
            console.error('Error loading initial data:', error);
            this.showError('Failed to load initial data. Please refresh the page.');
        }
    }

    async loadTaxOptions() {
        try {
            const response = await fetch('/financial_settings/tax/list_option');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            this.taxOptions = await response.json();
        } catch (error) {
            console.error('Failed to load tax options:', error);
            this.showError('Failed to load tax options.');
        }
    }

    async loadStores() {
        try {
            const response = await fetch('/stores/list_option');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            this.stores = await response.json();
        } catch (error) {
            console.error('Failed to load stores:', error);
            this.showError('Failed to load stores.');
        }
    }

    updatePurchaseRateLabels() {
        const selectedUnit = this.buyingUnitSelect.options[this.buyingUnitSelect.selectedIndex]?.text || '';
        document.querySelectorAll('.purchase-rate-unit').forEach(label => {
            label.textContent = selectedUnit;
        });
    
        // Validate selling price against purchase price
        this.validateSellingPrice();
    }
    
    updateSellingPriceLabels() {
        const selectedUnit = this.sellingUnitSelect.options[this.sellingUnitSelect.selectedIndex]?.text || '';
        document.querySelectorAll('.selling-price-unit').forEach(label => {
            label.textContent = selectedUnit;
        });
    
        // Validate selling price against purchase price
        this.validateSellingPrice();
    }
    
    validateSellingPrice() {
        const purchaseRateInputs = document.querySelectorAll('input[name="purchase_rate[]"]');
        const sellingPriceInputs = document.querySelectorAll('input[name="selling_price[]"]');
    
        purchaseRateInputs.forEach((purchaseRateInput, index) => {
            const purchaseRate = parseFloat(purchaseRateInput.value);
            const sellingPrice = parseFloat(sellingPriceInputs[index].value);
    
            if (sellingPrice < purchaseRate) {
                sellingPriceInputs[index].value = purchaseRate; // Set selling price to purchase rate
                Swal.fire('Warning', 'Selling price cannot be less than the purchase rate of ${purchaseRate}.' , 'Warning');
            }
        });
    }

    getTaxOptionsHtml() {
        let options = '<option value="">Select Tax</option>';
        this.taxOptions.forEach(tax => {
            if (tax.id) {
                const taxType = tax.tax_type ?? 'N/A';
                const taxPercentage = tax.tax_percentage ?? '0';
                options += `<option value="${tax.id}">${tax.name} (${taxType} - ${taxPercentage}%)</option>`;
            }
        });
        return options;
    }

    getStoresOptionsHtml() {
        let options = '<option value="">Select Store</option>';
        this.stores.forEach(store => {
            if (store.id) {
                options += `<option value="${store.id}">${store.name}</option>`;
            }
        });
        return options;
    }

    validateQuantityInputs(row) {
        const minQuantityInput = row.querySelector('input[name="min_quantity[]"]');
        const maxQuantityInput = row.querySelector('input[name="max_quantity[]"]');
        const stockQuantityInput = row.querySelector('input[name="stock_quantity[]"]');
    
        // Min quantity validation
        minQuantityInput.addEventListener('change', () => {
            const minVal = parseFloat(minQuantityInput.value);
            maxQuantityInput.min = minVal; // Set the minimum value for max quantity
            if (parseFloat(maxQuantityInput.value) < minVal) {
                maxQuantityInput.value = minVal; // Adjust max quantity if it's less than min
            }
            if (parseFloat(stockQuantityInput.value) < minVal) {
                stockQuantityInput.value = minVal; // Adjust stock quantity if it's less than min
                Swal.fire('Warning', 'Stock quantity cannot be less than the minimum quantity of ${minVal}.', 'Warning');
            }
        });
    
        // Max quantity validation
        maxQuantityInput.addEventListener('change', () => {
            const maxVal = parseFloat(maxQuantityInput.value);
            const minVal = parseFloat(minQuantityInput.value);
            if (maxVal < minVal) {
                maxQuantityInput.value = minVal; // Adjust max quantity if it's less than min
                Swal.fire('Warning', 'Maximum quantity cannot be less than the minimum quantity of ${minVal}.' , 'Warning');
            }
            if (parseFloat(stockQuantityInput.value) > maxVal) {
                stockQuantityInput.value = maxVal; // Adjust stock quantity if it's greater than max
                Swal.fire('Warning', 'Stock quantity cannot exceed the maximum quantity of ${maxVal}.', 'Warning');
            }
        });
    
        // Stock quantity validation
        stockQuantityInput.addEventListener('change', () => {
            const stockVal = parseFloat(stockQuantityInput.value);
            const minVal = parseFloat(minQuantityInput.value);
            const maxVal = parseFloat(maxQuantityInput.value);
            if (stockVal < minVal) {
                stockQuantityInput.value = minVal; // Adjust stock quantity if it's less than min
                Swal.fire('Warning', 'Stock quantity cannot be less than the minimum quantity of ${minVal}.', 'Warning');
            }
            if (stockVal > maxVal) {
                stockQuantityInput.value = maxVal; // Adjust stock quantity if it's greater than max
                Swal.fire('Warning', 'Stock quantity cannot exceed the maximum quantity of ${maxVal}.', 'Warning');
            }
        });
    }

    addStoreRow() {
        if (!this.storeTable) return;

        const buyingUnit = this.buyingUnitSelect.options[this.buyingUnitSelect.selectedIndex]?.text || '';
        const sellingUnit = this.sellingUnitSelect.options[this.sellingUnitSelect.selectedIndex]?.text || '';

        const newRow = document.createElement('tr');
        const storeSelectHtml = this.getAvailableStoresOptionsHtml(); // Use the new function

        if (!storeSelectHtml) { // Check if any stores are available
            Swal.fire('No Store Available', 'No more stores are available to add.', 'warning');
            return;
        }
        newRow.innerHTML = `
            <td>
                <select name="store_id[]" class="form-control" required>
                    ${storeSelectHtml}
                </select>
            </td>
            <td>
                <input type="number" 
                       name="min_quantity[]" 
                       class="form-control" 
                       min="0"
                       value="0"
                       step="1" 
                       required>
            </td>
            <td>
                <input type="number" 
                       name="max_quantity[]" 
                       class="form-control" 
                       min="0"
                       value="0"
                       step="1" 
                       required>
            </td>
            <td>
                <input type="number" 
                       name="stock_quantity[]" 
                       class="form-control" 
                       min="0"
                       value="0"
                       step="1"
                       required>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" 
                           name="purchase_rate[]" 
                           class="form-control" 
                           min="0"
                           value="0"
                           step="0.01" 
                           required>
                    <span class="input-group-text purchase-rate-unit">${buyingUnit}</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" 
                           name="selling_price[]" 
                           class="form-control" 
                           min="0"
                           value="0"
                           step="0.01" 
                           required>
                    <span class="input-group-text selling-price-unit">${sellingUnit}</span>
                </div>
            </td>
            <td>
                <select name="tax_id[]" class="form-control">
                    <option value="">None</option>
                    ${this.getTaxOptionsHtml()}
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-store-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        this.validateQuantityInputs(newRow);
        this.storeTable.appendChild(newRow);
    }

    getAvailableStoresOptionsHtml() {
        const selectedStoreIds = new Set();
        this.storeTable.querySelectorAll('select[name="store_id[]"]').forEach(select => {
            const selectedId = select.value;
            if (selectedId) {
                selectedStoreIds.add(selectedId);
            }
        });

        let options = '<option value="">Select Store</option>';
        let availableStores = this.stores.filter(store => !selectedStoreIds.has(String(store.id))); // Filter stores

        if (availableStores.length === 0) {
            return null; // Return null if no stores are available
        }


        availableStores.forEach(store => {
            options += `<option value="${store.id}">${store.name}</option>`;
        });
        return options;
    }

    updateStoreSelects() {
      const storeSelects = this.storeTable.querySelectorAll('select[name="store_id[]"]');
      storeSelects.forEach(select => {
          const currentValue = select.value;
          const availableOptions = this.getAvailableStoresOptionsHtml();
          if (availableOptions) {
              select.innerHTML = availableOptions;
              select.value = currentValue; // Try to re-select if still available
          } else {
              select.innerHTML = '<option value="">No Stores Available</option>'; // Or disable the select
          }
      });
    }

    deleteStoreRow(button) {
        const row = button.closest('tr');
        if (this.storeTable.rows.length > 1) {
            row.remove();
            this.updateStoreSelects(); 
        } else {
            this.showError('Cannot delete the last row.');
        }
    }
    showError(message) {
        // You can customize this based on your UI needs
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonText: 'OK'
        });
    }

    async fetchStoresForVendor() {
        try {
            const vendorId = this.vendorSelect.value;
            if (!vendorId) return;

            const response = await fetch(`/stores/list_by_vendor/${vendorId}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            this.stores = await response.json();
            this.updateStoreSelects();
        } catch (error) {
            console.error('Failed to fetch stores for vendor:', error);
            this.showError('Failed to load stores for selected vendor.');
        }
    }

    updateStoreSelects() {
        const storeSelects = this.storeTable.querySelectorAll('select[name="store_id[]"]');
        storeSelects.forEach(select => {
            const currentValue = select.value;
            select.innerHTML = this.getStoresOptionsHtml();
            select.value = currentValue;
        });
    }
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    window.storeTableManager = new StoreTableManager();
});