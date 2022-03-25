@component('component.card', ['title' =>  __('Relevance analysis') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            .ui_tooltip_content {
                font-weight: normal;
            }

            .bg-warning-elem {
                background-color: #f5e2aa !important;
            }

            #unigramTBody > tr > td:nth-child(1) {
                text-align: center;
            }

            #app > div > div > div.card-body > div.d-flex.flex-column > div > button.btn.btn-secondary.col-2 > span > i {
                color: #fffdfd !important;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
            }

            #unigramTBody > tr > td:nth-child(8),
            #unigramTBody > tr > td:nth-child(10),
            #unigramTBody > tr > td:nth-child(12) {
                background: #ebf0f5;
            }

            .ui_tooltip.__left, .ui_tooltip.__right {
                width: auto;
            }
        </style>
    @endslot
    <div id="toast-container" class="toast-top-right error-message analyse" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message">{{ __('An error has occurred, repeat the request.') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right error-message empty" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message">{{ __("The 'keyword' and 'landing page' fields should not be empty") }}</div>
        </div>
    </div>
    <div class="col-5 pb-3">
        <div class="form-group required">
            <label>{{ __('Keyword') }}</label>
            {!! Form::text("phrase", null ,["class" => "form-control phrase", "required"]) !!}
        </div>

        <div class="form-group required">
            <label>{{ __('Your landing page') }}</label>
            {!! Form::text("link", null ,["class" => "form-control link", "required"]) !!}
        </div>

        <div class="form-group required">
            <label>{{ __('Ignored domains') }}</label>
            {!! Form::textarea("ignoredDomains",
                "2gis.ru\n".
                "aliexpress.com\n".
                "AliExpress.ru\n".
                "auto.ru\n".
                "avito.ru\n".
                "banki.ru\n".
                "beru.ru\n".
                "blizko.ru\n".
                "cataloxy.ru\n".
                "deal.by\n".
                "domclick.ru\n".
                "ebay.com\n".
                "edadeal.ru\n".
                "e-katalog.ru\n".
                "hh.ru\n".
                "instagram.com\n".
                "irecommend.ru\n".
                "irr.ru\n".
                "leroymerlin.ru\n".
                "market.yandex.ru\n".
                "mvideo.ru\n".
                "onliner.by\n".
                "otzovik.com\n".
                "ozon.ru\n".
                "pandao.ru\n".
                "price.ru\n".
                "prodoctorov.ru\n".
                "profi.ru\n".
                "pulscen.ru\n".
                "quto.ru\n".
                "rambler.ru\n".
                "regmarkets.ru\n".
                "satom.ru\n".
                "shop.by\n".
                "sravni.ru\n".
                "tiu.ru\n".
                "toshop.ru\n".
                "wikipedia.org\n".
                "wildberries.ru\n".
                "yandex.ru\n".
                "yell.ru\n".
                "zoon.ru\n" ,["class" => "form-control ignoredDomains"] ) !!}
        </div>
    </div>
    <div class="d-flex flex-column">
        <div class="btn-group col-lg-3 col-md-5 mb-2">
            <button class="btn btn-secondary" id="test-analyse">
                {{ __('Full analysis') }}
            </button>
        </div>
    </div>
    <div class="pb-3 clouds">
        <div class="d-flex flex-column pb-3">
            <div style="display:flex; flex-wrap: wrap" id="clouds">
            </div>
        </div>
    </div>
    <div class="pb-3 sites" style="display: none">
        <h3>{{ __('Analyzed sites') }}</h3>
        <table id="scaned-sites" class="table table-bordered table-hover dataTable dtr-inline">
            <thead>
            <tr role="row">
                <th>{{ __('Position in the top') }}</th>
                <th>{{ __('Domain') }}</th>
                <th>{{ __('Coverage') }}</th>
                <th>{{ __('Density') }}</th>
                <th>{{ __('Result') }}</th>
            </tr>
            </thead>
            <tbody id="scaned-sites-tbody">
            </tbody>
        </table>
    </div>
    @slot('js')
        <script defer src="{{ asset('plugins/canvasjs/js/canvasjs.js') }}"></script>
        <script defer src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script defer src="{{ asset('plugins/relevance-analysis/scripts/renderClouds.js') }}"></script>
        <script defer src="{{ asset('plugins/relevance-analysis/scripts/renderScanedSitesList.js') }}"></script>
        <script>
            $('#test-analyse').click(() => {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('test.relevance') }}",
                    data: {
                        link: $('.form-control.link').val(),
                        separator: $('#separator').val(),
                        phrase: $('.form-control.phrase').val(),
                        noIndex: $('#switchNoindex').is(':checked'),
                        listWords: $('.form-control.listWords').val(),
                        count: $('.custom-select.rounded-0.count').val(),
                        region: $('.custom-select.rounded-0.region').val(),
                        hiddenText: $('#switchAltAndTitle').is(':checked'),
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        ignoredDomains: $('.form-control.ignoredDomains').val(),
                        switchMyListWords: $('#switchMyListWords').is(':checked'),
                        conjunctionsPrepositionsPronouns: $('#switchConjunctionsPrepositionsPronouns').is(':checked')
                    },
                    beforeSend: function () {
                        $('#test-analyse').prop("disabled", true);
                    },
                    success: function (response) {
                        console.log(response)
                        successRequest(response)
                    },
                    error: function () {
                        errorRequest()
                    }
                });
            })

            function successRequest(response) {
                renderScanedSitesList(response.sites);
                $("#test-analyse").prop("disabled", false);
                var iterator = 1
                $.each(response.clouds, function (key, value) {
                    let item = arrayToObj(value)
                    $('#clouds').append(
                        "<div style='width: 50%;'>" +
                        "<span>" + key + "</span>" +
                        "<div id='cloud" + iterator + "' style='height: 400px; width: 100%; padding-top: 10px; padding-bottom: 10px'></div>" +
                        "</div>"
                    )
                    $("#cloud" + iterator).jQCloud(item)
                    iterator++
                });
            }

            function errorRequest() {
                $("#full-analyse").prop("disabled", false);
                $("#repeat-main-page-analyse").prop("disabled", true);
                $("#repeat-relevance-analyse").prop("disabled", true);
                $('.toast-top-right.error-message.analyse').show(300)
                setTimeout(() => {
                    $('.toast-top-right.error-message.analyse').hide(300)
                }, 5000)
            }

            function removeAllRenderElements() {
                $(".generated-cloud").html("")
                $('.render').remove();
                $('.pb-3.text').hide()
                $('.pb-3.unigram').hide()
                $('.pb-3.sites').hide()
                $('.clouds').hide()
            }

            function arrayToObj(array) {
                let length = array.count
                let a = [], b = {};
                for (let i = 0; i < length; i++) {
                    b = array[i]
                    a.push(b);
                }
                return a;
            }
        </script>
    @endslot
@endcomponent
