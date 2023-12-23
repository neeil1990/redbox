<template>

    <div class="modal fade" :id="target" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ lang.save_project }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label class="col-form-label">{{ lang.project_name }}:</label>
                            <input type="text" class="form-control" v-model="name" required>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input" :id="'customSwitchStatusForm' + target + status" v-model="status">
                                <label class="custom-control-label" :for="'customSwitchStatusForm' + target + status">{{ lang.status }}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">{{ lang.period }}:</label>

                            <div v-for="radio in radios" class="custom-control custom-radio">
                                <input v-model.number="period" class="custom-control-input" type="radio" :id="'Radio' + target + radio.value" :value="radio.value">
                                <label :for="'Radio' + target + radio.value" class="custom-control-label">{{ radio.text }}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ lang.timeout }}:</label>
                            <input type="number" min="1" class="form-control" v-model="timeout">
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">{{ lang.link }}:</label>
                            <textarea class="form-control" v-model="link" required></textarea>
                        </div>

                        <div class="row" v-for="len in length">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>{{ lang.length_word }} {{ len.name }}</label>
                                    <input type="number" class="form-control" placeholder="min" v-model.number="len.input.min">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="number" class="form-control" placeholder="max" v-model.number="len.input.max">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ lang.close }}</button>
                    <button @click.prevent="OnSubmitMetaForm" type="button" class="btn btn-primary">{{ lang.save }}</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "BaseModalForm",
        props: {
            request: {
                required: true,
                type: String
            },
            method: {
                required: true,
                type: String
            },
            target: {
                required: true,
                type: String
            },
            data: {
                type: [Array, Object]
            },
            values: {
                type: [Array, Object]
            },
            links: {
                type: String
            },
            lang: {
                type: [Array, Object]
            },
        },
        created(){

            this.link = this.links
        },
        data(){
            return {
                status: 0,
                name: 'My project',
                period: 6,
                link: '',
                timeout: 500,
                length: [
                    {id: 'title', name: this.lang.title, input: {min: null, max: null}},
                    {id: 'description', name: this.lang.description, input: {min: null, max: null}},
                    {id: 'keywords', name: this.lang.keywords, input: {min: null, max: null}},
                ],
                radios: [
                    {value: 0, text: 'manual'},
                    {value: 6, text: this.lang.check_interval_every + ' 6 ' + this.lang.hours},
                    {value: 12, text: this.lang.check_interval_every + ' 12 ' + this.lang.hours},
                    {value: 24, text: this.lang.check_interval_every + ' 24 ' + this.lang.hours},
                ],
            }
        },
        watch: {
            values: function(val){

                this.status = val.status;
                this.name = val.name;
                this.period = val.period;
                this.link = val.links;
                this.timeout = val.timeout;

                _.forEach(this.length, function(value) {
                    value.input.min = val[value.id + '_min'];
                    value.input.max = val[value.id + '_max'];
                });
            }
        },
        methods: {

            OnSubmitMetaForm() {
                var app = this;

                var data = {
                    status: app.status,
                    name: app.name,
                    period: app.period,
                    links: app.link,
                    timeout: app.timeout,
                    histories: app.data,
                };

                _.forEach(this.length, function(value) {
                    data[value.id + '_min'] = value.input.min;
                    data[value.id + '_max'] = value.input.max;
                });

                axios.request({
                    url: app.request,
                    method: app.method,
                    data: data,
                }).then(function(response){
                    console.log(response);

                    if(response.statusText === "OK");
                        toastr.success('Успешно изменено');

                    app.$emit('close-modal-form', response);
                }).catch(function (error) {
                    if(error.response){
                        console.log(error.response);
                        toastr.error(error.response.data.message);
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>
