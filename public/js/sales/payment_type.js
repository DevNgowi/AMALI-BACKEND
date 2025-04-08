$(document).ready(function() {
    // --- Selectors ---
    let $paymentSelect = $('#payment-type-select');
    let $paymentContainer = $('.payment_id');

    // --- Functions ---

    /**
     * Sets the default payment method.
     *
     * Prioritizes:
     * 1. Stored payment in localStorage
     * 2. "Cash" option if available
     * 3. First available option (excluding placeholder)
     */
    function setDefaultPayment() {
        const cashValue = "Cash"; // Define the "Cash" value (adjust if your IDs differ)
        const storedPayment = localStorage.getItem('selectedPayment'); // Retrieve stored payment

        // If stored payment exists and is a valid option, set it
        if (storedPayment && $paymentSelect.find(`option[value="${storedPayment}"]`).length) {
            $paymentSelect.val(storedPayment).trigger('change');
        }
        // Fallback to "Cash" or first available option
        else {
            let $cashOption = $paymentSelect.find(`option[value="${cashValue}"]`);
            if ($cashOption.length) {
                $paymentSelect.val(cashValue).trigger('change');
            } else if ($paymentSelect.find('option').length > 1) {
                let firstPayment = $paymentSelect.find('option:eq(1)').val(); // Skip placeholder
                $paymentSelect.val(firstPayment).trigger('change');
            }
        }
    }

    /**
     * Loads payment options from the API and populates the payment select dropdown.
     *
     * @param {function} callback - Optional callback function to execute after loading options.
     */
    function loadPaymentOptions(callback) {
        $.ajax({
            url: '/api/payments',
            method: 'GET',
            success: function(response) {
                $paymentSelect.empty();
                $paymentSelect.append('<option value="">Select Payment</option>');

                response.forEach(payment => {
                    $paymentSelect.append(
                        `<option value="${payment.id}">${payment.short_code}</option>`
                    );
                });

                if (typeof callback === 'function') {
                    callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load payment options:', error);
                if (typeof callback === 'function') {
                    callback();
                }
            }
        });
    }

    // --- Event Handlers ---

    // Load payment options and then set the default payment on document ready
    loadPaymentOptions(function() {
        setDefaultPayment();
    });

    // Handle payment selection change event
    $paymentSelect.change(function() {
        var paymentId = $(this).val();
        if (paymentId) {
            localStorage.setItem('selectedPayment', paymentId);
        }
        fetchAndSetPayments(paymentId); // Assuming this function is defined elsewhere
    });

    // Checkout button click event handler
    $('#print-checkout-button').click(function(event) {
        event.preventDefault();

        var paymentId = $paymentSelect.val();

        // Validate payment selection
        if (!paymentId || paymentId === "") {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select a valid payment method.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            $paymentSelect.focus();
            return;
        }

        // Proceed with checkout logic (for demonstration)
        console.log('Proceeding with checkout...', { paymentId });
    });

    // Customer type change event handler (for registered customer group visibility)
    $('#customer_type_id').change(function() {
        const selectedType = $(this).val();
        $('#registeredCustomerGroup').toggle(selectedType === 'registered');
    });
});

