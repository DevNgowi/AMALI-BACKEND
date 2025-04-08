
function validateExtraChargeForm() {
    const chargeType = document.getElementById('charge_type_id').value;
    const amount = document.getElementById('amount').value;

    if (!chargeType) {
        alert('Please select a charge type.');
        return false;
    }

    if (!amount) {
        alert('Please enter the charge amount.');
        return false;
    }

    return true;
}

const registerExtraChargeModal = document.getElementById('registerExtraChargeModal');
registerExtraChargeModal.addEventListener('show.bs.modal', () => {
    document.getElementById('name').value = '';
    document.getElementById('tax_id').value = '';
    document.getElementById('charge_type_id').value = '';
    document.getElementById('amount').value = '';
});


function populateEditExtraChargeModal(extraCharge) {
    try {
        const form = document.getElementById('editExtraChargeForm');
        if (!form) {
            throw new Error("Edit form not found");
        }

        // Set the form action
        form.action = `/financial_settings/extra_charge/${extraCharge.id}`;

        // Reset any previous validation states
        clearValidationErrors();

        // Populate form fields
        const fields = {
            'edit_name': extraCharge.name,
            'edit_tax_id': extraCharge.tax_id || '',
            'edit_charge_type_id': extraCharge.charge_type_id,
            'edit_amount': extraCharge.amount
        };

        Object.entries(fields).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
            } else {
                console.warn(`Element with id '${id}' not found`);
            }
        });
    } catch (error) {
        console.error("Error populating edit modal:", error);
        alert("There was an error loading the extra charge data. Please try again.");
    }
}

function validateEditForm() {
    let isValid = true;
    clearValidationErrors();

    // Validate name
    const nameInput = document.getElementById('edit_name');
    if (!nameInput.value.trim()) {
        showError(nameInput, 'Name is required');
        isValid = false;
    }

    // Validate charge type
    const chargeTypeInput = document.getElementById('edit_charge_type_id');
    if (!chargeTypeInput.value) {
        showError(chargeTypeInput, 'Charge type is required');
        isValid = false;
    }

    // Validate amount
    const amountInput = document.getElementById('edit_amount');
    if (!amountInput.value || amountInput.value < 0) {
        showError(amountInput, 'Please enter a valid amount');
        isValid = false;
    }

    return isValid;
}

function showError(element, message) {
    element.classList.add('is-invalid');
    const feedback = element.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = message;
    }
}

function clearValidationErrors() {
    const form = document.getElementById('editExtraChargeForm');
    const invalidElements = form.querySelectorAll('.is-invalid');
    invalidElements.forEach(element => {
        element.classList.remove('is-invalid');
    });
}