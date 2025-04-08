const searchInput = document.querySelector('input[name="search_item_group"]');
const itemGroupList = document.querySelector('.item-group-list');
const noRecordMessage = document.querySelector('.no-record');
const itemCategoryList = document.querySelector('#item-category-list');

// Search input listener (unchanged)
searchInput.addEventListener('input', function () {
  const query = this.value.trim();
  console.log('Search query entered:', query);

  fetch(`/point_of_sale/pos/item_groups?search=${query}`)
    .then(response => {
      console.log('Response status:', response.status);
      return response.json();
    })
    .then(data => {
      console.log('Data received from server:', data);
      itemGroupList.innerHTML = '';

      if (data.length > 0) {
        data.forEach(itemGroup => {
          const li = document.createElement('li');
          li.className = 'item-group-list-item';
          li.innerHTML = `<a href="#" onclick="loadItemGroup('${itemGroup.id}')">${itemGroup.name}</a>`;
          itemGroupList.appendChild(li);
        });
        noRecordMessage.style.display = 'none';
      } else {
        noRecordMessage.style.display = 'block';
      }
    })
    .catch(error => {
      console.error('Error fetching item groups:', error);
      noRecordMessage.style.display = 'block';
      noRecordMessage.textContent = 'Failed to fetch item groups.';
    });
});

document.addEventListener('DOMContentLoaded', () => {
  if (firstItemGroupId) {
    loadItemGroup(firstItemGroupId);
  }
});

// Search Items (unchanged)
function searchItems() {
  const searchInput = document.getElementById('search-items-input');
  const filter = searchInput.value.toLowerCase().trim();
  const itemsContainer = document.getElementById('items-container');
  const itemCards = itemsContainer.querySelectorAll('.item-card');
  const noItemsMessage = itemsContainer.querySelector('.no-items-message');

  if (filter === '') {
    itemCards.forEach(itemCard => {
      itemCard.style.display = 'block';
    });
    if (noItemsMessage) {
      noItemsMessage.remove();
    }
    return;
  }

  let found = false;
  itemCards.forEach(itemCard => {
    const itemName = itemCard.querySelector('.item-name').textContent.toLowerCase();
    if (itemName.includes(filter)) {
      itemCard.style.display = 'block';
      found = true;
    } else {
      itemCard.style.display = 'none';
    }
  });

  if (!found) {
    if (!noItemsMessage) {
      const message = document.createElement('p');
      message.className = 'no-items-message';
      message.textContent = 'No items match your search.';
      itemsContainer.appendChild(message);
    }
  } else if (noItemsMessage) {
    noItemsMessage.remove();
  }
}

// Load Item Group and Auto-Load First Category's Items
function loadItemGroup(groupId) {
  const itemsContainer = document.getElementById('items-container');

  // Show loading state for items
  itemsContainer.innerHTML = '<p>Loading items...</p>';

  // Fetch associated item categories for the group
  fetch(`/point_of_sale/pos/item_categories?item_group_id=${groupId}`)
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      itemCategoryList.innerHTML = ''; // Clear previous categories

      if (data.length > 0) {
        data.forEach(itemCategory => {
          const li = document.createElement('li');
          li.className = 'item-category-list-item';
          li.innerHTML = `<a href="#" onclick="loadItemcategory('${itemCategory.id}')">${itemCategory.name.toUpperCase()}</a>`;
          itemCategoryList.appendChild(li);
        });

        // Automatically load items for the first category
        const firstCategoryId = data[0].id; // Get the first category's ID
        loadItemcategory(firstCategoryId);
      } else {
        itemCategoryList.innerHTML = '<li>No categories found.</li>';
        itemsContainer.innerHTML = '<p>No categories available for this group.</p>';
      }
    })
    .catch(error => {
      console.error('Error loading item categories:', error);
      itemCategoryList.innerHTML = '<li>Error loading categories.</li>';
      itemsContainer.innerHTML = '<p>Error loading items.</p>';
    });
}

// Load Items by Category
function loadItemcategory(categoryId) {
  const itemsContainer = document.getElementById('items-container');

  itemsContainer.innerHTML = '<p>Loading items...</p>';

  fetch(`/point_of_sale/pos/items?item_category_id=${categoryId}`)
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.error) {
        itemsContainer.innerHTML = `<p>${data.error}</p>`;
        return;
      }

      if (data.length > 0) {
        let itemsHtml = '<div class="flex-container">';
        data.forEach(item => {
          const imageUrl = item.image_url
            ? `/storage${item.image_url}` // Adjust path if needed
            : defaultImageUrl;

          itemsHtml += `
            <div class="item-card" data-item-id="${item.id}">
              <img src="${imageUrl}" 
                   alt="${item.item_name}" 
                   class="item-image"
                   onerror="this.src='${defaultImageUrl}'">
              <div class="item-details">
                <h5 class="item-name">${item.item_name.toUpperCase()}</h5>
                <p class="item-info">(${item.item_price}) - ${item.stock_quantity} ${item.item_unit || 'N/A'}</p>
              </div>
            </div>`;
        });
        itemsHtml += '</div>';
        itemsContainer.innerHTML = itemsHtml;
      } else {
        itemsContainer.innerHTML = '<p>No items found for this category.</p>';
      }
    })
    .catch(error => {
      console.error('Error loading items:', error);
      itemsContainer.innerHTML = '<p>Error loading items. Please try again.</p>';
    });
}