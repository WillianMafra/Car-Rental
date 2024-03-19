<template>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" v-model="selectedOption" role="switch" :checked="checked" :id="id"  @change="updateSelectedOption()">
        <label class="form-check-label" :for="id">
            {{ title }}
        </label>
    </div>
</template>
 
<script>
export default {
    props: [
        'id',
        'title',
        'checked'
    ],
    data() {
        return {
            selectedOption: this.checked
        };
    },
    methods: {
        updateSelectedOption() {
            this.$emit('update:modelValue', this.selectedOption);
        }
    }
}
</script>
