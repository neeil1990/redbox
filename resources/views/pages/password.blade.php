@component('component.card', ['title' => __('Password generator')])

    @slot('css')
        <link rel='stylesheet' id='swpc-main-css'  href='{{ asset('plugins/utm-marks/css/style.css') }}' type='text/css' media='all' />
    @endslot

    <div class="password-generator">
        <div>
            <form action="{{  route('generate.password') }}" method="post">
                @csrf
                <fieldset>
                    <legend>{{__('Generator settings')}}:</legend>
                    <label>
                        <input type="checkbox" id="checkbox1" class="checkbox" name="enums">
                        {{__('Enums')}}
                    </label><br>
                    <label>
                        <input type="checkbox" id="checkbox2" class="checkbox" name="upperCase">
                        {{__('Upper case')}}
                    </label><br>
                    <label>
                        <input type="checkbox" id="checkbox3" class="checkbox" name="lowerCase">
                        {{__('Lower case')}}
                    </label><br>
                    <label>
                        <input type="checkbox" id="checkbox4" class="checkbox" name="specialSymbols">
                        {{__('Special symbols')}} %, *, ), ?, @, #, $, ~
                    </label>
                    <br>
                    <label>
                        <input type="checkbox" id="checkbox5" class="checkbox" name="savePassword">
                        {{__('Save password')}}?
                    </label>
                    <br>
                    <label>
                        {{__('Characters')}} :
                        <input type="number" class="number" name="countSymbols" value="6" max="50" min="1">
                    </label>
                </fieldset>
                <input type="submit" value="{{__('Generate password')}}" class="btn btn-secondary"
                       onclick="saveState()">
            </form>
            <h4 class="mt-3 mb-3 text-danger">{{$errors->first()}}</h4>
            @if (\Illuminate\Support\Facades\Session::has('message'))
                <div class="alert alert-danger mt-5">{{ \Illuminate\Support\Facades\Session::get('message') }}</div>
            @endif
            @if (\Illuminate\Support\Facades\Session::has('password'))
                <h3 class="mt-5">Сгенерированный пароль: {{ \Illuminate\Support\Facades\Session::get('password') }}</h3>
            @endif
        </div>
    </div>

    <div class="my-passwords mt-5">
        <h2>{{__('Your generated passwords')}}</h2>

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
    @slot('js')
        <script src="{{ asset('plugins/password-generator/js/my-password-generator.js') }}"></script>
    @endslot
@endcomponent
