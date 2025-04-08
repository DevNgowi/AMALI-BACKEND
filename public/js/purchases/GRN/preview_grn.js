// Add this at the top of your JavaScript file
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function verifyGRN(id) {
    Swal.fire({
        title: 'Verify Good Receive Note',
        text: 'Are you sure you want to verify this GRN?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Verify GRN!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/grn/' + id + '/verify',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire('Verified!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while verifying the GRN.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}

function acceptGRN(id) {
    Swal.fire({
        title: 'Accept Good Receive Note',
        text: 'Are you sure you want to accept this GRN?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Accept GRN!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/grn/' + id + '/accept',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire('Accepted!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while accepting the GRN.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}

function rejectGRN(id) {
    Swal.fire({
        title: 'Reject Good Receive Note',
        text: 'Are you sure you want to reject this GRN?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reject GRN!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/grn/' + id + '/reject',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire('Rejected!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while rejecting the GRN.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}

function completeGRN(id) {
    Swal.fire({
        title: 'Complete Good Receive Note',
        text: 'Are you sure you want to mark this GRN as complete?',
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#6c757d',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Complete GRN!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/grn/' + id + '/complete',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire('Completed!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while completing the GRN.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}

function reopenGRN(id) {
    Swal.fire({
        title: 'Reopen Good Receive Note',
        text: 'Are you sure you want to reopen this GRN?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reopen GRN!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/grn/' + id + '/reopen',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire('Reopened!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while reopening the GRN.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}

function cancelGRN(id) {
    Swal.fire({
        title: 'Cancel Good Receive Note',
        text: 'Are you sure you want to cancel this GRN?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Cancel GRN!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/purchases/grn/' + id + '/cancel',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    Swal.fire('Cancelled!', response.message, 'success').then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.message || 'An error occurred while cancelling the GRN.';
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
    });
}