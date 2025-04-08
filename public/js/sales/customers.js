document.addEventListener("DOMContentLoaded", function () {
    const customerTypeSelect = document.getElementById("customer_type_id");
    const registeredCustomerGroup = document.getElementById("registeredCustomerGroup");

    customerTypeSelect.addEventListener("change", function () {
        const selectedOption = customerTypeSelect.options[customerTypeSelect.selectedIndex].text.toLowerCase();

        if (selectedOption.includes("registered")) {
            registeredCustomerGroup.style.display = "block";
        } else {
            registeredCustomerGroup.style.display = "none";
        }
    });
});