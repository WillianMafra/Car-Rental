<template>
  <div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th v-for="(header, key) in tableHeaders" :key="key" class="text-center text-uppercase" scope="col">{{ header.title }}</th>
          <th  v-if="deleteButton.visible || showButton.visible || editButton.visible"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="row in data" :key="row.id">
          <td class="text-center" v-for="(column, key) in tableHeaders" :key="key">
              <template v-if="column.type === 'image'">
                <img :style="column.style" :src="column.path ? '/storage/' + getNestedValue(row, column.path) : '/storage/' +  row[key]">
              </template>
              <template v-else-if="typeof row[key] === 'boolean'">
                <svg v-if="row[key]" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-center bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-center bi bi-x-circle-fill text-danger" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                </svg>
              </template>
              <template v-else-if="column.path">
                {{ getNestedValue(row, column.path) }}
              </template>
              <template v-else>
                {{ row[key] }}
              </template>

          </td>
          <td v-if="deleteButton || showButton.visible || editButton">
            <button :data-bs-toggle="showButton.toggle" :data-bs-target="showButton.target" v-if="showButton.visible" class="btn btn-outline-primary btn-sm" @click="setStore(row)">Show</button>
            <button :data-bs-toggle="editButton.toggle" :data-bs-target="editButton.target" v-if="editButton.visible" class="btn btn-outline-secondary btn-sm m-1" @click="setStore(row)">Edit</button>
            <button :data-bs-toggle="deleteButton.toggle" :data-bs-target="deleteButton.target" v-if="deleteButton.visible" class="btn btn-outline-danger btn-sm " @click="setStore(row)">Delete</button>
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
      this.$store.state.transaction = {
        status: '',
        message: ''
      }
      const clonedObject = JSON.parse(JSON.stringify(object));
      this.$store.state.item = clonedObject;        
    },
    getNestedValue(obj, path) {
      return path.split('.').reduce((acc, key) => acc[key], obj);
    }
  }
};
</script>
