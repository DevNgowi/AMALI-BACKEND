function calculateGroundTotal(totalAmount, tip, discount) {
    return totalAmount + tip - discount; 
}

function submitOrder() {
    try {
        const customerTypeId = document.getElementById('customer_type_id').value; // Corrected: .value
        const customerId = document.getElementById('customer_id').value;     // Corrected: .value
        const paymentId = document.getElementById('payment-type-select').value; // Corrected: from payment-type-select and assumed to be payment method id.
        const totalAmount = parseFloat(document.getElementById('total-amount').textContent.replace(/,/g, ''));
        const tip = parseFloat(document.getElementById('tip').value) || 0;
        const discount = parseFloat(document.getElementById('discount').value) || 0;

        const paymentTable = document.getElementById('payment-table').getElementsByTagName('tbody')[0];
        const items = [];
        const extraCharges = [];

        // Collect items from the payment table
        for (let row of paymentTable.rows) {
            if (row.classList.contains('extra-charge-row')) {
                // Skip extra charge rows
                continue;
            }

            const itemId = row.dataset.itemId;

            // Correctly get quantity from the input field's value
            const quantityInput = row.cells[2].querySelector('.qty-input');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 0; // Ensure quantity is parsed from input value


            const totalPrice = parseFloat(row.cells[3].textContent); // Price is still in textContent
            const price = totalPrice / quantity;

            if (!itemId) {
                Swal.fire({
                    title: 'Error!',
                    text: `Item ID not found for ${row.cells[0].textContent}. Please try removing and adding the item again.`,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            items.push({
                item_id: itemId,
                quantity: quantity,
                price: price
            });
        }
        const extraChargeRows = paymentTable.querySelectorAll('.extra-charge-row');
        console.log('Extra Charge Rows Found:', extraChargeRows.length);

        extraChargeRows.forEach(row => {
            const extraChargeId = row.dataset.extraChargeId;
            const amount = parseFloat(row.cells[3].textContent);

            if (extraChargeId) {
                extraCharges.push({
                    extra_charge_id: extraChargeId,
                    amount: amount
                });
            }
        });

        console.log('Final Extra Charges:', extraCharges); // Verify extra charges before sending


        // Calculate the ground total
        const groundTotal = calculateGroundTotal(totalAmount, tip, discount);

        const orderData = {
            customer_type_id: customerTypeId,
            customer_id: customerId,
            payment_id: paymentId, // Corrected: using payment method id
            total_amount: totalAmount,
            tip: tip,
            discount: discount,
            grand_total: groundTotal,
            items: items,
            extra_charges: extraCharges
        };

        console.log('Order Data being sent:', orderData); // Add this line to inspect the data

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }

        // Send the order to the backend
        fetch('/point_of_sale/pos/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
            .then(response => {
                return response.text().then(text => {
                    console.log('Response:', text); // Log the response
                    if (!response.ok) {
                        let errorMessage = 'Network response was not ok';
                        try {
                            const json = JSON.parse(text);
                            errorMessage = json.message || errorMessage;
                        } catch (e) {
                            // If parsing fails, keep the original error message
                        }
                        throw new Error(errorMessage);
                    }
                    return JSON.parse(text); // Attempt to parse JSON
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Order has been processed successfully!',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    });
                    resetOrderForm();
                } else {
                    throw new Error(data.message || 'Failed to process order');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.message,
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    }
}

function resetOrderForm() {
    const paymentTable = document.getElementById('payment-table').getElementsByTagName('tbody')[0];
    if (paymentTable) {
        paymentTable.innerHTML = '';
    }
    if (document.getElementById('tip')) document.getElementById('tip').value = '';
    if (document.getElementById('discount')) document.getElementById('discount').value = '';
    updateTotalAmount();

    const checkoutProcess = document.getElementById('checkout-process');
    const paymentDetailsArea = document.getElementById('payment-details-area');

    if (checkoutProcess) checkoutProcess.style.display = 'none';
    if (paymentDetailsArea) paymentDetailsArea.style.display = 'block';
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    const itemsContainer = document.getElementById('items-container');
    const printCheckoutButton = document.getElementById('print-checkout-button');

    if (itemsContainer) {
        itemsContainer.addEventListener('click', (event) => {
            const itemCard = event.target.closest('.item-card');
            if (itemCard) {
                handleItemSelection(itemCard);
            }
        });
    }

    const paymentTable = document.getElementById('payment-table')?.getElementsByTagName('tbody')[0];
    if (paymentTable) {
        paymentTable.addEventListener('click', (event) => {
            if (event.target.closest('.remove-item-btn')) {
                const row = event.target.closest('tr');
                if (row) {
                    const currentQty = parseInt(row.cells[2].textContent);
                    if (currentQty > 1) {
                        row.cells[2].textContent = currentQty - 1;
                        const unitPrice = parseFloat(row.cells[3].textContent) / currentQty;
                        row.cells[3].textContent = ((currentQty - 1) * unitPrice).toFixed(2);
                    } else {
                        row.remove();
                    }
                    updateTotalAmount();
                }
            }
        });
    }

    if (printCheckoutButton) {
        printCheckoutButton.addEventListener('click', submitOrder);
    }
});