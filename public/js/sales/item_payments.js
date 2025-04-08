function handleItemSelection(item) {
    try {
        const itemName = item.querySelector('.item-name')?.textContent;
        const itemInfoText = item.querySelector('.item-info')?.textContent;

        if (!itemName || !itemInfoText) {
            throw new Error('Required item information is missing');
        }

        const priceMatch = itemInfoText.match(/\(([\d.]+)\)/);
        const itemPrice = priceMatch ? parseFloat(priceMatch[1]) : 0;

        // Extract stock quantity and unit from itemInfoText
        const itemDetails = itemInfoText.split('-');
        if (itemDetails.length < 2) {
            throw new Error('Invalid item info format');
        }

        const stockInfo = itemDetails[1].trim().split(' ');
        if (stockInfo.length < 2) {
            throw new Error('Invalid stock information format');
        }

        let stockQuantity = parseInt(stockInfo[0]);
        const itemUnit = stockInfo[1];

        if (isNaN(stockQuantity) || isNaN(itemPrice)) {
            throw new Error('Invalid number format for stock quantity or price');
        }

        // Check if the item is out of stock
        if (stockQuantity <= 0) {
            Swal.fire({
                title: 'Out of Stock',
                text: `${itemName} is currently out of stock`,
                icon: 'warning',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            });
            return;
        }

        const paymentTable = document.getElementById('payment-table')?.getElementsByTagName('tbody')[0];
        if (!paymentTable) {
            throw new Error('Payment table not found');
        }

        let existingRow = null;

        // Check if the item already exists in the payment table
        for (let row of paymentTable.rows) {
            if (row.cells[0].textContent === itemName) {
                existingRow = row;
                break;
            }
        }

        // If the item already exists, update the quantity and total price
        if (existingRow) {
            const qtyInput = existingRow.cells[2].querySelector('input');
            let currentQty = parseInt(qtyInput.value);

            // This is the problem - it's checking if currentQty >= stockQuantity
            // Should check if currentQty + 1 > stockQuantity instead
            if (currentQty + 1 > stockQuantity) {
                Swal.fire({
                    title: 'Stock Limit Reached',
                    text: `Cannot add more ${itemName}. No more stock available.`,
                    icon: 'warning',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            qtyInput.value = currentQty + 1;
            existingRow.cells[3].textContent = ((currentQty + 1) * itemPrice).toFixed(2);

            // Update hidden quantity input in existing row
            const hiddenQtyInputExistingRow = existingRow.querySelector('.item-quantity-input');
            if (hiddenQtyInputExistingRow) {
                hiddenQtyInputExistingRow.value = currentQty + 1;
            }
        } else {
            const newRow = paymentTable.insertRow();
            const itemId = item.dataset.itemId;

            if (!itemId) {
                throw new Error('Item ID is missing');
            }

            const defaultQuantity = 1;
            newRow.dataset.itemId = itemId;
            newRow.innerHTML = `
                <td>${itemName}</td>
                <td>${itemUnit}</td>
                <td>
                    <input type="number" class="form-control qty-input" value="${defaultQuantity}" min="1" max="${stockQuantity}">
                    <input type="hidden" name="item_ids[]" value="${itemId}">
                    <input type="hidden"  name="quantities[]" class="item-quantity-input" readonly value="${defaultQuantity}">
                </td>
                <td class="item-price-cell">${(defaultQuantity * itemPrice).toFixed(2)}
                    <input type="hidden" name="prices[]" class="item-price-input" value="${itemPrice}">
                </td>
                <td>
                    <span class="remove-item-btn btn"><i class="fas fa-trash text-danger"></i></span>
                </td>
            `;

            // Add event listener for quantity change
            newRow.querySelector('.qty-input').addEventListener('input', function () {
                updateItemTotal(newRow, itemPrice, stockQuantity);
            });

            // Add event listener for remove button
            newRow.querySelector('.remove-item-btn').addEventListener('click', function () {
                newRow.remove();
                updateTotalAmount();
            });
        }

        // Update the total amount
        updateTotalAmount();

        // Deduct one from the stock quantity and update display
        stockQuantity -= 1;
        item.querySelector('.item-info').textContent = `(${itemPrice}) - ${stockQuantity} ${itemUnit}`;
    } catch (error) {
        Swal.fire({
            title: 'Error',
            text: error.message,
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    }
}


function updateItemTotal(row, itemPrice, stockQuantity) {
    try {
        const qtyInput = row.querySelector('.qty-input');
        let newQty = parseInt(qtyInput.value);

        if (isNaN(newQty) || newQty < 1) {
            newQty = 1; // Default to 1 if invalid
        } else if (newQty > stockQuantity) {
            newQty = stockQuantity; // Prevent exceeding stock
            Swal.fire({
                title: 'Stock Limit Reached',
                text: `Cannot add more than ${stockQuantity} items.`,
                icon: 'warning',
                confirmButtonColor: '#dc3545'
            });
        }

        qtyInput.value = newQty;

        // Update hidden quantity input
        const hiddenQtyInput = row.querySelector('.item-quantity-input');
        if (hiddenQtyInput) {
            hiddenQtyInput.value = newQty;
        }


        row.cells[3].textContent = (newQty * itemPrice).toFixed(2);
        updateTotalAmount();
    } catch (error) {
        console.error('Error updating item total:', error);
    }
}


function updateTotalAmount() {
    try {
        const paymentTable = document.getElementById('payment-table')?.getElementsByTagName('tbody')[0];
        if (!paymentTable) {
            throw new Error('Payment table not found');
        }

        let total = 0;
        for (let row of paymentTable.rows) {
            const amount = parseFloat(row.cells[3].textContent); // Get from displayed cell
            if (isNaN(amount)) {
                throw new Error('Invalid amount found in payment table');
            }
            total += amount;
        }

        const totalElement = document.getElementById('total-amount');
        if (!totalElement) {
            throw new Error('Total amount element not found');
        }

        totalElement.textContent = total.toFixed(2);
    } catch (error) {
        console.error('Error updating total amount:', error);
    }
}