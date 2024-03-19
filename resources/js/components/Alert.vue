<template>
    <div :class="style" role="alert">
        {{ title }}
        <hr>
        <ul v-if="$store.state.transaction.status == 'error' || returnStatus == 'error'">
            <li v-for="value, key in $store.state.transaction.message" :key="key">{{ value[0] }}</li>
        </ul>
        <p v-if="$store.state.transaction.status == 'success' || returnStatus == 'success'" >{{ $store.state.transaction.message }}</p>
    </div>
</template>

<script>
    export default {
        props: ['type', 'title', 'returnStatus'],
        computed: {
            style() {
                return 'alert alert-'+this.type;
            }
        }
    }

</script>

