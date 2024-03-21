<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component title="Leases">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-component title="Costomer Name" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.costumer_name">
                                </input-component>
                                <input-component title="Car Model" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.car_model_name">
                                </input-component>
                                <input-component title="Car Plate" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.car_plate">
                                </input-component>
                                <input-component title="Initial Date" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.initial_date">
                                </input-component>
                                <input-component title="Final Date" id="finalDate">
                                    <input type="date" class="form-control" id="finalDate" v-model="filters.final_date">
                                </input-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm me-md-2" @click="searchLease()">Search</button>
                    </template>
                </card-component>
                <div class="card">
                    <div class="card-header">Lease List</div>
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
                            :editButton="{
                                visible: true,
                                toggle: 'modal',
                                target: '#editBrandModal'
                            }"
                            :tableHeaders="{
                                id: { title: 'id', type: 'text' },
                                costumer_name: { title: 'Costumer', type: 'text' },
                                car_model: { title: 'Car Model', type: 'text'},
                                car_plate: { title: 'Plate', type: 'text'},
                                lease_date: { title: 'Leased at', type: 'text'},
                            }">
                        </table-component>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <paginate-component class="me-auto">
                            <ul class="pagination">
                                <li v-for="(link, key) in leases.links" :key="key" class="page-item">
                                    <a :class="{ 'page-link': true, 'active': link.active }" href="#" @click="pagination(link)" v-html="link.label"></a>
                                </li>
                            </ul>
                        </paginate-component>
                    </div>
                </div>
            </div>
        </div>
        <!-- Show Modal -->
        <modal-component id="showBrandModal" title="Show Brand">
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
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </template>
        </modal-component>
         <!-- Delete Modal -->
         <modal-component id="deleteBrandModal" title="Delete Brand">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :details="returnDetails" v-if="$store.state.transaction.status == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :details="returnDetails" v-if="$store.state.transaction.status == 'error'" type="danger"></alert-component>
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
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteLease()">Delete</button>
            </template>
        </modal-component>

        <!-- Edit Modal -->
        <!-- <modal-component id="editBrandModal" title="Edit Brand">
        <template v-slot:alerts>
            <alert-component :title="'Success'" v-if="this.$store.state.transaction.status == 'success'"  type="success"></alert-component>
            <alert-component :title="'Error'" v-if="this.$store.state.transaction.status == 'error'" type="danger"></alert-component>
        </template>
        <template v-slot:content>
            <div class="form-group">
                <input-component title="New Brand Name" id="inputEditBrandName" >
                    <input type="text" class="form-control" id="inputEditBrandName" v-model="$store.state.item.name">
                </input-component>
                <input-component title="New Image" id="inputEditBrandImage">
                    <input type="file" class="form-control" id="inputEditBrandImage" @change="loadImage($event)">
                </input-component>
            </div>
        </template>
        <template v-slot:modal-footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="updateBrand()">Update</button>
        </template>
        </modal-component> -->
    </div>
</template>
<script>
import { storeKey } from 'vuex';

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
                baseUrl: 'http://localhost/api/lease',
                returnStatus: '',
                leases: [],
                filters: { 
                    costumer_name: '',
                    car_model_name: '',
                    car_plate: '',
                    initial_date: '',
                    final_date: ''
                },
                paginationUrl: '',
                filterUrl: ''
            }
        },
        methods:
            {
                loadList() {
                    let url = this.baseUrl + '?' + this.paginationUrl + this.filterUrl + '&paginate=2';
                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.get(url, config)
                    .then(response => {
                        this.leases = response.data;
                    })
                    .catch(errors => {
                        console.log(errors);
                    })
                },
                pagination(link){
                    this.paginationUrl = link.url.split('?')[1];
                    this.loadList();
                },

                searchLease(){
                    let filter = '';
                    for(let key in this.filters){
                        if(this.filters[key] != ''){
                            filter += `&${key}=ilike:%${this.filters[key]}%`                            
                        }
                    }
                    if(filter != ''){
                        this.paginationUrl = 'page=1'
                        this.filterUrl = filter;
                    } else {
                        this.filterUrl = '';
                    }
                    this.loadList();
                },
                deleteLease(){
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
                        this.loadList();
                    })
                    .catch(errors => {
                        this.$store.state.transaction.status = 'error'
                        this.$store.state.transaction.message = errors
                    })
                },
                // updateBrand(){
                //     let url = this.baseUrl + '/' + this.$store.state.item.id

                //     let formData = new FormData();
                //     formData.append('_method', 'patch');
                //     if(this.imageFile[0]){
                //         formData.append('image', this.imageFile[0]);
                //     }
                //     formData.append('name', this.$store.state.item.name);
                //     let config = {
                //         headers: {
                //             'Content-Type': 'multipart/form-data',
                //             'Accept': 'application/json',
                //             'Authorization': this.token
                //         }
                //     }
                //     axios.post(url, formData, config)
                //     .then(response => {
                //         this.loadList();
                //     })
                //     .catch(errors => {
                //         console.log(errors);
                //         this.$store.state.transaction.status = 'error'
                //         this.$store.state.transaction.message = errors.response.data.errors
                //     })
                // }
            },
            mounted() {
                this.loadList();
            }
        }
</script>