@component('component.card', ['title' =>  __('Все слова') ])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <style>
            .dt-button {
                margin-left: 5px;
            }

            #unigram > tbody > tr > td:nth-child(7),
            #unigram > tbody > tr > td:nth-child(9),
            #unigram > tbody > tr > td:nth-child(11) {
                background: #ebf0f5;
            }

            .RelevanceAnalysis {
                background: oldlace;
            }
        </style>
    @endslot
    <div id="toast-container" class="toast-top-right error-message empty" style="display:none;">
        <div class="toast toast-error" aria-live="polite">
            <div class="toast-message error-message" id="toast-message"></div>
        </div>
    </div>

    <div id="toast-container" class="toast-top-right success-message lock-word" style="display:none;">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('added in ignored') }}</div>
        </div>
    </div>

    <div class="pb-3 unigram">
        <h2>{{ __('Top list of phrases (TLP)') }}</h2>
        <table id="unigram" class="table table-bordered table-hover dataTable dtr-inline"
               style="width: 100% !important;">
            <thead>
            <tr style="position: relative; z-index: 100">
                <th>
                    {{ __('Words') }}
                    <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="ui_tooltip __right">
                                        <span class="ui_tooltip_content" style="text-align: right">{{ __('Words and their word forms that are present on competitors websites.') }}
                                        </span>
                                    </span>
                                </span>
                </th>
                <th>Tf
                    <span class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="ui_tooltip __right">
                                        <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                        </span>
                                    </span>
                                </span>
                </th>
                <th>Idf
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The weight of the phrase relative to others.') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
                <th>
                    {{ __('Intersection') }}
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The number of sites in which the word is present.') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
                <th>{{ __('Re - spam') }}
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The maximum number of repetitions found on the competitors website.') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
                <th>{{ __('Average number of repetitions in the text and links') }}
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text and links of your competitors.') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
                <th>{{ __('The total number of repetitions in the text and links') }}
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The total number of repetitions on your page in links and text.') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
                <th>{{ __('Average number of repetitions in the text') }}
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the text of your competitors.') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
                <th>{{ __('Number of repetitions in text') }}
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The number of repetitions in the text on your page') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
                <th>{{ __('Average number of repetitions in links') }}
                    <span
                        class="__helper-link ui_tooltip_w">
                                    <i class="fa fa-question-circle"></i>
                                    <span class="ui_tooltip __left">
                                        <span class="ui_tooltip_content">{{ __('The average value of the number of repetitions in the links of your competitors.') }}
                                        </span>
                                    </span>
                                    </span>
                </th>
                <th>{{ __('Number of repetitions in links') }}
                    <span class="__helper-link ui_tooltip_w">
                                        <i class="fa fa-question-circle"></i>
                                        <span class="ui_tooltip __left">
                                            <span class="ui_tooltip_content">{{ __('The number of repetitions in the links on your page.') }}
                                            </span>
                                        </span>
                                    </span>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $key => $item)
                <tr>
                    <td>{{ $key }}</td>
                    <td>{{ $item['tf'] }}</td>
                    <td>{{ $item['idf'] }}</td>
                    <td>{{ $item['numberOccurrences'] }}</td>
                    <td>{{ $item['reSpam'] }}</td>
                    <td>{{ $item['avgInTotalCompetitors'] }}</td>
                    <td>{{ $item['totalRepeatMainPage'] }}</td>
                    <td>{{ $item['avgInText'] }}</td>
                    <td>{{ $item['repeatInTextMainPage'] }}</td>
                    <td>{{ $item['avgInLink'] }}</td>
                    <td>{{ $item['repeatInLinkMainPage'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/canvasjs/js/canvasjs.js') }}"></script>
        <script src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
        <script>
            $(document).ready(function () {
                $('#unigram').DataTable({
                    "order": [[1, "desc"]],
                    "pageLength": 25,
                    "searching": true,
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel'
                    ]
                })
                $('.dt-button').addClass('btn btn-secondary')
            })
        </script>
    @endslot
@endcomponent
