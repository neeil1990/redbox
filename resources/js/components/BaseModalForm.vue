<template>

    <div class="modal fade" :id="target" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Сохранить проект</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label class="col-form-label">Название проекта:</label>
                            <input type="text" class="form-control" v-model="name" required>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" class="custom-control-input" :id="'customSwitchStatusForm' + target + status" v-model="status">
                                <label class="custom-control-label" :for="'customSwitchStatusForm' + target + status">Status</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">Частота проверок:</label>

                            <div v-for="radio in radios" class="custom-control custom-radio">
                                <input v-model.number="period" class="custom-control-input" type="radio" :id="'Radio' + target + radio.value" :value="radio.value">
                                <label :for="'Radio' + target + radio.value" class="custom-control-label">{{ radio.text }}</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Timeout:</label>
                            <input type="number" min="1" class="form-control" v-model="timeout">
                        </div>

                        <div class="form-group">
                            <label class="col-form-label">Ссылки:</label>
                            <textarea class="form-control" v-model="link" required></textarea>
                        </div>

                        <div class="row" v-for="len in length">
                            <div class="col-sm-6">
                                <!-- text input -->
                                <div class="form-group">
                                    <label>Длина {{ len.name }}</label>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button @click.prevent="OnSubmitMetaForm" type="button" class="btn btn-primary">Сохранить</button>
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
                    {id: 'title', name: 'title (recommend 30-70)', input: {min: null, max: null}},
                    {id: 'description', name: 'description (recommend 30-70)', input: {min: null, max: null}},
                    {id: 'keywords', name: 'keywords (recommend 30-70)', input: {min: null, max: null}},
                ],
                radios: [
                    {value: 0, text: 'manual'},
                    {value: 6, text: 'Интервал проверки каждые 6 часов'},
                    {value: 12, text: 'Интервал проверки каждые 12 часов'},
                    {value: 24, text: 'Интервал проверки каждые 24 часов'},
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

                    console.log(error);
                });
            }
        }
    }
</script>

<style scoped>

</style>
