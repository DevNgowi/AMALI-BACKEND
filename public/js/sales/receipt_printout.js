document.getElementById('print-checkout-button').addEventListener('click', async () => {
    try {
        console.log("Fetching company details...");

        const companyResponse = await fetch('/point_of_sale/pos/company_details/');
        const companyData = await companyResponse.json();
        
        console.log("Company details fetched:", companyData);

        const { company_name, phone, email, tin_no } = companyData.data;

        const totalAmount = document.getElementById('checkout-total-amount').textContent;
        const tip = document.getElementById('tip').value || '0.00';
        const discount = document.getElementById('discount').value || '0.00';
        const groundTotal = document.getElementById('ground-total-amount').textContent;
        const invoiceNumber = document.querySelector('.checkout-header .col-md-6 span').textContent.split(': ')[1];

        const paymentTypeSelect = document.getElementById('payment-type-select');
        const payment_method = paymentTypeSelect.options[paymentTypeSelect.selectedIndex].text;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Create FormData object
        const formData = new FormData();
        formData.append('company_name', company_name);
        formData.append('phone', phone);
        formData.append('email', email);
        formData.append('tin_no', tin_no);
        formData.append('invoiceNumber', invoiceNumber);
        formData.append('date', new Date().toLocaleString());
        formData.append('order_type', 'Cash');
        formData.append('customer', 'Walk-in customer');
        formData.append('payment_method', payment_method);
        formData.append('totalAmount', totalAmount);
        formData.append('tip', tip);
        formData.append('discount', discount);
        formData.append('groundTotal', groundTotal);
        formData.append('_token', csrfToken); // CSRF token

        // Append receipt items
        getReceiptItems().forEach((item, index) => {
            formData.append(`items[${index}][item]`, item.item);
            formData.append(`items[${index}][price]`, item.price);
            formData.append(`items[${index}][qty]`, item.qty);
            formData.append(`items[${index}][total]`, item.total);
        });

        console.log("Sending receipt data to backend:", formData);

        const response = await fetch('/point_of_sale/pos/print_receipt', {
            method: 'POST',
            body: formData // No need to set headers; browser does it automatically
        });

        console.log("Raw response received:", response);
        
        const responseText = await response.text();
        console.log("Response Text:", responseText);

        try {
            const result = JSON.parse(responseText);
            console.log("Parsed JSON response:", result);
            
            if (result.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Receipt sent to the printer.',
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });
            } else {
                throw new Error(result.message || 'Failed to print receipt.');
            }
        } catch (jsonError) {
            console.error("JSON Parse Error:", jsonError);
            console.log("Raw Response (Not JSON):", responseText);
            
            Swal.fire({
                title: 'Error!',
                text: "Sorry Connect the Printer first then print the order.",
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    } catch (error) {
        console.error('Error:', error);

        Swal.fire({
            title: 'Error!',
            text: error.message,
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    }
});

// Function to get receipt items
function getReceiptItems() {
    console.log("Fetching receipt items...");
    
    const rows = document.querySelectorAll('#payment-table tbody tr');
    let items = [];

    rows.forEach(row => {
        const cells = row.cells;
        const item = cells[0].textContent;
        const price = cells[3].textContent;
        const qty = cells[2].textContent;
        const total = (parseFloat(price) * parseFloat(qty)).toFixed(2);

        items.push({ item, price, qty, total });
    });

    console.log("Receipt items:", items);
    return items;
}