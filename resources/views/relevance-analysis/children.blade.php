@component('component.card', ['title' =>  __('Relevance analysis') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
    @endslot
    <div class="pb-3 unigram">
        <h2>Наиболее используемые слова</h2>
        <table id="unigram-children" class="table table-bordered table-hover dataTable dtr-inline"
               style="width: 100% !important;">
            <thead>
            <tr>
                <th class="font-weight-normal text-muted">{{ __('Ranges for filtering the table') }}</th>
                <th>
                    <div style="width: 90px">
                        <input class="w-100" type="number" name="minTF" id="minTF" placeholder="min">
                        <input class="w-100" type="number" name="maxTF" id="maxTF" placeholder="max">
                    </div>
                </th>
                <th>
                    <div style="width: 90px">
                        <input class="w-100" type="number" name="minIdf" id="minIdf" placeholder="min">
                        <input class="w-100" type="number" name="maxIdf" id="maxIdf" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minInter" id="minInter" placeholder="min">
                        <input class="w-100" type="number" name="maxInter" id="maxInter" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minReSpam" id="minReSpam" placeholder="min">
                        <input class="w-100" type="number" name="maxReSpam" id="maxReSpam" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minAVG" id="minAVG" placeholder="min">
                        <input class="w-100" type="number" name="maxAVG" id="maxAVG" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minAVGText" id="minAVGText" placeholder="min">
                        <input class="w-100" type="number" name="maxAVGText" id="maxAVGText" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minInYourPage" id="minInYourPage" placeholder="min">
                        <input class="w-100" type="number" name="maxInYourPage" id="maxInYourPage" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minTextIYP" id="minTextIYP" placeholder="min">
                        <input class="w-100" type="number" name="maxTextIYP" id="maxTextIYP" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minAVGLink" id="minAVGLink" placeholder="min">
                        <input class="w-100" type="number" name="maxAVGLink" id="maxAVGLink" placeholder="max">
                    </div>
                </th>
                <th>
                    <div>
                        <input class="w-100" type="number" name="minLinkIYP" id="minLinkIYP" placeholder="min">
                        <input class="w-100" type="number" name="maxLinkIYP" id="maxLinkIYP" placeholder="max">
                    </div>
                </th>
            </tr>
            <tr style="position: relative; z-index: 100">
                <th>
                    {{ __('Words') }}
                    <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                    <span class="ui_tooltip __right">
                        <span class="ui_tooltip_content" style="text-align: right">
                            {{ __('Words and their word forms that are present on competitors websites.') }}
                        </span>
                    </span>
                </span>
                </th>
                <th>tf
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The weight of the phrase relative to others.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>idf
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The weight of the phrase relative to others.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>
                    {{ __('Intersection') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The number of sites in which the word is present.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>{{ __('Re - spam') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The maximum number of repetitions found on the competitors website.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>{{ __('Average number of repetitions in the text and links') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The average value of the number of repetitions in the text and links of your competitors.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>{{ __('The total number of repetitions in the text and links') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The total number of repetitions on your page in links and text.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>{{ __('Average number of repetitions in the text') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The average value of the number of repetitions in the text of your competitors.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>{{ __('Number of repetitions in text') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The number of repetitions in the text on your page') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>{{ __('Average number of repetitions in links') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The average value of the number of repetitions in the links of your competitors.') }}
                            </span>
                        </span>
                    </span>
                </th>
                <th>{{ __('Number of repetitions in links') }}
                    <span class="__helper-link ui_tooltip_w">
                        <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __left">
                            <span class="ui_tooltip_content">
                                {{ __('The number of repetitions in the links on your page.') }}
                            </span>
                        </span>
                    </span>
                </th>
            </tr>
            </thead>
            <tbody id="unigramTBody">
            @foreach($array as $key => $item)
                <tr>
                    <td style="text-align: left">{{ $key }}</td>
                    <td>{{ number_format($item['tf'], 5) }}</td>
                    <td>{{ $item['idf'] }}</td>
                    <td>
                        {{ $item['numberOccurrences'] }}
                    </td>
                    <td>{{ $item['reSpam'] }}</td>
                    <td>{{ $item['avgInTotalCompetitors'] }}</td>
                    <td @if($item['totalRepeatMainPage'] == 0)class="bg-warning-elem" @endif>{{ $item['totalRepeatMainPage'] }}</td>
                    <td>{{ $item['avgInText'] }}</td>
                    <td @if($item['repeatInTextMainPage'] == 0)class="bg-warning-elem" @endif>{{ $item['repeatInTextMainPage'] }}</td>
                    <td>{{ $item['avgInLink'] }}</td>
                    <td @if($item['repeatInLinkMainPage'] == 0)class="bg-warning-elem" @endif>{{ $item['repeatInLinkMainPage'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @slot('js')
        <script defer src="{{ asset('plugins/canvasjs/js/canvasjs.js') }}"></script>
        <script defer src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script defer src="{{ asset('plugins/relevance-analysis/scripts/renderUnigramTable.js') }}"></script>
        <script defer src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script defer src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script defer src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                var table = $('#unigram-children').DataTable({
                    "bSort":false,
                    "pageLength": 50,
                    "searching": true,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]
                });
                $('#unigram-children').wrap("<div style='width: 100%; overflow-x: scroll;'></div>")
                $('.buttons-html5').addClass('btn btn-secondary')


                $('#minTF, #maxTF').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxTF = parseFloat($('#maxTF').val());
                            var minTF = parseFloat($('#minTF').val());
                            var TF = parseFloat(data[1]);
                            if ((isNaN(minTF) && isNaN(maxTF)) ||
                                (isNaN(minTF) && TF <= maxTF) ||
                                (minTF <= TF && isNaN(maxTF)) ||
                                (minTF <= TF && TF <= maxTF)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minIdf, #maxIdf').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxIdf = parseFloat($('#maxIdf').val());
                            var minIdf = parseFloat($('#minIdf').val());
                            var IDF = parseFloat(data[2]);
                            if (
                                (isNaN(minIdf) && isNaN(maxIdf)) ||
                                (isNaN(minIdf) && IDF <= maxIdf) ||
                                (minIdf <= IDF && isNaN(maxIdf)) ||
                                (minIdf <= IDF && IDF <= maxIdf)
                            ) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minInter, #maxInter').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxInter = parseFloat($('#maxInter').val());
                            var minInter = parseFloat($('#minInter').val());
                            var inter = parseFloat(data[3])
                            if ((isNaN(minInter) && isNaN(maxInter)) ||
                                (isNaN(minInter) && inter <= maxInter) ||
                                (minInter <= inter && isNaN(maxInter)) ||
                                (minInter <= inter && inter <= maxInter)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minReSpam, #maxReSpam').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxReSpam = parseFloat($('#maxReSpam').val());
                            var minReSpam = parseFloat($('#minReSpam').val());
                            var reSpam = parseFloat(data[4])
                            if ((isNaN(minReSpam) && isNaN(maxReSpam)) ||
                                (isNaN(minReSpam) && reSpam <= maxReSpam) ||
                                (minReSpam <= reSpam && isNaN(maxReSpam)) ||
                                (minReSpam <= reSpam && reSpam <= maxReSpam)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minAVG, #maxAVG').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxAVG = parseFloat($('#maxAVG').val());
                            var minAVG = parseFloat($('#minAVG').val());
                            var AVG = parseFloat(data[5])
                            if ((isNaN(minAVG) && isNaN(maxAVG)) ||
                                (isNaN(minAVG) && AVG <= maxAVG) ||
                                (minAVG <= AVG && isNaN(maxAVG)) ||
                                (minAVG <= AVG && AVG <= maxAVG)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minAVGText, #maxAVGText').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxAVGText = parseFloat($('#maxAVGText').val());
                            var minAVGText = parseFloat($('#minAVGText').val());
                            var count = parseFloat(data[6])
                            if ((isNaN(minAVGText) && isNaN(maxAVGText)) ||
                                (isNaN(minAVGText) && count <= maxAVGText) ||
                                (minAVGText <= count && isNaN(maxAVGText)) ||
                                (minAVGText <= count && count <= maxAVGText)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minInYourPage, #maxInYourPage').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxInYourPage = parseFloat($('#maxInYourPage').val());
                            var minInYourPage = parseFloat($('#minInYourPage').val());
                            var count = parseFloat(data[7])
                            if ((isNaN(minInYourPage) && isNaN(maxInYourPage)) ||
                                (isNaN(minInYourPage) && count <= maxInYourPage) ||
                                (minInYourPage <= count && isNaN(maxInYourPage)) ||
                                (minInYourPage <= count && count <= maxInYourPage)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minTextIYP, #maxTextIYP').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxTextIYP = parseFloat($('#maxTextIYP').val());
                            var minTextIYP = parseFloat($('#minTextIYP').val());
                            var count = parseFloat(data[8])
                            if ((isNaN(minTextIYP) && isNaN(maxTextIYP)) ||
                                (isNaN(minTextIYP) && count <= maxTextIYP) ||
                                (minTextIYP <= count && isNaN(maxTextIYP)) ||
                                (minTextIYP <= count && count <= maxTextIYP)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minAVGLink, #maxAVGLink').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxAVGLink = parseFloat($('#maxAVGLink').val());
                            var minAVGLink = parseFloat($('#minAVGLink').val());
                            var count = parseFloat(data[9])
                            if ((isNaN(minAVGLink) && isNaN(maxAVGLink)) ||
                                (isNaN(minAVGLink) && count <= maxAVGLink) ||
                                (minAVGLink <= count && isNaN(maxAVGLink)) ||
                                (minAVGLink <= count && count <= maxAVGLink)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
                $('#minLinkIYP, #maxLinkIYP').keyup(function () {
                    $.fn.dataTable.ext.search.push(
                        function (settings, data) {
                            var maxLinkIYP = parseFloat($('#maxLinkIYP').val());
                            var minLinkIYP = parseFloat($('#minLinkIYP').val());
                            var count = parseFloat(data[10])
                            if ((isNaN(minLinkIYP) && isNaN(maxLinkIYP)) ||
                                (isNaN(minLinkIYP) && count <= maxLinkIYP) ||
                                (minLinkIYP <= count && isNaN(maxLinkIYP)) ||
                                (minLinkIYP <= count && count <= maxLinkIYP)) {
                                return true;
                            }
                            return false;
                        }
                    );
                    $.each($('[generated-child=true]'), function () {
                        $(this).attr('generated-child', false)
                    })
                    table.draw();
                });
            });
        </script>
    @endslot
@endcomponent
