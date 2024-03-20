<template>
    <label :for="id">{{ title }}</label>
    <select class="form-select" :id="id" :value="modelValue" @change="updateSelectedOption($event.target.value)">
        <option v-for="(value, key) in data" :value="key">{{ value }}</option>
    </select>
</template>

<script>
export default {
    props: ['title', 'id', 'modelValue'],
    data() {
        return {
            data: {
                '': '---',
                '1': 'Yes',
                '0': 'No'
            }
        };
    },
    methods: {
        updateSelectedOption(value) {
            this.$emit('update:modelValue', value);
        }
    }
}
</script>
