
$(document).ready(function() {
    function fetchPurchasesData(filter) {
        $.ajax({
            url: '/dashboard/purchase_counter',
            method: 'GET',
            data: {
                filter: filter
            },
            success: function(response) {
                $('#purchases-counter').text(
                    `Tsh ${parseFloat(response.total_purchases || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`
                    );
            },
            error: function(xhr, status, error) {
                console.error('Error fetching purchases data:', error);
            }
        });

    }

    const initialFilter = 'today';
    fetchPurchasesData(initialFilter);

    $('#filter').on('change', function() {
        const filterValue = $(this).val();
        fetchPurchasesData(filterValue);
    });

});