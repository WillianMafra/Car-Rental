<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component title="Leases">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-component title="Initial Date" id="inputName">
                                    <input type="date" class="form-control" id="inputName" v-model="filters.start_date">
                                </input-component>
                                <input-component title="Final Date" id="finalDate">
                                    <input :min="filters.start_date" type="date" class="form-control" id="finalDate" v-model="filters.expected_end_date">
                                </input-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm me-md-2" @click="search()">Search</button>
                    </template>
                </card-component>
                <div  class="card fw-bold">
                    <div class="card-header">My Leases</div>
                    <div class="card-body">
                        <table-component 
                            :data="leases.data" 
                            :deleteButton="{
                                visible: user.role_id == 1 ? true : false,
                                toggle: 'modal',
                                target: '#deleteModal'
                            }"
                            :showButton="{
                                visible: true,
                                toggle: 'modal',
                                target: '#showModal'
                            }"
                            :editButton="''"
                            :tableHeaders="{
                                car_model_name: { title: 'Model', type: 'text', path: 'car.car_model.name' },
                                plate: { title: 'plate', type: 'text', path: 'car.plate' },
                                star_date: { title: 'Leased At', type: 'date', 'path': 'start_date' },
                                expected_end_date: { title: 'End Date', type: 'date' },
                            }">
                        </table-component>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <paginate-component class="me-auto">
                            <ul class="pagination">
                                <li v-for="(link, key) in leases.links" :key="key" class="page-item">
                                    <a :class="{ 'page-link': true, 'active': link.active }" href="#" @click.prevent="pagination(link)" v-html="link.label"></a>
                                </li>
                            </ul>
                        </paginate-component>
                        <div style="margin-top: 0.5%;" class="col-1">
                            <button type="submit" class="btn btn-primary btn-sm" height="15px" data-bs-toggle="modal" data-bs-target="#addModal" @click="clearTransaction()">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Show Modal -->
        <modal-component id="showModal" title="Show Lease">
            <template v-slot:content>
                <input-component v-if="$store.state.item.car" class=" fw-bold mb-2 mt-2" title="Car Model">
                    <input disabled type="text" :value="$store.state.item.car.car_model.name" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car" class="  fw-bold" title="Doors">
                    <input disabled type="text" :value="$store.state.item.car.car_model.doors" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car" class=" fw-bold mb-2 mt-2" title="Seats">
                    <input disabled type="text" :value="$store.state.item.car.car_model.seats" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car" class=" fw-bold mb-2 mt-2" title="Daily Rate">
                    <input disabled type="text" :value="$store.state.item.car.daily_rate" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car" class=" fw-bold mb-2 mt-2" title="Leased at">
                    <input disabled type="text" :value="$store.state.item.start_date" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car" class=" fw-bold mb-2 mt-2" title="End Date">
                    <input disabled type="text" :value="$store.state.item.actual_end_date" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car" title="Total">
                    <input disabled  type="number" :value="calculateDateDifference * $store.state.item.car.daily_rate" class="form-control" >
                </input-component>
                <true-false-icons-component v-if="$store.state.item.car_model" class="text-center" :style="'font-size: 18px'" :width="20" :height="20" :title="'Airbag'" :condition="$store.state.item.car_model.air_bag"></true-false-icons-component>
                <true-false-icons-component v-if="$store.state.item.car_model" class="text-center" :style="'font-size: 18px'" :width="20" :height="20" :title="'ABS'" :condition="$store.state.item.car_model.abs"></true-false-icons-component>
                <input-component v-if="$store.state.item.image" class="text-center">
                    <img  :src="'/storage/' + $store.state.item.car_model.image" alt="Image" class="img-fluid" style="margin: 0 0 0 1%;" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </template>
        </modal-component>
    </div>
</template>
<script>
import { extractIdentifiers } from '@vue/compiler-core';
import { storeKey } from 'vuex';

export default {
        props: ['user'],
        computed: {
            token() {
                let token = document.cookie.split(';').find(value => {
                    return value.includes('token=');
                });

                token  = token.split('=')[1]; // Split the token of the token=

                token = 'Bearer '+ token;
                return token;
            },
            calculateDateDifference() {
            if (!this.$store.state.item.start_date || !this.$store.state.item.expected_end_date) return '';
            
            const startDate = new Date(this.$store.state.item.start_date);
            const endDate = new Date(this.$store.state.item.expected_end_date);
            
            const differenceMs = endDate - startDate;
            
            const differenceDays = Math.floor(differenceMs / (1000 * 60 * 60 * 24));
            
            return differenceDays;
            }
        },
        watch: {
            'leaseData.start_date'(newStartDate) {
                if (this.filters.expected_end_date && new Date(this.filters.expected_end_date) < new Date(newStartDate)) {
                    this.filters.expected_end_date = newStartDate;
                }
            }
        },
        data() {
            return {
                baseUrl: 'http://localhost/api',
                returnStatus: '',
                filters: { 
                    start_date: '',
                    final_date: '',
                },
                paginationUrl: '',
                filterUrl: '',
                leases: [],
                car_models: [],
                leaseData: {
                    start_date: '',
                    expected_end_date: ''
                }
            }
        },
        methods:
            {
                loadList() {
                    let url = this.baseUrl + '/all-my-leases?' + this.paginationUrl + this.filterUrl + '&paginate=5 ';
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

                search(){
                    let filter = '';
                    for(let key in this.filters){
                        if(this.filters[key] !== ''){
                            if(key == 'start_date') {
                                filter += `&${key}=>=:${this.filters[key]}`                            
                            } else {
                                filter += `&${key}=<=:${this.filters[key]}`                            
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
                clearTransaction(){
                    this.$store.state.transaction.status = ''
                    this.$store.state.transaction.message = ''
                    this.returnStatus = ''
                },
                leaseCar(){
                    let url = this.baseUrl + '/lease';
                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    let formData = new FormData();
                    formData.append('id', this.$store.state.item.id);
                    formData.append('start_date', this.leaseData.start_date);
                    formData.append('expected_end_date',this.leaseData.expected_end_date);
                    
                    axios.post(url, formData, config)
                    .then(response => {
                        console.log(response)
                        this.loadList();
                    })
                    .catch(error => {
                        console.log(error)
                    })
                }
            },
            mounted() {
                this.loadList();
            }
        }
</script>