document.addEventListener('DOMContentLoaded', () => {
    const AddToCart = document.getElementById('add-to-cart');
    const totalAmountDisplay = document.getElementById('total-amount');
    const customerTypeId = document.getElementById('customer_type_id');
    const customerId = document.getElementById('customer_id');
    const applyExtraChargeButton = document.getElementById('apply-extra-charge');
    const orderNumber = window.orderNumber;
    const csrfToken = window.csrfToken;

    console.log('Order Number in JS:', orderNumber);
    console.log('CSRF Token in JS:', csrfToken);

    let cartItems = [];
    let extraCharges = [];

    function collectCartItems() {
        cartItems = [];
        const rows = document.querySelectorAll('#payment-table tbody tr');
        rows.forEach(row => {
            if (row.classList.contains('extra-charge-row')) return;
            const itemId = row.dataset.itemId;
            if (!itemId) {
                Swal.fire('Error', `Missing item ID for ${row.cells[0].textContent.trim()}`, 'error');
                throw new Error('Missing item_id');
            }
            const quantityInput = row.cells[2].querySelector('input');
            const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : parseInt(row.cells[2].textContent.trim()) || 1;
            const amountText = row.cells[3].textContent.replace(/,/g, '');
            const item = {
                item_id: itemId,
                name: row.cells[0].textContent.trim(),
                unit: row.cells[1].textContent.trim(),
                quantity: quantity,
                amount: amountText ? parseFloat(amountText) || 0 : 0
            };
            console.log('Collected Item:', item);
            cartItems.push(item);
        });
        console.log('All Cart Items:', cartItems);
    }

    applyExtraChargeButton.addEventListener('click', () => {
        extraCharges = [];
        const checkboxes = document.querySelectorAll('#extra-charge-checkboxes .form-check-input:checked');
        checkboxes.forEach(checkbox => {
            extraCharges.push({
                name: checkbox.getAttribute('data-name') || checkbox.nextElementSibling.textContent,
                amount: parseFloat(checkbox.value) || 0
            });
        });
        updateTotalAmount();
        bootstrap.Modal.getInstance(document.getElementById('extra-charge-modal')).hide();
    });

    function updateTotalAmount() {
        collectCartItems();
        let total = cartItems.reduce((sum, item) => sum + item.amount, 0);
        total += extraCharges.reduce((sum, charge) => sum + charge.amount, 0);
        totalAmountDisplay.textContent = formatNumber(total);
    }

    AddToCart.addEventListener('click', () => {
        collectCartItems();
        if (cartItems.length === 0) {
            Swal.fire('Error', 'No items in cart to add', 'error');
            return;
        }

        const now = new Date();
        const mysqlDate = now.getFullYear() + '-' +
            String(now.getMonth() + 1).padStart(2, '0') + '-' +
            String(now.getDate()).padStart(2, '0') + ' ' +
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0') + ':' +
            String(now.getSeconds()).padStart(2, '0');

        const cartData = {
            order_number: orderNumber,
            customer_type_id: customerTypeId.value,
            customer_id: customerId.value || null,
            total_amount: parseFloat(totalAmountDisplay.textContent.replace(/,/g, '')),
            items: cartItems, // Now includes item_id
            extra_charges: extraCharges,
            date: mysqlDate
        };

        console.log('Cart Data:', cartData);

        if (!cartData.order_number) {
            Swal.fire('Error', 'No valid order number provided', 'error');
            return;
        }

        fetch('/point_of_sale/cart/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(cartData)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', 'Cart saved successfully', 'success')
                        .then(() => {
                            document.querySelector('#payment-table tbody').innerHTML = '';
                            totalAmountDisplay.textContent = '0.00';
                            extraCharges = [];
                            document.getElementById('checkout-process').style.display = 'none';
                            document.getElementById('payment-details-area').style.display = 'block';
                        });
                } else {
                    Swal.fire('Error', data.message || 'Failed to save cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'An error occurred while saving cart', 'error');
            });
    });

    function formatNumber(number) {
        return number.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
});