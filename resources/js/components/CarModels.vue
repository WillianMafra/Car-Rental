<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component title="Car Models">
                    <template v-slot:content>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-component title="ID" id="inputId">
                                    <input type="number" class="form-control" id="inputId" v-model="filters.id">
                                </input-component>
                            </div>
                            <div class="col mb-3">
                                <input-component title="Car Name" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.name">
                                </input-component>
                            </div>
                            <div class="col mb-3">
                                <input-component title="Brand Name" id="inputName">
                                    <input type="text" class="form-control" id="inputName" v-model="filters.brand_name">
                                </input-component>
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
                                <checkbox-component v-model="filters.abs" :title="'ABS'" ></checkbox-component>
                            </div>
                            <div class="col mb-3">
                                <checkbox-component v-model="filters.air_bag" :title="'Airbag'" ></checkbox-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:footer>
                        <button type="submit" class="btn btn-primary btn-sm me-md-2" @click="search()">Search</button>
                    </template>
                </card-component>
                <div class="card">
                    <div class="card-header">Car Model List</div>
                    <div class="card-body">
                        <table-component 
                            :data="cars.data" 
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
                                name: { title: 'name', type: 'text' },
                                image: { title: 'image', type: 'image', style: 'width: 100px; height:100px' },
                                seats: { title: 'seats', type: 'number' },
                                doors: { title: 'doors', type: 'number' },
                                abs: { title: 'abs', type: 'boolean' },
                                air_bag: { title: 'airbag', type: 'boolean' },
                            }">
                        </table-component>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <paginate-component class="me-auto">
                            <ul class="pagination">
                                <li v-for="(link, key) in cars.links" :key="key" class="page-item">
                                    <a :class="{ 'page-link': true, 'active': link.active }" href="#" @click="pagination(link)" v-html="link.label"></a>
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
        <!-- Add Modal -->
        <modal-component id="addModal" title="New Car Model">
            <template v-slot:alerts>
                <alert-component :title="'Success'" :returnStatus="returnStatus" v-if="returnStatus == 'success'"  type="success"></alert-component>
                <alert-component :title="'Error'" :returnStatus="returnStatus" v-if="returnStatus == 'error'" type="danger"></alert-component>
            </template>
            <template v-slot:content>
                <div class="form-group">
                    <select-component :id="'brand_id'" :title="'Brand'" :data="brands" v-model="newCar.brand_id"></select-component>
                    <input-component title="Car Name" id="name" >
                        <input type="text" required class="form-control" id="name" v-model="newCar.name">
                    </input-component>
                    <input-component title="Doors" id="doors" >
                        <input type="number" required value="4" min="1" max="5" class="form-control" id="inputName" v-model="newCar.doors">
                    </input-component>
                    <input-component title="Seats" id="seats" >
                        <input type="number" value="2" min="2" max="20" required class="form-control" id="seats" v-model="newCar.seats">
                    </input-component>
                    <checkbox-component title="Airbag" id="airbag" :checked="false" v-model="newCar.air_bag"></checkbox-component>
                    <checkbox-component title="Abs" id="abs" :checked="false" v-model="newCar.abs"></checkbox-component>
                    <input-component title="Image" id="inputImage">
                        <input type="file" class="form-control" id="inputImage" @change="loadImage($event)">
                    </input-component>
                </div>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click="save()">Create</button>
            </template>
        </modal-component>

        <!-- Show Modal -->
        <modal-component  id="showModal" title="Show Car Model">
            <template v-slot:content>
                <input-component  class=" fw-bold mb-2" title="ID">
                    <input disabled type="text" :value="$store.state.item.id" class="form-control ">
                </input-component>
                <input-component  class=" fw-bold mb-2 mt-2" title="Name">
                    <input disabled type="text" :value="$store.state.item.name" class="form-control " >
                </input-component>
                <input-component v-if="$store.state.item.brand"  class="  fw-bold" title="Brand">
                    <input disabled type="text" :value="$store.state.item.brand.name" class="form-control " >
                </input-component>
                <input-component  class=" fw-bold mb-2 mt-2" title="Doors">
                    <input disabled type="text" :value="$store.state.item.doors" class="form-control " >
                </input-component>
                <input-component  class=" fw-bold mb-2 mt-2" title="Seats">
                    <input disabled type="text" :value="$store.state.item.seats" class="form-control " >
                </input-component>
                <true-false-icons-component class="text-center" :style="'font-size: 18px'" :width="20" :height="20" :title="'Airbag'" :condition="$store.state.item.air_bag"></true-false-icons-component>
                <true-false-icons-component class="text-center" :style="'font-size: 18px'" :width="20" :height="20" :title="'ABS'" :condition="$store.state.item.abs"></true-false-icons-component>
                <input-component  class="text-center">
                    <img v-if="$store.state.item.image" :src="'/storage/' + $store.state.item.image" alt="Image" class="img-fluid" style="margin: 0 0 0 1%;" >
                </input-component>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </template>
        </modal-component>
         <!-- Delete Modal -->
         <modal-component id="deleteModal" title="Delete Car Model">
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
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteCar()">Delete</button>
            </template>
        </modal-component>

        <!-- Edit Modal -->
        <modal-component id="editModal" title="Edit Car Model">
        <template v-slot:alerts>
            <alert-component :title="'Success'" v-if="this.$store.state.transaction.status == 'success'"  type="success"></alert-component>
            <alert-component :title="'Error'" v-if="this.$store.state.transaction.status == 'error'" type="danger"></alert-component>
        </template>
        <template v-slot:content>
            <input-component  class=" fw-bold mb-2" title="ID">
                <input type="text" v-model="$store.state.item.id" class="form-control ">
            </input-component>
            <input-component  class=" fw-bold mb-2 mt-2" title="Name">
                <input type="text" v-model="$store.state.item.name" class="form-control " >
            </input-component>
            <select-component :id="'brand_id'" :title="'Brand'" :data="brands" :selectedId="$store.state.item.brand_id" v-model="$store.state.item.brand_id"></select-component>
            <input-component  class=" fw-bold mb-2 mt-2" title="Doors">
                <input type="text" v-model="$store.state.item.doors" class="form-control " >
            </input-component>
            <input-component  class=" fw-bold mb-2 mt-2" title="Seats">
                <input type="text" v-model="$store.state.item.seats" class="form-control " >
            </input-component>
            <checkbox-component title="Airbag" id="airbag" :checked="$store.state.item.air_bag" v-model="$store.state.item.air_bag"></checkbox-component>
            <checkbox-component title="Abs" id="abs" :checked="$store.state.item.abs" v-model="$store.state.item.abs"></checkbox-component>
            <input-component class=" fw-bold mb-2 mt-2" title="New Image" id="inputEditImage">
                <input type="file" class="form-control" id="inputEditImage" @change="loadImage($event)">
            </input-component>
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
                baseUrl: 'http://localhost/api',
                newCar: {
                    name: '',
                    seats: 2,
                    doors: 4,
                    brand_id: '',
                    air_bag: false,
                    abs: false
                },
                imageFile: [],
                returnStatus: '',
                cars: [],
                filters: { 
                    id: '',
                    name: '',
                    brand_name: '',
                    doors: '',
                    seats: '',
                    abs: false,
                    airbag: false,
                },
                paginationUrl: '',
                filterUrl: '',
                brands: []
            }
        },
        methods:
            {
                loadImage(e) {
                    this.imageFile = Array.from(e.target.files);
                },

                save()
                {
                    let formData = new FormData();
                    formData.append('name', this.newCar.name);
                    formData.append('image', this.imageFile[0]);
                    formData.append('doors', this.newCar.doors);
                    formData.append('seats', this.newCar.seats);
                    formData.append('brand_id', this.newCar.brand_id);
                    formData.append('abs', this.newCar.abs === true ? 1 : 0);
                    formData.append('air_bag', this.newCar.air_bag === true ? 1 : 0);

                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    let url = this.baseUrl + '/car-model';
                    axios.post(url, formData, config)
                    .then(response => {
                        console.log(response);
                        this.returnStatus = 'success'
                        this.$store.state.transaction.message = response.data.msg
                        this.loadList();
                    })
                    .catch(errors => {
                        console.log(errors);
                        this.returnStatus = 'error'
                        this.$store.state.transaction.message = errors.response.data.errors
                    })
                },
                loadList() {
                    let url = this.baseUrl + '/car-model?' + this.paginationUrl + this.filterUrl + '&paginate=2';
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
                        if(this.filters[key] != ''){
                            if(filter != ''){
                                filter += ';';
                            }     
                            filter += `${key}:ilike:%${this.filters[key]}%`                            
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
                deleteCar(){
                    let formData = new FormData();
                    formData.append('_method', 'delete');
                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    let url = this.baseUrl + '/car-model/' +this.$store.state.item.id
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
                    let url = this.baseUrl + '/car-model/' + this.$store.state.item.id

                    let formData = new FormData();
                    formData.append('_method', 'patch');
                    if(this.imageFile[0]){
                        formData.append('image', this.imageFile[0]);
                    }
                    formData.append('name', this.$store.state.item.name);
                    formData.append('doors', this.$store.state.item.doors);
                    formData.append('seats', this.$store.state.item.seats);
                    formData.append('brand_id', this.$store.state.item.brand_id);
                    formData.append('abs', this.$store.state.item.abs === true ? 1 : 0);
                    formData.append('air_bag', this.$store.state.item.air_bag === true ? 1 : 0);
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
                        console.log(errors);
                        this.$store.state.transaction.status = 'error'
                        this.$store.state.transaction.message = errors.response.data.errors
                    })
                },
                brandList(){
                    let url = this.baseUrl + '/brand'
                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }
                    axios.get(url, config)
                    .then(response => {
                        this.brands = response.data
                    })
                    .catch(error => {
                        console.log(error)
                    })
                },
                clearTransaction(){
                    this.$store.state.transaction.status = ''
                    this.$store.state.transaction.message = ''
                    this.returnStatus = ''
                }
            },
            mounted() {
                this.loadList();
                this.brandList();
            }
        }
</script>