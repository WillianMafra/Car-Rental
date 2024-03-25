<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component title="Leases">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-component title="Costumer Name" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.costumer_name">
                                </input-component>
                                <select-component :title="'Car Model'" v-model="filters.car_model_id" :data="carModels"></select-component>
                                <input-component title="Car Plate" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.car_plate">
                                </input-component>
                                <input-component title="Initial Date" id="inputName">
                                    <input type="date" class="form-control" id="inputName" v-model="filters.initial_date">
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
                            :data="leases.data" 
                            :deleteButton="{
                                visible: true,
                                toggle: 'modal',
                                target: '#deleteModal'
                            }"
                            :showButton="{
                                visible: true,
                                toggle: 'modal',
                                target: '#showModal'
                            }"
                            :editButton="{
                                visible: true,
                                toggle: 'modal',
                                target: '#editModal'
                            }"
                            :tableHeaders="{
                                id: { title: 'id', type: 'text' },
                                costumer_name: { title: 'Costumer', type: 'text', 'path': 'user.name' },
                                car_model: { title: 'Car Model', type: 'text', 'path': 'car.car_model.name'},
                                car_plate: { title: 'Plate', type: 'text', 'path': 'car.plate'},
                                lease_date: { title: 'Leased at', type: 'text', 'path': 'start_date'},
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
        <modal-component v-if="$store.state.item.user" id="showModal" title="Show Lease">
            <template v-slot:content>
                <input-component title="ID">
                    <input disabled type="text" :value="$store.state.item.id" class="form-control">
                </input-component>
                <input-component title="Costumer Name">
                    <input disabled type="text" :value="$store.state.item.user.name" class="form-control" >
                </input-component>
                <input-component title="Car Model">
                    <input disabled type="text" :value="$store.state.item.car.car_model.name" class="form-control" >
                </input-component>
                <input-component title="Plate">
                    <input disabled type="text" :value="$store.state.item.car.plate" class="form-control" >
                </input-component>
                <input-component title="Initial KM">
                    <input disabled type="text" :value="$store.state.item.initial_km" class="form-control" >
                </input-component>
                <input-component title="Final KM">
                    <input disabled type="text" :value="$store.state.item.final_km" class="form-control" >
                </input-component>
                <input-component title="Leased at">
                    <input disabled type="datetime-local" :value="$store.state.item.start_date" class="form-control" >
                </input-component>
                <input-component title="Expected End Date">
                    <input disabled type="datetime-local" :value="$store.state.item.expected_end_date" class="form-control" >
                </input-component>
                <input-component title="Actual End Date ">
                    <input disabled type="datetime-local" :value="$store.state.item.actual_end_date" class="form-control" >
                </input-component>
                <input-component title="Daily Rate">
                    <input disabled type="text" :value="$store.state.item.daily_rate" class="form-control" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </template>
        </modal-component>
         <!-- Delete Modal -->
         <modal-component id="deleteModal" title="Delete Lease">
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
                    car_model_id: '',
                    car_plate: '',
                    initial_date: '',
                    final_date: ''
                },
                paginationUrl: '',
                filterUrl: '',
                carModels: []
            }
        },
        methods:
            {
                loadList() {
                    let url = this.baseUrl + '?' + this.paginationUrl + this.filterUrl + '&paginate=5';
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
                            if(key == 'car_model_id'){
                                filter += `&${key}==:${this.filters[key]}`                            
                            } else {
                                filter += `&${key}=ilike:%${this.filters[key]}%`                            
                            }
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
                getCarModels(){
                    let url = 'http://localhost/api/car-model';
                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.get(url, config)
                    .then(response => {
                        this.carModels = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    })
                }
            },
            mounted() {
                this.getCarModels();
                this.loadList();
            }
        }
</script>