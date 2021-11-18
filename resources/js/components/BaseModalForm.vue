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
                            <label class="col-form-label">Частота проверок:</label>

                            <div v-for="radio in radios" class="custom-control custom-radio">
                                <input v-model.number="period" class="custom-control-input" type="radio" :id="'Radio' + target + radio.value" :value="radio.value">
                                <label :for="'Radio' + target + radio.value" class="custom-control-label">Интервал проверки каждые {{ radio.text }}</label>
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
                name: 'My project',
                period: 6,
                link: '',
                timeout: 500,
                radios: [
                    {value: 6, text: '6 часов'},
                    {value: 12, text: '12 часов'},
                    {value: 24, text: '24 часов'},
                ],
            }
        },
        watch: {
            values: function(val){

                this.name = val.name;
                this.period = val.period;
                this.link = val.links;
                this.timeout = val.timeout;
            }
        },
        methods: {

            OnSubmitMetaForm() {
                var app = this;

                axios.request({
                    url: app.request,
                    method: app.method,
                    data: {
                        name: app.name,
                        period: app.period,
                        links: app.link,
                        timeout: app.timeout,
                    }
                }).then(function(response){

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
