// Function to handle tax field toggling
function toggleTaxFields() {
    const taxMode = document.getElementById('tax_mode');
    if (!taxMode) return; // Guard clause to prevent errors

    const percentageField = document.querySelector('.tax-percentage-field');
    const amountField = document.querySelector('.tax-amount-field');
    
    if (!percentageField || !amountField) return; // Guard clause

    const mode = taxMode.value;
    if (mode === 'percentage') {
        percentageField.style.display = 'block';
        amountField.style.display = 'none';
        safeSetValue('tax_amount', '');
        safeSetRequired('tax_percentage', true);
        safeSetRequired('tax_amount', false);
    } else if (mode === 'amount') {
        percentageField.style.display = 'none';
        amountField.style.display = 'block';
        safeSetValue('tax_percentage', '');
        safeSetRequired('tax_percentage', false);
        safeSetRequired('tax_amount', true);
    } else {
        percentageField.style.display = 'none';
        amountField.style.display = 'none';
        safeSetRequired('tax_percentage', false);
        safeSetRequired('tax_amount', false);
    }
}

// Helper functions to safely handle DOM operations
function safeSetValue(id, value) {
    const element = document.getElementById(id);
    if (element) element.value = value;
}

function safeSetRequired(id, required) {
    const element = document.getElementById(id);
    if (element) element.required = required;
}

function validateTaxForm() {
    const taxMode = document.getElementById('tax_mode');
    if (!taxMode) return true;

    const mode = taxMode.value;
    const taxPercentage = document.getElementById('tax_percentage')?.value;
    const taxAmount = document.getElementById('tax_amount')?.value;

    if (mode === 'percentage' && !taxPercentage) {
        alert('Please enter the tax percentage.');
        return false;
    }
    if (mode === 'amount' && !taxAmount) {
        alert('Please enter the tax amount.');
        return false;
    }
    return true;
}

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize modal event listeners if modal exists
    const registerTaxModal = document.getElementById('registerTaxModal');
    if (registerTaxModal) {
        registerTaxModal.addEventListener('show.bs.modal', () => {
            ['name', 'tax_type', 'tax_mode', 'tax_percentage', 'tax_amount'].forEach(id => {
                safeSetValue(id, '');
            });

            const percentageField = document.querySelector('.tax-percentage-field');
            const amountField = document.querySelector('.tax-amount-field');
            
            if (percentageField) percentageField.style.display = 'none';
            if (amountField) amountField.style.display = 'none';
        });
    }

    // Add event listener for tax mode changes
    const taxModeSelect = document.getElementById('tax_mode');
    if (taxModeSelect) {
        taxModeSelect.addEventListener('change', toggleTaxFields);
    }
});

function toggleEditTaxFields() {
    const taxMode = document.getElementById('edit_tax_mode');
    if (!taxMode) return;

    const percentageField = document.querySelector('.edit-tax-percentage-field');
    const amountField = document.querySelector('.edit-tax-amount-field');
    
    if (!percentageField || !amountField) return;

    const mode = taxMode.value;
    if (mode === 'percentage') {
        percentageField.style.display = 'block';
        amountField.style.display = 'none';
        safeSetValue('edit_tax_amount', '');
    } else if (mode === 'amount') {
        percentageField.style.display = 'none';
        amountField.style.display = 'block';
        safeSetValue('edit_tax_percentage', '');
    } else {
        percentageField.style.display = 'none';
        amountField.style.display = 'none';
    }
}

function populateEditTaxModal(tax) {
    if (!tax) return;

    const form = document.getElementById('editTaxForm');
    if (!form) return;

    form.action = `/financial_settings/tax/${tax.id}`;
    
    ['name', 'tax_type', 'tax_mode', 'tax_percentage', 'tax_amount'].forEach(field => {
        safeSetValue(`edit_${field}`, tax[field]);
    });

    toggleEditTaxFields();
}