<template>
    <div class="card-tools" v-if="show">
        <span class="badge badge-danger">Обнаружены критические ошибки</span>
        <span v-for="(item, tag) in data" v-if="item.length > 1 && (tag === 'title' || tag === 'description' || tag === 'canonical' || tag === 'h1')"
              class="badge badge-info mr-1">< {{ tag }} > : {{ item.length }}</span>

        <span v-if="error" v-for="e in error" class="badge badge-info mr-1">{{ e }}</span>
    </div>
</template>

<script>
    export default {
        name: "BaseTools",
        props: {
            data: {
                required: true,
                type: Object
            },
            length: {
                required: true,
            }
        },
        data() {
            return {
                show: false,
                error: []
            }
        },
        created: function() {
            var app = this;

            _.forEach(this.data, function(value, key) {
                if(key === 'title' || key === 'description' || key === 'keywords' || key === 'canonical' || key === 'h1'){

                    if(value.length > 1){
                        app.show = true;
                    }

                    let idx = _.findIndex(app.length, function(o) { return o.key === key; });
                    if(app.length[idx] && value[0]){
                        let meta_count = value[0].length;
                        let count_arr = app.length[idx].val;

                        if(count_arr[0] && count_arr[1]){
                            if(meta_count < count_arr[0] || meta_count > count_arr[1]){
                                app.show = true;
                                app.error.push('Длина ' + key + ': ' + meta_count);
                            }
                        }
                    }
                }
            });
        }
    }
</script>

