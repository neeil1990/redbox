<template>
    <div class="card-tools" v-if="show">
        <span class="badge badge-danger">Обнаружены критические ошибки</span>
        <span v-for="(item, tag) in data" v-if="item.length > 1 && (tag === 'title' || tag === 'description' || tag === 'canonical' || tag === 'h1')" class="badge badge-info mr-1">< {{ tag }} > : {{ item.length }}</span>
    </div>
</template>

<script>
    export default {
        name: "BaseTools",
        props: {
            data: {
                required: true,
                type: Object
            }
        },
        data() {
            return {
                show: false
            }
        },
        created: function() {
            var app = this;

            _.forEach(this.data, function(value, key) {
                if(key === 'title' || key === 'description' || key === 'canonical' || key === 'h1'){

                    if(value.length > 1){
                        app.show = true;
                    }
                }
            });
        }
    }
</script>

