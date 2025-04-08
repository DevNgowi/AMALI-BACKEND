document.addEventListener('DOMContentLoaded', () => {
    const checkOutButton = document.getElementById('check-out-button');
    const hideCheckoutButton = document.getElementById('hide-checkout-button');
    const paymentDetailsArea = document.getElementById('payment-details-area');
    const checkoutProcess = document.getElementById('checkout-process');
    const totalAmountDisplay = document.getElementById('total-amount'); 
    const tipInput = document.getElementById('tip');
    const discountInput = document.getElementById('discount');
    const groundTotalDisplay = document.getElementById('ground-total-amount');
    const keypadButtons = document.querySelectorAll('.keypad button');
    const clearButton = document.querySelector('.keypad .clear');

    let activeInputField = null; 

    // Show Checkout Process and Hide Payment Details Area
    checkOutButton.addEventListener('click', () => {
        const totalAmount = parseFloat(totalAmountDisplay.textContent.replace(/,/g, '')); // Remove commas before parsing

        // Update the total amount in the checkout process with formatting
        document.getElementById('checkout-total-amount').textContent = formatNumber(totalAmount);

        // Calculate and display the ground total
        updateGroundTotal(totalAmount);

        paymentDetailsArea.style.display = 'none'; // Hide Payment Details
        checkoutProcess.style.display = 'block';   // Show Checkout Process
    });

    // Hide Checkout Process and Show Payment Details Area
    hideCheckoutButton.addEventListener('click', () => {
        checkoutProcess.style.display = 'none';    // Hide Checkout Process
        paymentDetailsArea.style.display = 'block'; // Show Payment Details
    });

    // Event listeners for tip and discount inputs
    tipInput.addEventListener('input', () => {
        const totalAmount = parseFloat(totalAmountDisplay.textContent.replace(/,/g, '')); // Remove commas before parsing
        updateGroundTotal(totalAmount);
    });

    discountInput.addEventListener('input', () => {
        const totalAmount = parseFloat(totalAmountDisplay.textContent.replace(/,/g, '')); // Remove commas before parsing
        updateGroundTotal(totalAmount);
    });

    // Track which input field is focused
    tipInput.addEventListener('focus', () => {
        activeInputField = tipInput;
    });

    discountInput.addEventListener('focus', () => {
        activeInputField = discountInput;
    });

    // Keypad functionality
    keypadButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (!activeInputField) return; // If no input field is focused, do nothing

            const buttonText = button.textContent;
            if (buttonText === 'X') {
                // Handle backspace (remove last character)
                activeInputField.value = activeInputField.value.slice(0, -1);
            } else if (buttonText === 'Clear') {
                // Clear the active input field
                activeInputField.value = '';
            } else {
                // Append the button's text to the active input field
                activeInputField.value += buttonText;
            }

            // Update the ground total
            const totalAmount = parseFloat(totalAmountDisplay.textContent.replace(/,/g, '')); // Remove commas before parsing
            updateGroundTotal(totalAmount);
        });
    });

    // Clear button functionality
    clearButton.addEventListener('click', () => {
        tipInput.value = '';
        discountInput.value = '';
        const totalAmount = parseFloat(totalAmountDisplay.textContent.replace(/,/g, '')); // Remove commas before parsing
        updateGroundTotal(totalAmount);
    });

    // Function to update the ground total
    function updateGroundTotal(totalAmount) {
        const tip = parseFloat(tipInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;

        // Validate discount (should not exceed 50% of total amount)
        if (discount > totalAmount * 0.5) {
            Swal.fire({
                title: 'Invalid Discount',
                text: `Discount cannot exceed 50% of the total amount (${formatNumber(totalAmount * 0.5)})`,
                icon: 'warning',
                confirmButtonColor: '#dc3545',
            });
            discountInput.value = (totalAmount * 0.5).toFixed(2); // Reset discount to maximum allowed
        }

        // Validate tip (should not exceed 20% of total amount)
        if (tip > totalAmount * 0.2) {
            Swal.fire({
                title: 'Invalid Tip',
                text: `Tip cannot exceed 20% of the total amount (${formatNumber(totalAmount * 0.2)})`,
                icon: 'warning',
                confirmButtonColor: '#dc3545',
            });
            tipInput.value = (totalAmount * 0.2).toFixed(2); // Reset tip to maximum allowed
        }

        const groundTotal = totalAmount + tip - discount;

        // Format the ground total with commas
        groundTotalDisplay.textContent = formatNumber(groundTotal);
    }

    // Function to format numbers with commas
    function formatNumber(number) {
        return number.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }
});