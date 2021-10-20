@component('component.card', ['title' => __('Add Link tracking')])
@section('content')
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    {!! Form::open(['action' =>'BacklinkController@storeLink', 'method' => 'POST'])!!}
    <div class='col-md-6 mt-3 express-form'>
        <div class='form-group required'>
            <input type="hidden" name="id" value="{{ $id }}">
            {!! Form::label('Params') !!}
            {!! Form::textarea('params', null, [
            'class'=>'form-control',
            'required'=>'required',
            'placeholder'=>'Site donor::Link on site::anchor::Отслеживать nofollow(0/1)::Отслеживать noindex(0/1)::Проверка в индексах yandex(0/1)::Проверка в индексах google(0/1)'
            ]) !!}
            <span class="__helper-link ui_tooltip_w">
                Непонятна конструкция
            <i class="fa fa-question-circle"></i>
                <span class="ui_tooltip __right __l">
                    <span class="ui_tooltip_content">
                        <p>
                        Сайт на котором будет находится ссылка::Ссылка::Анкор::Отслеживать nofollow(0/1)::Отслеживать noindex(0/1)::Проверка в индексах yandex(0/1)::Проверка в индексах google(0/1)
                        </p>
                        <p>Пример https://ru.wikipedia.org/wiki/Сайт::/wiki/%D0%91%D1%80%D0%B0%D1%83%D0%B7%D0%B5%D1%80::браузеров::0::0::0::0</p>
                        https://ru.wikipedia.org/wiki/Сайт - Домен
                        /wiki/%D0%91%D1%80%D0%B0%D1%83%D0%B7%D0%B5%D1%80 - Ссылка, которую будет искать скрипт<br>
                        браузеров - Анкор ссылки<br>
                        Проверять что в ссылке не присутствует атрибут rel с свойством nofollow<br>
                        Проверять что ссылку отсутствует в теге noindex <br>
                        Проверка того, что ссылка проиндексирована Яндексом<br>
                        Проверка того, что ссылка проиндексирована Google<br><br>
                        Разделяйте строки при помощи Shift + Enter
                    </span>
                </span>
            </span>
            <p>Вы можете <a href="#" class="text-info">воспользоваться упрощённым форматом</a></p>

        </div>
        <div class='pt-3'>
            <button class='btn btn-secondary' title='Save' type='submit'>{{ __('Add to backlink') }}</button>
            <a href='{{ route('show.backlink',$id) }}' class='btn btn-default'> {{ __('Back') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
    <div style="display: none" class="simplified-form">
        <p>Вы можете <a href="#" class="text-info express">воспользоваться ускоренным форматом</a></p>
        {!! Form::open(['action' =>'BacklinkController@storeLink', 'method' => 'POST'])!!}
        <input type="hidden" name="id" value="{{ $id }}">
        <input type="hidden" name="countRows" id="countRows" value="1">
        <table id="example2"
               class="table table-bordered table-hover dataTable dtr-inline"
               role="grid"
               aria-describedby="example2_info">
            <thead>
            <tr>
                <th>Ссылка на сайт донор</th>
                <th>Ссылка, которую будет искать скрипт</th>
                <th>Анкор</th>
                <th>Проверять наличие nofollow</th>
                <th>Проверять наличие noindex</th>
                <th>Проверять индексирование в Яндекс</th>
                <th>Проверять индексирование в Google</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    {!! Form::text('site_donor_1', null ,['class' => 'form-control backlink','required' => 'required','placeholder' => 'Сайт донор']) !!}
                </td>
                <td>
                    {!! Form::text('link_1', null ,['class' => 'form-control backlink','required' => 'required','placeholder' => 'Ссылка']) !!}
                </td>
                <td>
                    {!! Form::text('anchor_1', null ,['class' => 'form-control backlink','required' => 'required','placeholder' => 'Анкор']) !!}
                </td>
                <td>
                    {!! Form::select('nofollow_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
                <td>
                    {!! Form::select('noindex_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
                <td>
                    {!! Form::select('yandex_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
                <td>
                    {!! Form::select('google_1', ['1' => __('Yes'), '0' => __('No')], null, ['class' => 'custom-select rounded-0']) !!}
                </td>
            </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <div class="buttons">
                <input type="submit" class="btn btn-secondary mr-2" value="{{ __('Save') }}">
                <input type="button" class="btn btn-default mr-2" id="addRow" value="add row">
                <input type="button" class="btn btn-default" id="removeRow" value="delete row" style="display: none">
            </div>
            <a href='{{ route('backlink') }}' class='btn btn-default mr-2'> {{ __('Back') }}</a>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
@slot('js')
    <script src="{{ asset('plugins/backlink/js/add-row-in-table.js') }}"></script>
@endslot
@endcomponent
