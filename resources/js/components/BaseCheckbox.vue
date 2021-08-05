<template>
    <div class="custom-control custom-checkbox">
        <input class="custom-control-input" type="checkbox" :id="data.id" :value="data.name" v-model="checked">
        <label :for="data.id" class="custom-control-label">{{ data.title }}</label>
    </div>
</template>

<script>
    export default {
        name: "BaseCheckbox",
        props: {
            data: {
                required: true,
                type: Object
            }
        },
        data(){
            return {
                checked: false
            }
        },
        created() {
            this.checked = this.data.selected;
        },
        watch: {
            checked: function (val) {
                this.data.selected = val;
            },
        }
    }
</script>


