<template>
    <div>
      <table class="table table-hover">
        <thead>
          <tr>
            <th v-for="(header, key) in tableHeaders" :key="key" class="text-uppercase" scope="col">{{ header.title }}</th>
            <th v-if=" deleteButton.visible || showButton.visible || editButton.visible"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in data" :key="row.id">
            <td v-for="(column, key) in tableHeaders" :key="key">
              <template v-if="row.hasOwnProperty(key)">
                <template v-if="column.type === 'image'">
                  <img :src="'/storage/' + row[key]">
                </template>
                <template v-else>
                  {{ row[key] }}
                </template>
              </template>
            </td>
            <td v-if=" deleteButton || showButton.visible || editButton">
              <button :data-bs-toggle="showButton.toggle" :data-bs-target="showButton.target" v-if="showButton.visible" class="btn btn-outline-primary btn-sm" @click="setStore(row)">Show</button>
              <button :data-bs-toggle="editButton.toggle" :data-bs-target="editButton.target" v-if="editButton.visible" class="btn btn-outline-secondary btn-sm" @click="setStore(row)">Edit</button>
              <button :data-bs-toggle="deleteButton.toggle" :data-bs-target="deleteButton.target" v-if="deleteButton.visible" class="btn btn-outline-danger btn-sm" @click="setStore(row)">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  export default {
    props: ['data', 'tableHeaders', 'deleteButton', 'showButton', 'editButton'],
    methods: {
      setStore(object){
        this.$store.state.item = object 
      }
    }
  };
  </script>
  