<template>

    <div>

        <div class="row mb-4">
            <div class="col-md-6">

                <form @submit.prevent="ShowHttpResponse">
                    <div class="form-group">
                        <label>{{ textTitle }}</label>
                        <textarea type="text" class="form-control" rows="10" v-model="urls"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ timeoutTitle }}</label>
                        <input type="number" min="1" class="form-control" v-model="time">
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="submit" class="btn btn-secondary" :value="submit">
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card" v-if="cardDisplay">
                    <div class="card-header">
                        <span v-for="(item, index) in codes" class="mr-2">
                            <strong>{{ index }}</strong>:
                            <span class="right badge badge-info">{{ item.length }}</span>
                        </span>
                    </div>
                    <div class="card-body">

                        <table class="table dataTable table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th style="width: 50px">{{ more }}</th>
                                <th>{{ urlTitle }}</th>
                                <th class="sorting" @click.prevent="Sorting">{{ codeTitle }}</th>
                                <th style="width: 40px"></th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="item in items" :key="item.id">
                                <td>{{ item.id + 1 }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm rounded" data-toggle="dropdown" data-offset="-52" aria-expanded="false">
                                            <i class="fas fa-bars"></i>
                                        </button>
                                        <div class="dropdown-menu" role="menu" style="">
                                            <a :href="`?url=${item.url}`" target="_blank" class="dropdown-item">{{ openNewPage }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ item.url }}</td>
                                <td>{{ item.code }}</td>
                                <td>
                                    <span v-if="item.status" class="badge bg-success p-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <span v-else class="badge bg-danger p-2">
                                        <i class="far fa-times-circle"></i>
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ResponseHttpCode",
    props: {
        submit: {
            type: String
        },
        urlTitle: {
            type: String
        },
        codeTitle: {
            type: String
        },
        textTitle: {
            type: String
        },
        timeoutTitle: {
            type: String
        },
        exportBtn: {
            type: String
        },
        openNewPage: {
            type: String
        },
        more: {
            type: String
        },
    },
    data() {
        return {
            time: 1000,
            order: 'desc',
            urls: '',
            arUrls: [],
            items: [],
            codes: {},
            table: {},
        }
    },
    computed: {

        cardDisplay: function () {
            return this.items.length > 0;
        }
    },
    methods: {
        ShowHttpResponse() {
            var app = this;

            app.items = [];
            app.codes = {};

            app.StringToArray();

            app.arUrls.forEach((element, i) => {
                if (i >= 500)
                    return false;

                setTimeout(() => {
                    app.HttpRequest(element, i);
                }, i * app.time);
            });
        },

        Sorting() {
            if (this.order === 'desc')
                this.order = 'asc';
            else
                this.order = 'desc';

            this.items = _.orderBy(this.items, 'code', this.order);
        },

        HttpRequest(url, i) {
            var app = this;

            axios.get('/http-headers', {
                params: {
                    url: url,
                    http: 1
                }
            }).then(function (response) {
                let code = response.data;
                let status = response.data === 200;

                if (app.codes[response.data] === undefined)
                    app.codes[response.data] = [];

                app.codes[response.data].push(response.data);

                app.items.push({id: i, url: url, code: code, status: status});
            });
        },

        StringToArray() {
            if (this.urls.length)
                this.arUrls = _.compact(this.urls.split(/[\r\n]+/));
        }
    },
    updated() {
        this.$nextTick(function () {
            let table = $(this.$el).find('.table');

            if(table.length > 0 && (this.arUrls.length === this.items.length)){

                this.table = table.DataTable({
                    destroy: true,
                    dom: 'BtB',
                    ordering: false,
                    searching: false,
                    paging: false,
                    buttons: [
                        {
                            extend: 'csv',
                            className: 'btn btn-default btn-sm',
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-default btn-sm',
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-default btn-sm',
                        },
                        {
                            extend: 'copy',
                            className: 'btn btn-default btn-sm',
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-default btn-sm',
                        },
                    ],
                });

                this.table.buttons().container().addClass('mailbox-controls pl-0');
            }
        });
    }
}
</script>
