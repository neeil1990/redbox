@component('component.card', ['title' => __('Text Analyzing')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/domain-information/css/domain-information.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! Form::open(['action' =>'TextAnalyzerController@analyze', 'method' => 'POST'])!!}
{{--    <div class="form-group required">--}}
{{--        {!! Form::select('type', ['text' => __('TEXT'),'HTML code' => __('HTML code'), 'url' => __('URL')], null, ['class' => 'form-control col-4 type-analyzing']) !!}--}}
{{--    </div>--}}
{{--    <div class="form-group required text-or-html">--}}
{{--        {!! Form::textarea('text', null, ['class' => 'form-control textarea-text-or-html', 'required']) !!}--}}
{{--    </div>--}}
    <div class="form-group required url">
        <label for="link">URL</label>
        {!! Form::text('link', null, ['class' => 'form-control col-8 url']) !!}
    </div>
    <div class="switch mt-5 mb-3">
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           checked
                           id="switchNoindex"
                           name="noIndex">
                    <label class="custom-control-label" for="switchNoindex"></label>
                </div>
            </div>
            <p>Исключать текст в теге noindex</p>
        </div>
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="switchAltAndTitle"
                           name="altTitle">
                    <label class="custom-control-label" for="switchAltAndTitle"></label>
                </div>
            </div>
            <p>Отслеживать слова в атрибутах alt, tittle и data-text</p>
        </div>
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           checked
                           id="switchConjunctionsPrepositionsPronouns"
                           name="conjunctionsPrepositionsPronouns">
                    <label class="custom-control-label" for="switchConjunctionsPrepositionsPronouns"></label>
                </div>
            </div>
            <p>Исключить союзы, предлоги, местоименеия</p>
        </div>
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="switchMyListWords">
                    <label class="custom-control-label" for="switchMyListWords"></label>
                </div>
            </div>
            <span>Исключать
            <span class="text-muted">(свой список слов)</span>
        </span>
        </div>
    </div>
    <div class="form-group required list-words mt-1" style="display: none">
        {!! Form::textarea('listWords', null, ['class' => 'form-control listWords']) !!}
    </div>
    <input type="submit" class="btn btn-secondary mt-5" value="{{ __('Analyzing') }}">
    {!! Form::close() !!}
    @isset($response)
        <div>
            <div class="response mt-5">
                <h3>Результат</h3>
                <table class="table table-bordered table-hover dtr-inline w-100">
                    <tbody>
                    <tr>
                        <td class="dtr-control sorting_1 col-4">
                            <b> Количество слов</b>
                        </td>
                        <td>
                            {{ $response['general']['countWords'] }}
                        </td>
                    </tr>
                    <tr>
                        <td class="dtr-control sorting_1">
                            <b>Количество пробелов</b>
                        </td>
                        <td>
                            {{ $response['general']['countSpaces'] }}
                        </td>
                    </tr>
                    <tr>
                        <td class="dtr-control sorting_1">
                            <b>Количество символов</b>
                        </td>
                        <td>
                            {{ $response['general']['textLength'] }}
                        </td>
                    </tr>
                    <tr>
                        <td class="dtr-control sorting_1">
                            <b>Количество символов без пробелов</b>
                        </td>
                        <td>
                            {{ $response['general']['lengthWithOutSpaces'] }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-5" style="max-height: 600px; overflow-y: auto">
                <h3>Общий анализ слов</h3>
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                    <thead>
                    <tr>
                        <th style="max-width: 250px !important;">Слово</th>
                        <th>Плотность</th>
                        <th>Зона текста</th>
                        <th>Зона ссылок</th>
                        <th>Общая зона</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($response['totalWords'] as $word)
                        <tr>
                            <td style="max-width: 250px !important;">{!! $word['text'] !!}</td>
                            <td>{{ $word['density'] }}</td>
                            <td>{{ $word['inText'] }}</td>
                            <td>{{ $word['inLink'] }}</td>
                            <td>{{ $word['total'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-5">
                <h3>Зона текста</h3>
                <div id="textWithoutLinks" style="height: 400px;"></div>
            </div>
            <div class="mt-5">
                <h3>Зона ссылок</h3>
                <div id="links" style="height: 400px;"></div>
            </div>
            <div class="mt-5">
                <h3>Зона текста и ссылок</h3>
                <div id="textWithLinks" style="height: 400px;"></div>
            </div>
            <div class="mb-5 mt-5" style="max-height: 600px; overflow-y: auto;">
                <h3>Словосочетания из 2 слов</h3>
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                    <thead>
                    <tr>
                        <th class="col-4">
                            Словосочетание
                        </th>
                        <th>
                            Повторений
                        </th>
                        <th>
                            Плотность
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($response['phrases'] as $phrase)
                        <tr>
                            <td class="col-4">{{ $phrase['phrase'] }}</td>
                            <td class="col-4">{{ $phrase['count'] }}</td>
                            <td class="col-4">{{ $phrase['density'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endisset
    @slot('js')
        <script defer src="{{ asset('plugins/jqcloud/js/jqcloud-1.0.4.min.js') }}"></script>
        <script>
            // $('.form-control.col-4.type-analyzing').change(function () {
            //     let text = $('.textarea-text-or-html')
            //     if ($(this).val() === 'text' || $(this).val() === 'HTML code') {
            //         $('.form-group.required.url').hide(300)
            //         $('.form-control.col-8.url').removeAttr('required')
            //         text.show(300)
            //         text.prop('required', true)
            //     } else {
            //         $('.form-group.required.url').show(300)
            //         $('.form-control.col-8.url').prop('required', true)
            //         text.hide(300)
            //         text.removeAttr('required')
            //     }
            // })

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
                    if (array.count >= 250) {
                        length = 250
                    } else {
                        length = array.count
                    }
                    let a = [], b = {};
                    for (let i = 0; i < length; i++) {
                        b = array[i]
                        a.push(b);
                    }
                    return a;
                }
            });
        </script>
    @endslot
@endcomponent
