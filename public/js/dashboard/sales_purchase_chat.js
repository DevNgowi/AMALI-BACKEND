$(document).ready(function () {
    let salesAndPurchasesChart = null; // Global variable to store chart instance
    
    function fetchSalesAndPurchasesChartData() {
        const filterValue = $('#filter').val(); 
        $.ajax({
            url: '/dashboard/sales_and_purchases_chart',
            method: 'GET',
            data: { filter: filterValue }, 
            success: function(data) {
                const labels = data.map(item => item.date);
                const sales = data.map(item => item.total_sales || 0);
                const purchases = data.map(item => item.total_purchases || 0);
                renderSalesAndPurchasesChart(labels, sales, purchases);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching sales and purchases chart data:', error);
            }
        });
    }

    function renderSalesAndPurchasesChart(labels, sales, purchases) {
        const ctx = document.getElementById('salesAndPurchasesChart').getContext('2d');
        
        // Destroy existing chart if it exists
        if (salesAndPurchasesChart) {
            salesAndPurchasesChart.destroy();
        }
        
        salesAndPurchasesChart = new Chart(ctx, {
            type: 'bar', // Change from 'line' to 'bar'
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Sales',
                        data: sales,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Total Purchases',
                        data: purchases,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    fetchSalesAndPurchasesChartData();
});