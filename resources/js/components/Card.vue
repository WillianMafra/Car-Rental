<template>
    <div class="card  mb-3">
        <div class="card-header">{{ title }}</div>
        <div class="card-body">
           <slot name="content"></slot>
        </div>
        <div class="card-footer d-grid gap-2 d-md-flex justify-content-md-end">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title']
    }
</script>
