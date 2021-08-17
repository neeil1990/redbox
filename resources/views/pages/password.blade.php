@component('component.card', ['title' => __('Password generator')])

    @slot('css')
        <!-- CodeMirror -->
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/codemirror.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/theme/monokai.css') }}">
        <!-- jQuery ui -->
        <link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}">
    @endslot

    <div class="password-generator">
        <div class="genset">
            <form action="{{  route('generate-password') }}" method="post">
                @csrf
                <fieldset>
                    <legend>Настройки генератора:</legend>
                    <label>
                        <input type="checkbox" name="enums" checked="checked">
                        Цифры
                    </label><br>
                    <label>
                        <input type="checkbox" name="upperCase" checked="checked">
                        Прописные буквы
                    </label><br>
                    <label>
                        <input type="checkbox" name="lowerCase" checked="checked">
                        Строчные буквы
                    </label><br>
                    <label>
                        <input type="checkbox" name="specialSymbols">
                        Спец. символы %, *, ),?, @, #, $, ~
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" name="savePassword" checked="checked">
                        Сохранить пароль?
                    </label>
                    <br>
                    <label>
                        Длина пароля:<input type="text" name="countSymbols" value="6" maxlength="3" minlength="1"
                                            size="5">
                    </label>
                </fieldset>
                <input type="submit" value="Сгенерировать пароль" class="btn btn-success">
            </form>
            <h4 class="mt-3 mb-3 text-danger">{{$errors->first()}}</h4>
            @if($errors->any())
            @endif
            @if(session()->has('message'))
                <div class="alert">
                    <h3>Сгенерированный пароль</h3>
                    <h4>{{ session()->get('message') }}</h4>
                </div>
            @endif
        </div>
    </div>

    <div class="my-passwords">
        <div class="d-flex flex-column align-items-stretch flex-shrink-0 bg-white">
            <a href="/"
               class="d-flex align-items-center flex-shrink-0 p-3 link-dark text-decoration-none border-bottom">
                <svg class="bi me-2" width="30" height="24">
                    <use xlink:href="#bootstrap"></use>
                </svg>
            </a>
            <div class="list-group list-group-flush border-bottom scrollarea">
                @foreach($user->passwords as $password)
                    <a href="#" class="list-group-item list-group-item-action py-3 lh-tight" aria-current="true">
                        <div class="d-flex w-100 align-items-center justify-content-between">
                            <strong class="mb-1">{{$password->password}}</strong>
                            <small>{{$password->created_at}}</small>
                        </div>
                    </a>
                @endforeach

            </div>
        </div>
        @empty($user->passwords)
            <h2>У вас ещё нет сгенерированных паролей</h2>
        @endempty
    </div>
@endcomponent
