<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component title="Brands">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-component title="ID" id="inputId">
                                    <input type="number" class="form-control" id="inputId" v-model="filters.id">
                                </input-component>
                            </div>
                            <div class="col mb-3">
                                <input-component title="Name" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.name">
                                </input-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm me-md-2" @click="searchBrand()">Search</button>
                    </template>
                </card-component>
                <div class="card">
                    <div class="card-header">Brand List</div>
                    <div class="card-body">
                        <table-component 
                            :data="brands.data" 
                            :deleteButton="{
                                visible: true,
                                toggle: 'modal',
                                target: '#deleteBrandModal'
                            }"
                            :showButton="{
                                visible: true,
                                toggle: 'modal',
                                target: '#showBrandModal'
                            }"
                            :editButton="true"
                            :tableHeaders="{
                                id: { title: 'id', type: 'text' },
                                name: { title: 'name', type: 'text' },
                                image: { title: 'image', type: 'image' },
                            }">
                        </table-component>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <paginate-component class="me-auto">
                            <ul class="pagination">
                                <li v-for="(link, key) in brands.links" :key="key" class="page-item">
                                    <a :class="{ 'page-link': true, 'active': link.active }" href="#" @click="pagination(link)" v-html="link.label"></a>
                                </li>
                            </ul>
                        </paginate-component>
                        <div style="margin-top: 0.5%;" class="col-1">
                            <button type="submit" class="btn btn-primary btn-sm" height="15px" data-bs-toggle="modal" data-bs-target="#brandModal">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Modal -->
        <modal-component id="brandModal" title="New Brand">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :details="returnDetails" v-if="returnStatus == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :details="returnDetails" v-if="returnStatus == 'error'" type="danger"></alert-component>
            </template>
            <template v-slot:content>
                <div class="form-group">
                    <input-component title="Brand Name" id="inputNewBrandName" >
                        <input type="text" class="form-control" id="inputNewBrandName" v-model="brandName">
                    </input-component>
                    <input-component title="Image" id="inputNewBrandImage">
                        <input type="file" class="form-control" id="inputNewBrandImage" @change="loadImage($event)">
                    </input-component>
                </div>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="saveImage()">Save changes</button>
            </template>
        </modal-component>

        <!-- Show Modal -->
        <modal-component id="showBrandModal" title="Show Brand">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :details="returnDetails" v-if="returnStatus == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :details="returnDetails" v-if="returnStatus == 'error'" type="danger"></alert-component>
            </template>
            <template v-slot:content>
                <input-component title="ID">
                    <input disabled type="text" :value="$store.state.item.id" class="form-control">
                </input-component>
                <input-component title="Name">
                    <input disabled type="text" :value="$store.state.item.name" class="form-control" >
                </input-component>
                <input-component title="Image" >
                    <img v-if="$store.state.item.image" :src="'/storage/' + $store.state.item.image" alt="Image" class="img-fluid" style="margin: 0 0 0 1%;" >
                </input-component>
                <input-component title="Name">
                    <input disabled type="text" :value="$store.state.item.name" class="form-control" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </template>
        </modal-component>
         <!-- Delete Modal -->
         <modal-component id="deleteBrandModal" title="Delete Brand">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :details="returnDetails" v-if="returnStatus == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :details="returnDetails" v-if="returnStatus == 'error'" type="danger"></alert-component>
            </template>
            <template v-slot:content>
                <input-component title="ID">
                    <input disabled type="text" :value="$store.state.item.id" class="form-control">
                </input-component>
                <input-component title="Name">
                    <input disabled type="text" :value="$store.state.item.name" class="form-control" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteBrand()">Delete</button>
            </template>
        </modal-component>
    </div>
</template>
<script>
    export default {
        computed: {
            token() {
                let token = document.cookie.split(';').find(value => {
                    return value.includes('token=');
                });

                token  = token.split('=')[1]; // Split the token of the token=

                token = 'Bearer '+ token;
                return token;
            }
        },
        data() {
            return {
                baseUrl: 'http://localhost/api/brand',
                brandName: '',
                imageFile: [],
                returnStatus: '',
                returnDetails: '',
                brands: [],
                filters: { id: '', name: ''},
                paginationUrl: '',
                filterUrl: ''
            }
        },
        methods:
            {
                loadImage(e) {
                    this.imageFile = Array.from(e.target.files);
                },

                saveImage()
                {

                    let formData = new FormData();
                    formData.append('name', this.brandName);
                    formData.append('image', this.imageFile[0]);
                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.post(this.baseUrl, formData, config)
                    .then(response => {
                        this.returnStatus = 'success';
                        this.returnDetails = response
                        this.loadList();
                    })
                    .catch(errors => {
                        console.log('Erro na requisição:', errors);
                        this.returnStatus = 'error';
                        this.returnDetails = errors.response;
                    })
                },
                loadList() {
                    let url = this.baseUrl + '?' + this.paginationUrl + this.filterUrl;
                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.get(url, config)
                    .then(response => {
                        this.brands = response.data;
                    })
                    .catch(errors => {
                        console.log(errors);
                    })
                },
                pagination(link){
                    this.paginationUrl = link.url.split('?')[1];
                    this.loadList();
                },

                searchBrand(){
                    let filter = '';
                    for(let key in this.filters){
                        if(this.filters[key] != ''){
                            if(filter != ''){
                                filter += ';';
                            }     
                            filter += `${key}:like:%${this.filters[key]}%`                            
                        }
                    }
                    if(filter != ''){
                        this.paginationUrl = 'page=1'
                        this.filterUrl = '&filter='+filter;
                    } else {
                        this.filterUrl = '';
                    }
                    this.loadList();
                },
                deleteBrand(){
                    let formData = new FormData();
                    formData.append('_method', 'delete');
                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    let url = this.baseUrl + '/' +this.$store.state.item.id
                    axios.post(url, formData, config)
                    .then(response => {
                        console.log(response);
                        this.loadList();
                    })
                    .catch(errors => {
                        console.log(errors);
                    })
                }
            },
            mounted() {
                this.loadList();
            }
        }
</script>