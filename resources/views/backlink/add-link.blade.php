@component('component.card', ['title' => __('Link tracking')])
@section('content')
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/common.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    {!! Form::open(['action' =>'BacklinkController@storeLink', 'method' => 'POST'])!!}
    <div class='col-md-6 mt-3'>
        <div class='form-group required'>
            <input type="hidden" name="id" value="{{ $id }}">
            {!! Form::label('Params') !!}
            {!! Form::textarea('params', null, [
            'class'=>'form-control',
            'required'=>'required',
            'placeholder'=>'Site donor::Link on site::anchor::Отслеживать nofollow(0/1)::Отслеживать noindex(0/1)::Проверка в индексах yandex(0/1)::Проверка в индексах google(0/1)'
            ]) !!}
            <span class="text-muted">
                Site donor::Link on site::anchor::Отслеживать nofollow(0/1)::Отслеживать noindex(0/1)::Проверка в
                индексах yandex(0/1)::Проверка в индексах google(0/1)
            <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                <p>Вы должны ввести данные в формате</p>
                                <p>
                                Site donor::Link::anchor::Отслеживать nofollow(0/1)::Отслеживать noindex(0/1)::Проверка в индексах yandex(0/1)::Проверка в индексах google(0/1)</p>
                                <p>Пример https://habr.com/ru/all/::/ru/news/::новости::1::1::0::1</p>
                                <p>В этом случае будет проходить проверка на наличие ссылки /ru/news/ на доноре https://habr.com/ru/all/</p>
                                <p>Разделяйте строки при помощи Shift + Enter</p>
                            </span>
                        </span>
                    </span>

            </span>
        </div>
        <div class='pt-3'>
            <button class='btn btn-secondary' title='Save' type='submit'>{{ __('Add to backlink') }}</button>
            <a href='{{ route('show.backlink',$id) }}' class='btn btn-default'> {{ __('Back') }}</a>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@endcomponent
