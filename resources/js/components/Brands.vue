<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component title="Brands">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-component title="ID" id="inputId">
                                    <input type="number" class="form-control" id="inputId">
                                </input-component>
                            </div>
                            <div class="col mb-3">
                                <input-component title="Name" id="inputName">
                                    <input type="text" class="form-control" id="inputName">
                                </input-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm me-md-2">Search</button>
                    </template>
                </card-component>
                <div class="card">
                    <div class="card-header">Brand List</div>
                    <div class="card-body">
                        <table-component></table-component>
                    </div>
                    <div class="card-footer d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit"  data-bs-toggle="modal" data-bs-target="#brandModal" class="btn btn-primary btn-sm me-md-2">Add</button>
                    </div>
                </div>
            </div>
        </div>
<!-- Modal -->
        <modal-component id="brandModal" title="New Brand">
            <template v-slot:content>
                <div class="form-group">
                    <input-component title="Brand Name" id="inputNewBrandName">
                        <input type="text" class="form-control" id="inputNewBrandName">
                    </input-component>
                    <input-component title="Image" id="inputNewBrandImage">
                        <input type="file" class="form-control" id="inputNewBrandImage">
                    </input-component>
                </div>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </template>
        </modal-component>
    </div>
</template>