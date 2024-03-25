<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component class="fw-bold" title="Cars">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <select-component :id="'car_model_id'" :title="'Car Model'" :data="car_models" v-model="filters.car_model_id"></select-component>
                            </div>
                            <div class="col mb-3">
                                <input-component title="Doors" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.doors">
                                </input-component>
                            </div>
                            <div class="col mb-3">
                                <input-component title="Seats" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.seats">
                                </input-component>
                            </div>
                            <div class="col mb-3">
                                <yes-no-component  v-model="filters.abs" :title="'ABS'"></yes-no-component>
                            </div>
                            <div class="col mb-3">
                                <yes-no-component  v-model="filters.air_bag" :title="'Airbag'"></yes-no-component>
                            </div>
                            <div class="col mb-3">
                                <yes-no-component  v-model="filters.avaliable" :title="'Avaliable'"></yes-no-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm me-md-2" @click="search()">Search</button>
                    </template>
                </card-component>
                <div  class="card fw-bold">
                    <div class="card-header">Car List</div>
                    <div class="card-body">
                        <table-component 
                            :data="cars.data" 
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
                            :editButton="{
                                visible:  user.role_id == 1 ? true : false,
                                toggle: 'modal',
                                target: '#editModal'
                            }"
                            :tableHeaders="{
                                car_model_id: { title: 'Model', type: 'text', path: 'car_model.name' },
                                plate: { title: 'plate', type: 'text' },
                                km: { title: 'km', type: 'number' },
                                avaliable: { title: 'avaliable', type: 'boolean' },
                                image: { title: 'image', type: 'image', path: 'car_model.image', style:'width:100px;' },
                            }">
                        </table-component>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <paginate-component class="me-auto">
                            <ul class="pagination">
                                <li v-for="(link, key) in cars.links" :key="key" class="page-item">
                                    <a :class="{ 'page-link': true, 'active': link.active }" href="#" @click.prevent="pagination(link)" v-html="link.label"></a>
                                </li>
                            </ul>
                        </paginate-component>
                        <div style="margin-top: 0.5%;" class="col-1">
                            <button v-if="user.role_id == 1" type="submit" class="btn btn-primary btn-sm" height="15px" data-bs-toggle="modal" data-bs-target="#addModal" @click="clearTransaction()">Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Modal -->
        <modal-component id="addModal" title="New Car">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :returnStatus="returnStatus" v-if="returnStatus == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :returnStatus="returnStatus" v-if="returnStatus == 'error'" type="danger"></alert-component>
            </template>
            <template v-slot:content>
                <div class="form-group">
                    <select-component :id="'car_model_id'" :title="'Car Model'" :data="car_models" v-model="newCar.car_model_id"></select-component>
                    <input-component title="Plate" id="plate" >
                        <input type="text" required class="form-control" id="plate" v-model="newCar.plate">
                    </input-component>
                    <input-component title="KM" id="km" >
                        <input type="number" required value="0" min="0" class="form-control" id="km" v-model="newCar.km">
                    </input-component>
                    <input-component title="Daily Rate" id="daily_rate" >
                        <input type="number" required value="0" min="0" class="form-control" id="km" v-model="newCar.daily_rate">
                    </input-component>
                    <checkbox-component title="Avaliable" id="avaliable" :checked="false" v-model="newCar.avaliable"></checkbox-component>
                </div>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="save()">Create</button>
            </template>
        </modal-component>

        <!-- Show Modal -->
        <modal-component id="showModal" title="Show Car Model">
            <template v-slot:content>
                <input-component  class=" fw-bold mb-2" title="ID">
                    <input disabled type="text" :value="$store.state.item.id" class="form-control ">
                </input-component>
                <input-component v-if="$store.state.item.car_model" class=" fw-bold mb-2 mt-2" title="Car Model">
                    <input disabled type="text" :value="$store.state.item.car_model.name" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car_model" class="  fw-bold" title="Doors">
                    <input disabled type="text" :value="$store.state.item.car_model.doors" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car_model" class=" fw-bold mb-2 mt-2" title="Seats">
                    <input disabled type="text" :value="$store.state.item.car_model.seats" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.car_model" class=" fw-bold mb-2 mt-2" title="Daily Rate">
                    <input disabled type="text" :value="$store.state.item.daily_rate" class="form-control " >
                </input-component>
                <true-false-icons-component v-if="$store.state.item.car_model" class="text-center" :style="'font-size: 18px'" :width="20" :height="20" :title="'Airbag'" :condition="$store.state.item.car_model.air_bag"></true-false-icons-component>
                <true-false-icons-component v-if="$store.state.item.car_model" class="text-center" :style="'font-size: 18px'" :width="20" :height="20" :title="'ABS'" :condition="$store.state.item.car_model.abs"></true-false-icons-component>
                <input-component v-if="$store.state.item.image" class="text-center">
                    <img  :src="'/storage/' + $store.state.item.car_model.image" alt="Image" class="img-fluid" style="margin: 0 0 0 1%;" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#leaseModal" @click="clearTransaction()">Lease</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </template>
        </modal-component>
         <!-- Delete Modal -->
         <modal-component id="deleteModal" title="Delete Car Model">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :details="returnDetails" v-if="$store.state.transaction.status == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :details="returnDetails" v-if="$store.state.transaction.status == 'error'" type="danger"></alert-component>
            </template>
            <template v-if="$store.state.item.car_model" v-slot:content>
                <input-component title="ID">
                    <input disabled type="text" :value="$store.state.item.id" class="form-control">
                </input-component>
                <input-component title="Name">
                    <input disabled type="text" :value="$store.state.item.car_model.name" class="form-control" >
                </input-component>
                <input-component title="Plate">
                    <input disabled type="text" :value="$store.state.item.plate" class="form-control" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteCar()">Delete</button>
            </template>
        </modal-component>
         <!-- Lease Modal -->
         <modal-component id="leaseModal" title="Lease">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :details="returnDetails" v-if="$store.state.transaction.status == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :details="returnDetails" v-if="$store.state.transaction.status == 'error'" type="danger"></alert-component>
            </template>
            <template v-if="$store.state.item.car_model" v-slot:content>
                <input-component title="Name">
                    <input disabled type="text" :value="$store.state.item.car_model.name" class="form-control" >
                </input-component>
                <input-component title="Plate">
                    <input disabled type="text" :value="$store.state.item.plate" class="form-control" >
                </input-component>
                <input-component title="Daily Rate">
                    <input disabled type="text" :value="$store.state.item.daily_rate" class="form-control" >
                </input-component>
                <input-component title="Start Date">
                    <input required type="datetime-local" v-model="this.leaseData.start_date" class="form-control" >
                </input-component>
                <input-component title="Expected End Date">
                    <input required :min="this.leaseData.start_date" type="datetime-local" v-model="this.leaseData.expected_end_date" class="form-control" >
                </input-component>
                <input-component title="Total">
                    <input disabled  type="number" :value="calculateDateDifference * $store.state.item.daily_rate" class="form-control" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" @click="leaseCar()">Confirm</button>
            </template>
        </modal-component>

        <!-- Edit Modal -->
        <modal-component v-if="$store.state.item.car_model"  id="editModal" title="Edit Car">
        <template v-slot:alerts>
            <alert-component :title="'Success'" v-if="this.$store.state.transaction.status == 'success'"  type="success"></alert-component>
            <alert-component :title="'Error'" v-if="this.$store.state.transaction.status == 'error'" type="danger"></alert-component>
        </template>
        <template v-slot:content>
            <input-component  class=" fw-bold mb-2 mt-2" title="KM">
                <input type="text" v-model="$store.state.item.km" class="form-control " >
            </input-component>
            <select-component :id="'brand_id'" :title="'Car Model'" :data="car_models" :selectedId="$store.state.item.car_model_id" v-model="$store.state.item.car_model_id"></select-component>
            <input-component  class=" fw-bold mb-2 mt-2" title="Plate">
                <input type="text" v-model="$store.state.item.plate" class="form-control " >
            </input-component>
            <input-component  class=" fw-bold mb-2 mt-2" title="Daily Rate">
                <input type="text" v-model="$store.state.item.daily_rate" class="form-control " >
            </input-component>
            <checkbox-component title="Avaliable" id="avaliable" :checked="$store.state.item.avaliable" v-model="$store.state.item.avaliable"></checkbox-component>
        </template>
        <template v-slot:modal-footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="update()">Update</button>
        </template>
        </modal-component>
    </div>
</template>
<script>
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
            if (!this.leaseData.start_date || !this.leaseData.expected_end_date) return '';
            
            const startDate = new Date(this.leaseData.start_date);
            const endDate = new Date(this.leaseData.expected_end_date);
            
            const differenceMs = endDate - startDate;
            
            const differenceDays = Math.floor(differenceMs / (1000 * 60 * 60 * 24));
            
            return differenceDays;
            }
        },
        watch: {
            'leaseData.start_date'(newStartDate) {
                if (this.leaseData.expected_end_date && new Date(this.leaseData.expected_end_date) < new Date(newStartDate)) {
                    this.leaseData.expected_end_date = newStartDate;
                }
            }
        },
        data() {
            return {
                baseUrl: 'http://localhost/api',
                newCar: {
                    plate: '',
                    km: '',
                    car_model_id: '',
                    avaliable: false,
                    daily_rate: '',
                },
                imageFile: [],
                returnStatus: '',
                cars: [],
                filters: { 
                    doors: '',
                    seats: '',
                    abs: '',
                    air_bag: '',
                    avaliable: true
                },
                paginationUrl: '',
                filterUrl: '',
                car_models: [],
                leaseData: {
                    start_date: '',
                    expected_end_date: ''
                }
            }
        },
        methods:
            {
                save()
                {
                    let formData = new FormData();
                    formData.append('plate', this.newCar.plate);
                    formData.append('km', this.newCar.km);
                    formData.append('daily_rate', this.newCar.daily_rate);
                    formData.append('car_model_id', this.newCar.car_model_id);
                    formData.append('avaliable', this.newCar.avaliable === true ? 1 : 0);

                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    let url = this.baseUrl + '/car';
                    axios.post(url, formData, config)
                    .then(response => {
                        this.returnStatus = 'success'
                        this.$store.state.transaction.message = response.data.msg
                        this.loadList();
                    })
                    .catch(errors => {
                        this.returnStatus = 'error'
                        this.$store.state.transaction.message = errors.response.data.errors
                    })
                },
                loadList() {
                    let url = this.baseUrl + '/car?' + this.paginationUrl + this.filterUrl + '&paginate=5 ';
                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.get(url, config)
                    .then(response => {
                        this.cars = response.data;
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
                            if(key == 'abs' || key == 'air_bag' || key == 'avaliable'){
                                filter += `&${key}==:${this.filters[key]}`                            
                            } else if(key == 'car_model_id' || key == 'doors' || key == 'seats') {
                                filter += `&${key}==:${this.filters[key]}`                            
                            } else {
                                filter += `&${key}:ilike:%${this.filters[key]}%`                            
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
                deleteCar(){
                    let formData = new FormData();
                    formData.append('_method', 'delete');
                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    let url = this.baseUrl + '/car/' +this.$store.state.item.id
                    axios.post(url, formData, config)
                    .then(response => {
                        this.loadList();
                    })
                    .catch(errors => {
                        this.$store.state.transaction.status = 'error'
                        this.$store.state.transaction.message = errors
                    })
                },
                update(){
                    let url = this.baseUrl + '/car/' + this.$store.state.item.id

                    let formData = new FormData();
                    formData.append('_method', 'patch');
                    formData.append('plate', this.$store.state.item.plate);
                    formData.append('km', this.$store.state.item.km);
                    formData.append('car_model_id',this.$store.state.item.car_model_id);
                    formData.append('avaliable', this.$store.state.item.avaliable === true ? 1 : 0);

                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.post(url, formData, config)
                    .then(response => {
                        this.$store.state.transaction.status = 'success'
                        this.$store.state.transaction.message = response.data.msg
                        this.loadList();
                    })
                    .catch(errors => {
                        this.$store.state.transaction.status = 'error'
                        this.$store.state.transaction.message = errors.response.data.errors
                    })
                },
                carModelList(){
                    let url = this.baseUrl + '/car-model'
                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.get(url, config)
                    .then(response => {
                        this.car_models = response.data
                    })
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

                     // Verifica se as datas foram preenchidas
                    if (this.leaseData.start_date == '' || this.leaseData.expected_end_date == '') {
                        this.$store.state.transaction.status = 'error';
                        this.$store.state.transaction.message = {
                            error: {
                                0: 'Select the start date and the expected end date!'   
                            }
                        }
                        return false;
                    }

                    // Validar as datas
                    let startDate = new Date(this.leaseData.start_date);
                    let endDate = new Date(this.leaseData.expected_end_date);

                    if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                        this.$store.state.transaction.status = 'error';
                        this.$store.state.transaction.message = {
                            error: {
                                0: 'Invalid date format!'   
                            }
                        }
                        return false;
                    }

                    // Verificar se a data de início é anterior à data final
                    if (startDate >= endDate) {
                        this.$store.state.transaction.status = 'error';
                        this.$store.state.transaction.message = {
                            error: {
                                0: 'Start date must be before expected end date!'   
                            }
                        }
                        return false;
                    }

                    let formData = new FormData();
                    formData.append('id', this.$store.state.item.id);
                    formData.append('start_date', this.leaseData.start_date);
                    formData.append('expected_end_date',this.leaseData.expected_end_date);
                    
                    axios.post(url, formData, config)
                    .then(response => {
                        this.loadList();
                    })
                }
            },
            mounted() {
                this.loadList();
                this.carModelList();
            }
        }
</script>