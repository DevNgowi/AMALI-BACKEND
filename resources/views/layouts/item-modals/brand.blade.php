 {{-- modal for branding --}}

    <!-- Add Brand Modal -->
    <div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form id="addBrandForm" action="{{ route('store_item_brand') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="brand_description" class="form-label">Description</label>
                            <textarea class="form-control" id="brand_description" name="description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Brand</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
