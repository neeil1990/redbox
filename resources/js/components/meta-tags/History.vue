<template>

    <div class="row">
        <div class="col-md-12">

            <meta-filter :seen="seenCard" :metaTags="history" :lang="lang"></meta-filter>

            <div id="accordion">

                <div class="card" v-for="(item, index) in history" v-show="!seenCard.length || seenCard[index] === 1">
                    <div class="card-header card-header-accordion">
                        <h4 class="card-title">
                            <a class="d-block w-100 collapsed accordion-title" data-toggle="collapse" :href="'#collapse' + index" aria-expanded="false">
                                <i class="expandable-accordion-caret fas fa-caret-right fa-fw"></i> {{ item.title }}
                            </a>
                        </h4>

                        <div class="card-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-external-link-alt"></i>
                                </button>

                                <div class="dropdown-menu dropdown-menu-right" role="menu" style="">
                                    <a :href="item.title" target="_blank" class="dropdown-item">
                                        <i class="fas fa-external-link-alt"></i>
                                        {{ lang.go_to_site }}
                                    </a>
                                    <a href="#" class="dropdown-item" @click.prevent="Analyzer(item.title)">
                                        <i class="fas fa-chart-pie"></i>
                                        {{ lang.text_analysis }}
                                    </a>
                                </div>
                            </div>

                            <span v-for="error_badge in item.error.badge" v-if="error_badge.length" v-html="error_badge.join('')"></span>
                        </div>
                    </div>

                    <div :id="'collapse' + index" class="collapse" data-parent="#accordion" style="">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ lang.tag }}</th>
                                        <th>{{ lang.content }}</th>
                                        <th style="width: 40px">{{ lang.count }}</th>
                                        <th style="width: 150px">{{ lang.main_problems }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(value, tag) in item.data">
                                        <td><span class="badge badge-success">< {{ tag }} ></span></td>
                                        <td>
                                            <span v-if="value.length"><textarea class="form-control">{{ value.join( ', \r\n' ) }}</textarea></span>
                                            <span v-else class="badge badge-danger">{{ value }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ value.length }}</span>
                                        </td>
                                        <td v-html="item.error.main[tag].join(' <br />')"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</template>

<script>
    import MetaFilter from './Filter'

    export default {
        name: "MetaTagsHistory",
        components: {
            MetaFilter
        },
        props: {
            history: [Object, Array],
            lang: [Object, Array],
        },
        data() {
            return {
                seenCard: [],
            }
        },
        methods: {
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
        }
    }
</script>

<style scoped>

</style>
