
$(document).ready(function() {
    function fetchExpensesData(filter) {
        $.ajax({
            url: '/dashboard/expenses',
            method: 'GET',
            data: {
                filter: filter
            },
            success: function(response) {
                $('#expenses-counter').text(
                    `Tsh ${parseFloat(response.total_expenses || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching expenses data:', error);
            }
        });
    }

   
    const initialFilter = 'today';
    fetchExpensesData(initialFilter);

    $('#filter').on('change', function() {
        const filterValue = $(this).val();
        fetchExpensesData(filterValue);
    });

});