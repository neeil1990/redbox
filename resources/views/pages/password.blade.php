@component('component.card', ['title' => __('Password generator')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/list-comparison/css/style.css') }}"/>
    @endslot
    <div class="password-generator">
        <div>
            <form action="{{  route('generate.password') }}" method="post">
                @csrf
                <div class="d-flex flex-column">
                    <p>{{__('Generator settings')}}:</p>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox1" class="checkbox custom-control-input" name="enums">
                        <label for="checkbox1" class="custom-control-label">
                            {{__('Enums')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox2" class="checkbox custom-control-input" name="upperCase">
                        <label for="checkbox2" class="custom-control-label">
                            {{__('Upper case')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox3" class="checkbox custom-control-input" name="lowerCase">
                        <label for="checkbox3" class="custom-control-label">
                            {{__('Lower case')}}
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox4" class="checkbox custom-control-input" name="specialSymbols">
                        <label for="checkbox4" class="custom-control-label">
                            {{__('Special symbols')}} %, *, ), ?, @, #, $, ~
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="checkbox5" class="checkbox custom-control-input" name="savePassword">
                        <label for="checkbox5" class="custom-control-label">
                            {{__('Save password')}}?
                        </label>
                    </div>
                    <label>
                        {{__('Characters')}} :
                        <input type="number" class="number" name="countSymbols" value="6" max="50" min="1">
                    </label>
                </div>
                <input type="submit"
                       value="{{__('Generate password')}}"
                       class="btn btn-secondary"
                       onclick="saveState()">
            </form>
            <h4 class="mt-3 mb-3 text-danger">{{$errors->first()}}</h4>
            @if (\Illuminate\Support\Facades\Session::has('message'))
                <div class="alert alert-danger mt-5">{{ \Illuminate\Support\Facades\Session::get('message') }}</div>
            @endif
            @if (\Illuminate\Support\Facades\Session::has('password'))
                <h3 class="mt-5">{{__('Generated password')}}
                    : {{ \Illuminate\Support\Facades\Session::get('password') }}</h3>
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
