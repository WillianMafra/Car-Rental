<template>
    <div class="form-group">
        <label :for="id" class="form-label">{{ title }}</label>
        <slot></slot>
    </div>
</template>
 
 <script>
     export default {
        props: [
            'id',
            'title'
        ]
    }
 </script>