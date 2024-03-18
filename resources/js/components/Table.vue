<template>
    <div>
      <table class="table table-hover">
        <thead>
          <tr>
            <th v-for="(header, key) in tableHeaders" :key="key" class="text-uppercase" scope="col">{{ header.title }}</th>
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
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  export default {
    props: ['data', 'tableHeaders']
  };
  </script>
  