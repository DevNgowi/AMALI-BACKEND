function showConfirmationModal(url, successMessage, errorMessage, itemName) {
    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    Swal.fire({
        title: 'Are you sure?',
        text: successMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'POST', // Changed to POST
                data: {
                    _method: 'DELETE', // This is crucial
                    _token: token
                },
                success: function(response) {
                    Swal.fire('Deleted!', response.message || successMessage, 'success')
                        .then(() => {
                            location.reload();
                        });
                },
                error: function(xhr) {
                    console.error('Delete error:', xhr);
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || errorMessage || `${itemName} could not be deleted.`,
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}