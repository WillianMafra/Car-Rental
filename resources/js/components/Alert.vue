<template>
    <div :class="style" role="alert">
        {{ title }}
        <hr>
        <ul v-if="details.data.errors">
            <li v-for="value, key in details.data.errors" :key="key">{{ value[0] }}</li>
        </ul>
        <p v-if="details.data.msg" >{{ details.data.msg }}</p>
    </div>
</template>

<script>
    export default {
        props: ['type', 'title', 'details'],
        computed: {
            style() {
                return 'alert alert-'+this.type;
            }
        }
    }

</script>

