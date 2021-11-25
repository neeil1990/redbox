@component('component.card', ['title' => __('Text Analyzing')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/domain-information/css/domain-information.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! Form::open(['action' =>'TextAnalyzerController@analyze', 'method' => 'POST'])!!}
    <div class="form-group required">
        {!! Form::select('type', ['text' => __('TEXT'),'HTML code' => __('HTML code'), 'url' => __('URL')], null, ['class' => 'form-control col-4 type-analyzing']) !!}
    </div>
    <div class="form-group required text-or-html">
        {!! Form::textarea('text', null, ['class' => 'form-control textarea-text-or-html', 'required']) !!}
    </div>
    <div class="form-group required url" style="display: none">
        {!! Form::text('link', null, ['class' => 'form-control col-8 url']) !!}
    </div>
    <div class="switch mt-3 mb-3">
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           checked
                           id="switchNoindex">
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
                           id="switchAltAndTitle">
                    <label class="custom-control-label" for="switchAltAndTitle"></label>
                </div>
            </div>
            <p>Учитывать атрибуты alt and tittle</p>
        </div>
        <div class="d-flex">
            <div class="__helper-link ui_tooltip_w">
                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox"
                           class="custom-control-input"
                           checked
                           id="switchConjunctionsPrepositionsPronouns">
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
    <input type="submit" class="btn btn-secondary mt-3" value="{{ __('Analyzing') }}">
    {!! Form::close() !!}
    @isset($response)
        <div class="response pt-3 pb-3">
            <table class="table table-bordered table-hover dtr-inline w-50">
                <tbody>
                <tr>
                    <td class="dtr-control sorting_1 col-6">
                        Количество слов
                    </td>
                    <td>
                        {{ $response['countWords'] }}
                    </td>
                </tr>
                <tr>
                    <td class="dtr-control sorting_1">
                        Количество пробелов
                    </td>
                    <td>
                        {{ $response['countSpaces'] }}
                    </td>
                </tr>
                <tr>
                    <td class="dtr-control sorting_1">
                        Количество символов
                    </td>
                    <td>
                        {{ $response['textLength'] }}
                    </td>
                </tr>
                <tr>
                    <td class="dtr-control sorting_1">
                        Количество символов без пробелов
                    </td>
                    <td>
                        {{ $response['lengthWithOutSpaces'] }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    @endisset

    @slot('js')
        <script>
            $('.form-control.col-4.type-analyzing').change(function () {
                let text = $('.textarea-text-or-html')
                if ($(this).val() === 'text' || $(this).val() === 'HTML code') {
                    $('.form-group.required.url').hide(300)
                    $('.form-control.col-8.url').removeAttr('required')
                    text.show(300)
                    text.prop('required', true)
                } else {
                    $('.form-group.required.url').show(300)
                    $('.form-control.col-8.url').prop('required', true)
                    text.hide(300)
                    text.removeAttr('required')
                }
            })

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
    @endslot
@endcomponent
