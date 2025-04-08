
$(document).ready(function() {
    function fetchSalesData(filter) {
        $.ajax({
            url: '/dashboard/sale_counter',
            method: 'GET',
            data: {
                filter: filter
            },
            success: function(response) {
                $('#sales-counter').text(
                    `Tsh ${parseFloat(response.total_sales || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching sales data:', error);
            }
        });
    }

   
    const initialFilter = 'today';
    fetchSalesData(initialFilter);

    $('#filter').on('change', function() {
        const filterValue = $(this).val();
        fetchSalesData(filterValue);
    });

});