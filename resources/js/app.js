/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue'
import { createStore } from 'vuex'


/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */
// Cria uma nova instância do store.
const store = createStore({
    state () {
      return {
        item: {},
        transaction: {
          status: '',
          message: '' 
        }
      }
    }
  })
const app = createApp({});
app.use(store)

import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);

import LoginComponent from './components/Login.vue';
app.component('login-component', LoginComponent);

import HomeComponent from './components/Home.vue';
app.component('home-component', HomeComponent);

import BrandsComponent from './components/Brands.vue';
app.component('brands-component', BrandsComponent);

import InputComponent from './components/Input.vue';
app.component('input-component', InputComponent);

import TableComponent from './components/Table.vue';
app.component('table-component', TableComponent);

import CardComponent from './components/Card.vue';
app.component('card-component', CardComponent);

import ModalComponent from './components/Modal.vue';
app.component('modal-component', ModalComponent);

import AlertComponent from './components/Alert.vue';
app.component('alert-component', AlertComponent);

import PaginateComponet from './components/Paginate.vue';
app.component('paginate-component', PaginateComponet);

import CarModelComponent from './components/CarModels.vue';
app.component('car-models-component', CarModelComponent);

import SelectComponent from './components/Select.vue';
app.component('select-component', SelectComponent);

import CheckboxComponent from './components/Checkbox.vue';
app.component('checkbox-component', CheckboxComponent);

import CarComponent from './components/Car.vue';
app.component('car-component', CarComponent);

import trueFalseIcons from './components/TrueFalseIcons.vue';
app.component('true-false-icons-component', trueFalseIcons);

import YesNoComponent from './components/YesNo.vue';
app.component('yes-no-component', YesNoComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
