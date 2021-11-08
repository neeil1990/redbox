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
        data() {
            return {
                url: '',
                time: 500,
                result: [],
                error: []
            }
        },
        methods: {
            onSubmitMetaTags(){
                let url = '';

                if(this.url.length){
                    url = _.compact(this.url.split(/[\r\n]+/));
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

            }
        }
    }
</script>

<style scoped>

</style>
