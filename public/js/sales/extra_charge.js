document.addEventListener('DOMContentLoaded', () => {
    const extraChargeButton = document.getElementById('extra-charge-button');
    const extraChargeModal = document.getElementById('extra-charge-modal');
    const closeModal = document.querySelector('.btn-close');
    const applyExtraChargeButton = document.getElementById('apply-extra-charge');
    const extraChargeCheckboxes = document.getElementById('extra-charge-checkboxes');
    
    // Initialize the Bootstrap modal instance
    const modal = new bootstrap.Modal(extraChargeModal);

    // Open Modal
    extraChargeButton.addEventListener('click', async () => {
        try {
            const response = await fetch('/point_of_sale/pos/extra_charge');
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            extraChargeCheckboxes.innerHTML = '';
            data.data.forEach(charge => {
                const checkbox = document.createElement('label');
                checkbox.innerHTML = `
                    <input type="checkbox" name="extra-charge" 
                           value="${charge.amount}" 
                           data-charge-id="${charge.extra_charge_id}"
                           data-charge-name="${charge.name}">
                    ${charge.name} (${charge.amount})
                `;
                extraChargeCheckboxes.appendChild(checkbox);
                extraChargeCheckboxes.appendChild(document.createElement('br'));
            });
            // Show the modal
            modal.show();
        } catch (error) {
            console.error('Error fetching extra charges:', error);
        }
    });

    // Close Modal when the close button is clicked
    closeModal.addEventListener('click', () => {
        modal.hide();
    });

    // Apply Extra Charges and Close Modal
    applyExtraChargeButton.addEventListener('click', () => {
        const paymentTable = document.getElementById('payment-table').getElementsByTagName('tbody')[0];
        const checkboxes = document.querySelectorAll('#extra-charge-checkboxes input[type="checkbox"]:checked');
        
        checkboxes.forEach(checkbox => {
            const chargeId = checkbox.dataset.chargeId;
            const chargeName = checkbox.dataset.chargeName;
            const chargeAmount = parseFloat(checkbox.value);
            
            // Create a new row for each selected charge
            const extraChargeRow = paymentTable.insertRow();
            extraChargeRow.dataset.extraChargeId = chargeId;
            extraChargeRow.classList.add('extra-charge-row');
            
            extraChargeRow.innerHTML = `
                <td>${chargeName}</td>
                <td>-</td>
                <td>-</td>
                <td>${chargeAmount.toFixed(2)}</td>
                <td>
                 
                    <a class="delete-extra-charge text-danger" >
                      <i class="fas fa-trash"></i>
                    </a>
                </td>
            `;

            // Add event listener to the delete button
            const deleteButton = extraChargeRow.querySelector('.delete-extra-charge');
            deleteButton.addEventListener('click', function() {
                extraChargeRow.remove();
                updateTotalAmount();
            });
        });

        // Update the total amount
        updateTotalAmount();

        // Close the modal
        modal.hide();

        // Manually remove the backdrop if it persists
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    });
});