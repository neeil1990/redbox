@component('component.card', ['title' => __('Text Analyzing')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/domain-information/css/domain-information.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! Form::open(['action' =>'TextAnalyzerController@analyze', 'method' => 'POST'])!!}
    <div class="form-group required">
        {!! Form::select('type', ['url' => __('URL'),'text' => __('text/html')], isset($response['type'])?$response['type']:null, ['class' => 'form-control col-4 type-analyzing']) !!}
    </div>
    <div class="form-group required text-or-html">
        {!! Form::textarea('text', isset($response['text']) ? $response['text'] : (isset($url) ? $url : null), ['class' => 'form-control textarea-text-or-html', 'required']) !!}
    </div>
    <div class="switch mt-3 mb-3">
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="switchNoindex"
                           name="noIndex"
                           @isset($response['noIndex'])
                           checked
                        @endisset>
                    <label class="custom-control-label" for="switchNoindex"></label>
                </div>
            </div>
            <p>{{ __('Track the text in the noindex tag') }}</p>
        </div>
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="switchAltAndTitle"
                           name="hiddenText"
                           @isset($response['hiddenText'])
                           checked
                        @endisset>
                    <label class="custom-control-label" for="switchAltAndTitle"></label>
                </div>
            </div>
            <p>{{ __('Track words in the alt, title, and data-text attributes') }}</p>
        </div>
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="switchConjunctionsPrepositionsPronouns"
                           name="conjunctionsPrepositionsPronouns"
                           @isset($response['conjunctionsPrepositionsPronouns'])
                           checked
                        @endisset>
                    <label class="custom-control-label" for="switchConjunctionsPrepositionsPronouns"></label>
                </div>
            </div>
            <p>{{ __('Track conjunctions, prepositions, pronouns') }}</p>
        </div>
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="switchMyListWords"
                           name="switchMyListWords"
                           @isset($response['listWords'])
                           checked
                        @endisset>
                    <label class="custom-control-label" for="switchMyListWords"></label>
                </div>
            </div>
            <span>{{ __('Exclude') }}<span class="text-muted">{{ __('(your own list of words)') }}</span></span>
        </div>
    </div>
    <div class="form-group required list-words mt-1"
         @if(empty($response['listWords']))
         style="display: none"
        @endif>
        {!! Form::textarea(
            'listWords',
            isset($response['listWords'])? $response['listWords']: null,
            ['class' => 'form-control listWords col-8', 'cols' => 8, 'rows' => 5]
        ) !!}
    </div>
    <input type="submit" class="btn btn-secondary mt-2" value="{{ __('Analyse') }}">
    {!! Form::close() !!}
    @if(isset($response))
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/text-analyzer/css/style.css') }}"/>
        <div class="mt-5 mb-3">
            <h3>{{ __('Result') }}</h3>
            <table class="table table-bordered table-striped dataTable dtr-inline">
                <tbody>
                <tr>
                    <td class="dtr-control sorting_1 col-4">
                        <b>{{ __('Number of words') }}</b>
                    </td>
                    <td>
                        {{ $response['general']['countWords'] }}
                    </td>
                </tr>
                <tr>
                    <td class="dtr-control sorting_1">
                        <b>{{ __('Number of spaces') }}</b>
                    </td>
                    <td>
                        {{ $response['general']['countSpaces'] }}
                    </td>
                </tr>
                <tr>
                    <td class="dtr-control sorting_1">
                        <b>{{ __('Number of characters') }}</b>
                    </td>
                    <td>
                        {{ $response['general']['textLength'] }}
                    </td>
                </tr>
                <tr>
                    <td class="dtr-control sorting_1">
                        <b>{{ __('Number of characters without spaces') }}</b>
                    </td>
                    <td>
                        {{ $response['general']['lengthWithOutSpaces'] }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <h3 class="mt-5 mb-3">{{ __('Text analysis according to Zipfs law') }}</h3>
        <div id="chartContainer" class="w-100" style="height: 370px; width: 100%;"></div>
        <div class="mt-5 mb-3" style="max-height: 600px; overflow-y: auto;">
            <h3>{{ __('General word analysis') }}</h3>
            <table id="totalTable" class="table table-bordered table-striped dataTable dtr-inline">
                <thead>
                <tr>
                    <th class="w-50">?????????? <i class="fa fa-sort"></i></th>
                    <th>{{ __('Density') }} <i class="fa fa-sort"></i></th>
                    <th>{{ __('Common area') }} <i class="fa fa-sort"></i></th>
                    <th>{{ __('Text Area') }} <i class="fa fa-sort"></i></th>
                    <th>{{ __('Link Zone') }} <i class="fa fa-sort"></i></th>
                </tr>
                </thead>
                <tbody>
                @foreach($response['totalWords'] as $word)
                    <tr>
                        <td data-order="{{ $word['text'] }}" class="w-50">
                            <u class=" unique-word" style="cursor: pointer">{{ $word['text'] }}</u>
                            <span class="text-muted" style="display: none">
                                @isset($word['wordForms']['inLink'])
                                    <p class="mt-2"><b>{{__('Link Zone')}}:</b></p>
                                    <div class="d-flex justify-content-start">
                                    @foreach($word['wordForms']['inLink'] as $items)
                                            <div class="mr-3">
                                            @foreach($items as $key => $item)
                                                    <div>{{ $key }}: {{ $item }}</div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @endisset
                                @isset($word['wordForms']['inText'])
                                    <p class="mt-2"><b>{{ __('Text Area') }}:</b></p>
                                    <div class="d-flex justify-content-start">
                                    @foreach($word['wordForms']['inText'] as $items)
                                            <div class="mr-3">
                                            @foreach($items as $key => $item)
                                                    <div>{{ $key }}: {{ $item }}</div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                        </div>
                                @endisset
                            </span>
                        </td>
                        <td data-order="{{ $word['density'] }}">{{ $word['density'] }}</td>
                        <td data-order="{{ $word['total'] }}">{{ $word['total'] }}</td>
                        <td data-order="{{ $word['inText'] }}">{{ $word['inText'] }}</td>
                        <td data-order="{{ $word['inLink'] }}">{{ $word['inLink'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5 mb-3">
            <h3>{{ __('Text Area') }}</h3>
            <div class="mr-auto ml-auto" id="textWithoutLinks" style="height: 400px;"></div>
        </div>
        <div class="mt-5 mb-3">
            <h3>{{ __('Link Zone') }}</h3>
            <div class="mr-auto ml-auto" id="links" style="height: 400px;"></div>
        </div>
        <div class="mt-5 mb-3">
            <h3>{{ __('Text and Link zone') }}</h3>
            <div class="mr-auto ml-auto" id="textWithLinks" style="height: 400px;"></div>
        </div>
        <div class="mt-5 mb-3" style="max-height: 600px; overflow-y: auto;">
            <h3>{{ __('Phrases of 2 words') }}</h3>
            <table id="phrasesTable" class="table table-bordered table-striped dataTable dtr-inline">
                <thead>
                <tr>
                    <th class="col-4">
                        {{ __('Phrase') }}
                        <i class="fa fa-sort"></i>
                    </th>
                    <th>
                        {{ __('Repetitions') }}
                        <i class="fa fa-sort"></i>
                    </th>
                    <th>
                        {{ __('Density') }}
                        <i class="fa fa-sort"></i>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($response['phrases'] as $phrase)
                    <tr>
                        <td data-order="{{ $phrase['phrase'] }}" class="col-4">{{ trim($phrase['phrase']) }}</td>
                        <td data-order="{{ $phrase['count'] }}" class="col-4">{{ $phrase['count'] }}</td>
                        <td data-order="{{ $phrase['density'] }}" class="col-4">{{ $phrase['density'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
    @slot('js')
        @isset($url)
            <script src="{{ asset('plugins/text-analyzer/js/run-script.js') }}"></script>
        @endisset
        <script src="{{ asset('plugins/canvasjs/js/canvasjs.js') }}"></script>
        <script src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            $('input#switchMyListWords').click(function () {
                if ($(this).is(':checked')) {
                    $('.form-group.required.list-words.mt-1').show(300)
                    $('.form-control.listWords').prop('required', true)
                } else {
                    $('.form-group.required.list-words.mt-1').hide(300)
                    $('.form-control.listWords').removeAttr('required')
                }
            })
        </script>
        <script>
            $(document).ready(function () {
                var options = {
                    animationEnabled: true,
                    theme: "light2",
                    data: [
                        {
                            type: "line",
                            name: "???????????????? ????????????????",
                            showInLegend: true,
                            dataPoints: graph
                        },
                        {
                            type: "line",
                            name: "?????????????????? ????????????????",
                            showInLegend: true,
                            dataPoints: [
                                {x: 5, y: graph[0]['y']},
                                {x: 6, y: Math.round(graph[0]['y'] / 2)},
                                {x: 7, y: Math.round(graph[0]['y'] / 3)},
                                {x: 8, y: Math.round(graph[0]['y'] / 4)},
                                {x: 9, y: Math.round(graph[0]['y'] / 5)},
                                {x: 10, y: Math.round(graph[0]['y'] / 6)},
                                {x: 11, y: Math.round(graph[0]['y'] / 7)},
                                {x: 12, y: Math.round(graph[0]['y'] / 8)},
                                {x: 13, y: Math.round(graph[0]['y'] / 8)},
                                {x: 14, y: Math.round(graph[0]['y'] / 9)},
                                {x: 15, y: Math.round(graph[0]['y'] / 9)},
                                {x: 16, y: Math.round(graph[0]['y'] / 9)},
                                {x: 17, y: Math.round(graph[0]['y'] / 9)},
                                {x: 18, y: Math.round(graph[0]['y'] / 10)},
                                {x: 19, y: Math.round(graph[0]['y'] / 10)},
                                {x: 20, y: Math.round(graph[0]['y'] / 10)},
                                {x: 21, y: Math.round(graph[0]['y'] / 10)},
                                {x: 22, y: Math.round(graph[0]['y'] / 10)},
                                {x: 23, y: Math.round(graph[0]['y'] / 10)},
                                {x: 24, y: Math.round(graph[0]['y'] / 10)},
                                {x: 25, y: Math.round(graph[0]['y'] / 10)},
                            ]
                        }]
                };

                $("#chartContainer").CanvasJSChart(options);
            });

        </script>
        <script>
            $(document).ready(function () {
                $(function () {
                    if (typeof textWithoutLinks === 'object') {
                        let a = arrayToObj(textWithoutLinks)
                        $("#textWithoutLinks").jQCloud(a);
                    }
                    if (typeof linksText === 'object') {
                        let c = arrayToObj(linksText)
                        $("#links").jQCloud(c);
                    }
                    if (typeof textWithLinks === 'object') {
                        let e = arrayToObj(textWithLinks)
                        $("#textWithLinks").jQCloud(e);
                    }
                });

                function arrayToObj(array) {
                    let length;
                    length = array.count
                    let a = [], b = {};
                    for (let i = 0; i < length; i++) {
                        if (typeof array[i] != 'undefined') {
                            b = array[i]
                            a.push(b);
                        }
                    }
                    return a;
                }
            });
        </script>
        <script>
            $(document).ready(function () {
                $('#totalTable').DataTable({
                    "order": [[2, "desc"]]
                });
                $('#phrasesTable').DataTable({
                    "order": [[1, "desc"]]
                });
            });

            $('.unique-word').click(function () {
                if ($(this).parent().children('span').css('display') === 'none') {
                    $(this).parent().children('span').show()
                } else {
                    $(this).parent().children('span').hide()
                }
            });
        </script>
    @endslot
@endcomponent
