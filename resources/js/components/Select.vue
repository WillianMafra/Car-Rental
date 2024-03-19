<template>
    <label :for="id">{{ title }}</label>
    <select v-if="data.data !== ''" class="form-select" :id="id" :value="selectedId" @change="updateSelectedOption($event.target.value)">
        <option value="">----</option>
        <template v-for="value in data.data">
            <option :value="value.id">{{ value.name}}</option>            
        </template>
    </select>
</template>

<script>
export default {
    props: [
        'data',
        'title',
        'selectedId',
        'id',
        'modelValue' // Adicionado prop modelValue
    ],
    data() {
        return {
            selectedOption: null
        };
    },
    mounted() {
        // Atualize selectedOption com o valor inicial do modelValue
        this.selectedOption = this.modelValue;
    },
    methods: {
        updateSelectedOption(value) {
            this.selectedOption = value;
            this.$emit('update:modelValue', value);
        }
    }
}
</script>
