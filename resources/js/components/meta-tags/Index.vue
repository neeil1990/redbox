<template>
    <div>

        <div class="row mb-4">
            <div class="col-md-6">

                <form @submit.prevent="onSubmitMetaTags">
                    <div class="form-group">
                        <label>{{ lang.check_url }}</label>
                        <textarea type="text" class="form-control" rows="10" v-model="url"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ lang.timeout_request }}</label>
                        <input type="number" min="1" class="form-control" v-model="time">
                    </div>

                    <div class="row" v-for="len in length">
                        <div class="col-sm-6">
                            <!-- text input -->
                            <div class="form-group">
                                <label>{{ lang.length_word }} {{ len.name }}</label>
                                <input type="number" class="form-control" :placeholder="lang.min" v-model.lazy="len.input.min">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <input type="number" class="form-control" :placeholder="lang.max" v-model.lazy="len.input.max">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" class="btn btn-secondary" :value="lang.send">
                        </div>
                    </div>

                </form>

            </div>
        </div>

        <div class="row">
            <div class="col-md-12">

                <div class="card" v-if="result.length">

                    <div class="card-header">
                        <h3 class="card-title">{{ lang.check_url }}</h3>
                    </div>

                    <div class="progress">
                        <div class="progress-bar" role="progressbar" :style="'width: '+ loading +'%;'" :aria-valuenow="loading" aria-valuemin="0" aria-valuemax="100">
                            <span v-if="loading === 100">{{ lang.done }}</span>
                            <span v-else>{{ loading }}%</span>
                        </div>
                    </div>

                    <div class="card-body">

                        <meta-filter :seen="seenCard" :metaTags="result" :lang="lang"></meta-filter>

                        <div id="accordion">
                            <div class="card" v-for="(url, index) in result" v-show="!seenCard.length || seenCard[index] === 1">
                                    <div class="card-header card-header-accordion">
                                        <h4 class="card-title">
                                            <a class="d-block w-100 collapsed accordion-title" data-toggle="collapse" :href="'#collapse' + index" aria-expanded="false">
                                                <i class="expandable-accordion-caret fas fa-caret-right fa-fw"></i> {{ url.title }}
                                            </a>
                                        </h4>

                                        <div class="card-tools">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </button>

                                                <div class="dropdown-menu dropdown-menu-right" role="menu" style="">
                                                    <a :href="url.title" target="_blank" class="dropdown-item">
                                                        <i class="fas fa-external-link-alt"></i>
                                                        {{ lang.go_to_site }}
                                                    </a>
                                                    <a href="#" class="dropdown-item" @click.prevent="Analyzer(url.title)">
                                                        <i class="fas fa-chart-pie"></i>
                                                        {{ lang.text_analysis }}
                                                    </a>
                                                </div>
                                            </div>

                                            <span v-for="error_badge in url.error.badge" v-if="error_badge.length" v-html="error_badge.join('')"></span>
                                        </div>
                                    </div>

                                    <div :id="'collapse' + index" class="collapse" data-parent="#accordion" style="">
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th style="width: 150px;">{{ lang.tag }}</th>
                                                    <th>{{ lang.content }}</th>
                                                    <th style="width: 40px">{{ lang.count }}</th>
                                                    <th style="width: 150px">{{ lang.main_problems }}</th>
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
                                                    <td v-html="url.error.main[tag].join(' <br />')"></td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <div class="row" v-if="FormShow">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#ProjectModalForm" >{{ lang.save_as_project }}</button>
                                <base-modal-form v-on:close-modal-form="CloseModalFormMetaTags" target="ProjectModalForm" method="post" request="/meta-tags" :data="result" :links="url" :lang="lang"></base-modal-form>
                                <button type="button" class="btn btn-info" @click.prevent="Export">Экспорт</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row" v-if="metas.length">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ lang.projects }}</h3>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped projects">
                            <thead>
                            <tr>
                                <th style="width: 1%">{{ lang.id }}</th>
                                <th style="width: 15%">{{ lang.name }}</th>
                                <th style="width: 10%">{{ lang.period }}</th>
                                <th style="width: 10%">{{ lang.timeout }}</th>
                                <th style="width: 25%">{{ lang.link }}</th>
                                <th style="width: 9%">{{ lang.status }}</th>
                                <th style="width: 30%"></th>
                            </tr>
                            </thead>

                            <tbody>
                                <tr v-for="meta in metas" :key="meta.id">
                                    <td>{{ meta.id }}</td>
                                    <td>{{ meta.name }}</td>
                                    <td>
                                        <select class="form-control" v-model.number="meta.period" @change.prevent="onSubmitMetaTagsEditField(meta)">
                                            <option v-for="option in options" :value="option.value">{{option.text}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control" min="1" v-model.number="meta.timeout" @keyup.prevent="onSubmitMetaTagsEditField(meta)">
                                            <div class="input-group-append">
                                                <span class="input-group-text">ms.</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <textarea class="form-control" v-text="meta.links"></textarea>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                <input type="checkbox" class="custom-control-input" :id="'customSwitchStatus' + meta.id" v-model="meta.status" @change.prevent="onSubmitMetaTagsEditField(meta)">
                                                <label class="custom-control-label" :for="'customSwitchStatus' + meta.id">{{ lang.off }} / {{ lang.on }}</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="project-actions text-right">

                                        <a class="btn btn-info btn-sm" target="_blank" :href="'/meta-tags/histories/' + meta.id">
                                            <i class="fas fa-list"></i>
                                            {{ lang.history }}
                                        </a>
                                        <a class="btn btn-info btn-sm" href="#" @click.prevent="StartMetaTags(meta)">
                                            <i class="fas fa-play-circle"></i>
                                            {{ lang.start }}
                                        </a>
                                        <a class="btn btn-info btn-sm" href="#" data-toggle="modal" data-target="#ProjectModalFormEdit" @click.prevent="onSubmitMetaTagsEdit(meta)">
                                            <i class="fas fa-edit"></i>
                                            {{ lang.edit }}
                                        </a>
                                        <a class="btn btn-info btn-sm" @click.prevent="DeleteMetaTags(meta.id)">
                                            <i class="fas fa-trash-alt"></i>
                                            {{ lang.delete }}
                                        </a>

                                    </td>
                                </tr>

                            </tbody>

                            <base-modal-form v-on:close-modal-form="CloseModalFormMetaTags"
                                             target="ProjectModalFormEdit"
                                             method="patch"
                                             :values="value"
                                             :request="'/meta-tags/' + request"
                                             :lang="lang"
                            ></base-modal-form>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import MetaFilter from './Filter'

    export default {
        name: "MetaTags",
        components: {
            MetaFilter
        },
        props: {
            meta: {
                type: [Object, Array]
            },
            lang: {
                type: [Object, Array]
            }
        },
        created() {
            this.metas = this.meta;

            var app = this;
            axios.get('/meta-tags/getTariffMetaTagsPages')
                .then(function (response) {
                    app.TariffMetaTagsPages = response.data;
                });
        },
        data() {
            return {
                TariffMetaTagsPages: {},
                loading: 0,
                metas: [],
                value: {},
                request: null,
                FormShow: false,
                url: '',
                time: 500,
                length: [
                    {id: 'title', name: this.lang.title, input: {min: null, max: null}},
                    {id: 'description', name: this.lang.description, input: {min: null, max: null}},
                    {id: 'keywords', name: this.lang.keywords, input: {min: null, max: null}},
                ],
                result: [],
                seenCard: [],
                options: [
                    {value: 0, text: 'manual'},
                    {value: 6, text: '6 часов'},
                    {value: 12, text: '12 часов'},
                    {value: 24, text: '24 часов'},
                ],
                startBtnProjectId: null
            }
        },
        computed: {

        },
        watch:{
            result: function(val){
                let url = this.StringAsObj(this.url);
                this.FormShow = (url.length === val.length);

                this.loading = Math.ceil(val.length / url.length * 100);
            },
            loading: function(val){

                if(this.startBtnProjectId && val === 100 && this.FormShow){

                    axios.request({
                        url: '/meta-tags/histories/' + this.startBtnProjectId,
                        method: 'patch',
                        data: { histories: this.result }
                    }).then(function(response){

                        if(response.statusText === "OK");
                            toastr.success('История добавлена');

                    }).catch(function (error) {

                        console.log(error);
                    });

                    this.startBtnProjectId = null;
                }
            }
        },
        methods: {
            BreakException(message) {
                this.message = message;
                this.name = "Исключение, определённое пользователем";
            },
            Analyzer(link) {
                var form = document.createElement("form");
                form.action = "/text-analyzer";
                form.method = "POST";
                form.target = "_blank";

                var _token = document.createElement("input");
                _token.setAttribute("type", "text");
                _token.setAttribute("name", "_token");
                _token.setAttribute("value", $('meta[name="csrf-token"]').attr('content'));
                form.appendChild(_token);

                var type = document.createElement("input");
                type.setAttribute("type", "text");
                type.setAttribute("name", "type");
                type.setAttribute("value", "url");
                form.appendChild(type);

                var text = document.createElement("input");
                text.setAttribute("type", "text");
                text.setAttribute("name", "text");
                text.setAttribute("value", link);
                form.appendChild(text);

                document.body.appendChild(form);

                form.submit();

                form.remove();
            },
            StartMetaTags(meta)
            {
                $("html, body").stop().animate({scrollTop : 200}, 500, 'swing');

                this.url = meta.links;
                this.time = meta.timeout;
                this.seenCard = [];

                _.forEach(this.length, function(value) {
                    value.input.min = meta[value.id + '_min'];
                    value.input.max = meta[value.id + '_max'];
                });

                this.onSubmitMetaTags();

                this.startBtnProjectId = meta.id;
            },

            onSubmitMetaTagsEditField(meta)
            {

                axios.request({
                    url: '/meta-tags/' + meta.id,
                    method: 'patch',
                    data: meta
                }).then(function(response){

                    if(response.statusText === "OK");
                        toastr.success('Успешно изменено');

                }).catch(function (error) {

                    console.log(error);
                });
            },

            onSubmitMetaTagsEdit(meta)
            {

                this.request = meta.id;
                this.value = meta;
            },

            onSubmitMetaTags()
            {
                let url = '';

                if(this.url.length){
                    url = this.StringAsObj(this.url);

                    if(url.length > this.TariffMetaTagsPages.value){
                        toastr.error(this.TariffMetaTagsPages.message);
                        this.url = _.join(_.slice(url, 0, this.TariffMetaTagsPages.value), '\r\n');

                        return false;
                    }

                    this.result = [];
                    url.forEach((element, i) => {
                        setTimeout(() => {
                            this.HttpRequest(element, i);
                        }, i * this.time);
                    });
                } else{
                    this.url = '';
                }
            },

            HttpRequest(url, i)
            {
                var app = this;

                axios.post('/meta-tags/get', {
                    url: url,
                    length: app.length,
                }).then(function (response) {
                    app.result.push(response.data);
                }).catch(function (error) {
                    toastr.error(error.response.data.message);
                });
            },

            DeleteMetaTags(id)
            {

                let del = confirm("Your sure?");
                if(del){
                    let idx = _.findIndex(this.metas, function(o) { return o.id === id; });

                    axios.delete('/meta-tags/' + id);
                    this.metas.splice(idx, 1);

                    toastr.info('Успешно удалено');
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

            StringAsObj(str)
            {
                return _.compact(str.split(/[\r\n]+/));
            },
            Export()
            {
                axios.request({
                    url: '/meta-tags/export',
                    method: 'post',
                    responseType: 'blob',
                    data: {
                        result: this.result
                    }
                }).then(function(response){
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');

                    link.href = url;

                    const contentDisposition = response.headers['content-disposition'];
                    const fileNameMatch = contentDisposition.match(/filename="?(.+)"?/);

                    link.setAttribute('download', fileNameMatch[1]);
                    document.body.appendChild(link);
                    link.click();

                    link.remove();
                    window.URL.revokeObjectURL(url);
                });
            }
        }
    }
</script>

<style scoped>

    .list-item {
        display: inline-block;
        margin-right: 10px;
    }
    .list-enter-active, .list-leave-active {
        transition: all 1s;
    }
    .list-enter, .list-leave-to /* .list-leave-active до версии 2.1.8 */ {
        opacity: 0;
        transform: translateY(30px);
    }

</style>
