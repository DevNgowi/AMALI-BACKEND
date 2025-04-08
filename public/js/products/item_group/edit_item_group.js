function populateEditItemGroupModal(item_group) {
    document.getElementById('editItemGroupForm').action = `/inventory/item_group/update/${item_group.id}`;
    
    document.getElementById('edit_item_group_name').value = item_group.name;
    document.getElementById('edit_category_id').value = item_group.category_id;

}