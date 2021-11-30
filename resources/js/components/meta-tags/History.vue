<template>

    <div class="row">
        <div class="col-md-12">

            <meta-filter :seen="seenCard" :metaTags="history"></meta-filter>

            <div id="accordion">

                <div class="card" v-for="(item, index) in history" v-show="!seenCard.length || seenCard[index] === 1">
                    <div class="card-header card-header-accordion">
                        <h4 class="card-title">
                            <a class="d-block w-100 collapsed accordion-title" data-toggle="collapse" :href="'#collapse' + index" aria-expanded="false">
                                <i class="expandable-accordion-caret fas fa-caret-right fa-fw"></i> {{ item.title }}
                            </a>
                        </h4>

                        <div class="card-tools">
                            <span v-for="error_badge in item.error.badge" v-if="error_badge.length" v-html="error_badge.join('')"></span>
                        </div>
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
            history: [Object, Array]
        },
        data() {
            return {

                seenCard: [],
            }
        }
    }
</script>

<style scoped>

</style>
