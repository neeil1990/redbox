<template>
    <div>

        <div class="row mb-4">
            <div class="col-md-6">

                <form @submit.prevent="onSubmitMetaTags">
                    <div class="form-group">
                        <label>Check URL</label>
                        <textarea type="text" class="form-control" rows="10" v-model="url"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Timeout request</label>
                        <input type="number" min="1" class="form-control" v-model="time">
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" class="btn btn-secondary" value="submit">
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="card" v-if="result.length">

                    <div class="card-header">
                        <h3 class="card-title">Check URL</h3>
                    </div>

                    <div class="progress">
                        <div class="progress-bar" role="progressbar" :style="'width: '+ loading +'%;'" :aria-valuenow="loading" aria-valuemin="0" aria-valuemax="100">
                            <span v-if="loading === 100">Готово</span>
                            <span v-else>{{ loading }}%</span>
                        </div>
                    </div>

                    <div class="card-body">

                        <div id="accordion">

                            <div class="card" v-for="(url, index) in result">
                                <div class="card-header card-header-accordion">
                                    <h4 class="card-title">
                                        <a class="d-block w-100 collapsed accordion-title" data-toggle="collapse" :href="'#collapse' + index" aria-expanded="false">
                                            <i class="expandable-accordion-caret fas fa-caret-right fa-fw"></i> {{ url.title }}
                                        </a>
                                    </h4>
                                    <base-tools :data="url.data"></base-tools>
                                </div>

                                <div :id="'collapse' + index" class="collapse" data-parent="#accordion" style="">
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Tag</th>
                                                <th>Content</th>
                                                <th style="width: 40px">Count</th>
                                                <th style="width: 150px">Main problems</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                                <tr v-for="(item, tag) in url.data">
                                                    <td><span class="badge badge-success">< {{ tag }} ></span></td>
                                                    <td>
                                                        <span v-if="item.length"><textarea class="form-control">{{ item.join( ', \r\n' ) }}</textarea></span>
                                                        <span v-else class="badge badge-danger">{{ item }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning">{{ item.length }}</span>
                                                    </td>
                                                    <td>
                                                        <small v-if="item.length > 1 && (tag === 'title' || tag === 'description' || tag === 'canonical' || tag === 'h1')">
                                                            Дублирующийся тег <span class="badge badge-danger">< {{tag}} ></span> Проверьте страницу и оставьте 1 тег
                                                        </small>
                                                        <span v-else class="badge badge-success">Без проблем</span>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row" v-if="FormShow">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#ProjectModalForm" >Сохранить как проект</button>
                                <base-modal-form v-on:close-modal-form="CloseModalFormMetaTags" target="ProjectModalForm" method="post" request="/meta-tags" :data="result" :links="url"></base-modal-form>
                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->

                </div>
            </div>

        </div>

        <div class="row" v-if="metas.length">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Проекты</h3>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th style="width: 1%">ID</th>
                                <th style="width: 20%">name</th>
                                <th style="width: 20%">period</th>
                                <th style="width: 30%">link</th>
                                <th style="width: 20%"></th>
                            </tr>
                            </thead>

                            <tbody>

                                <tr v-for="meta in metas" :key="meta.id">
                                    <td>{{ meta.id }}</td>
                                    <td>{{ meta.name }}</td>
                                    <td>{{ meta.period }}</td>
                                    <td>
                                        <textarea class="form-control" v-text="meta.links"></textarea>
                                    </td>

                                    <td class="project-actions text-right">

                                        <a class="btn btn-info btn-sm" href="#" @click.prevent="StartMetaTags(meta)">
                                            <i class="fas fa-play-circle"></i>
                                            Start
                                        </a>
                                        <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#ProjectModalFormEdit" @click.prevent="onSubmitMetaTagsEdit(meta)">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <a class="btn btn-info btn-sm" @click.prevent="DeleteMetaTags(meta.id)">
                                            <i class="fas fa-trash-alt"></i>
                                            Delete
                                        </a>

                                    </td>
                                </tr>

                            </tbody>

                            <base-modal-form v-on:close-modal-form="CloseModalFormMetaTags" target="ProjectModalFormEdit" method="patch" :values="value" :request="'/meta-tags/' + request" ></base-modal-form>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MetaTags",
        props: {
            meta: {
                type: [Object, Array]
            }
        },
        created() {

            this.metas = this.meta;
        },
        data() {
            return {
                loading: 0,
                metas: [],
                value: {},
                request: null,
                FormShow: false,
                url: '',
                time: 500,
                result: [],
                error: []
            }
        },
        watch:{
            result: function(val){
                let url = this.StringAsObj(this.url);
                this.FormShow = (url.length === val.length);

                this.loading = Math.ceil(val.length / url.length * 100);
            }
        },
        methods: {
            StartMetaTags(meta) {
                $("html, body").stop().animate({scrollTop : 200}, 500, 'swing');

                this.url = meta.links;
                this.time = meta.timeout;

                this.onSubmitMetaTags();
            },
            onSubmitMetaTagsEdit(meta) {
                this.request = meta.id;
                this.value = meta;
            },
            onSubmitMetaTags(){
                let url = '';

                if(this.url.length){
                    url = this.StringAsObj(this.url);
                    this.result = [];
                    this.error = [];

                    url.forEach((element,i) => {

                        setTimeout(() => {
                            this.HttpRequest(element, i);
                        }, i * this.time);
                    });

                } else
                    this.url = '';

            },

            HttpRequest(url, i) {
                var app = this;

                axios.get('/meta-tags', {
                    params: {
                        url: url
                    }
                }).then(function (response) {

                    app.result.push(response.data);
                }).catch(function (error) {

                    console.log(error.response.status);
                });
            },

            DeleteMetaTags(id) {

                let del = confirm("Your sure?");
                if(del){
                    let idx = _.findIndex(this.metas, function(o) { return o.id === id; });

                    axios.delete('/meta-tags/' + id);
                    this.metas.splice(idx, 1);
                }
            },

            CloseModalFormMetaTags: function(response) {

                let idx = _.findIndex(this.metas, function(o) { return o.id === response.data.id; });

                if(idx < 0){

                    this.metas.push(response.data);
                }else{

                    _.merge(this.metas[idx], response.data);
                }

                $('.modal').modal('hide');
            },

            StringAsObj(str){
                return _.compact(str.split(/[\r\n]+/));
            }
        }
    }
</script>

<style scoped>

</style>
