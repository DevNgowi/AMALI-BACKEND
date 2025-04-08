function populateEditModal(store) {
    // Set the action URL for the edit form
    document.getElementById('editStoreForm').action = `/stores/update/${store.id}`;

    // Populate form fields with store data
    document.getElementById('edit_name').value = store.name; 
    document.getElementById('edit_location').value = store.location;
    document.getElementById('edit_manager').value = store.manager_id;
}

