<template>

    <div class="form-group">
        <label for="filter">Filter</label>
        <select class="custom-select" id="filter" v-model="selected" @change.prevent="onChange">
            <option value="all">Все</option>
            <option v-for="option in options" v-bind:value="option.value">{{ option.text }}</option>
        </select>
    </div>

</template>

<script>
    export default {
        name: "MetaFilter",
        props: {
            metaTags: [Object, Array],
            seen: [Object, Array]
        },
        created() {

            this.updateOptions(this.metaTags);
        },
        watch:{
            metaTags: function(obj){

                this.updateOptions(obj);
            }
        },
        data(){
            return {
                selected: 'all',
                options: []
            }
        },
        methods: {

            onChange() {
                var app = this;

                this.update(0);

                if(app.selected === 'all'){
                    this.update(1);
                    return;
                }

                _.forEach(this.metaTags, (value, index) => {
                    _.map(value.error.badge, (v, tag) => {
                        if(tag === app.selected && v.length)
                            app.$set(app.seen, index, 1);
                    });
                });

            },
            update(seen) {
                for (let i = 0; i < Object.keys(this.metaTags).length; i++)
                    this.$set(this.seen, i, seen);
            },
            updateOptions(objs) {

                _.forEach(objs, (value, index) => {
                    _.map(value.error.badge, (v, tag) => {
                        if(v.length)
                            this.options = Object.assign({}, this.options, { [tag]: {value: tag, text: _.upperFirst(tag)} })

                    });
                });
            }
        }
    }
</script>

<style scoped>

</style>
