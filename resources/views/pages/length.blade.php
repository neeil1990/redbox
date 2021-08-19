@component('component.card', ['title' => __('Counting text length')])
    <div>
        <h1>{{__('Counting text length')}}</h1>
        <p class="w-50 mt-3 mb-3">
            {{__('This tool will instantly calculate how many characters and spaces are in your text, as well as the number of characters without spaces and the number of words in the text.')}}
        </p>
        <p class="w-50 mt-3 mb-3">
            {{__('If you are typing not in a text editor, but in a notepad or browser, then this tool will become your faithful assistant.')}}
        </p>
        <h2>{{__("Enter text")}}</h2>
        <form id="text-length" action="{{  route('counting-text-length') }}" method="POST">
            @csrf
            <textarea name="text" class="form-control w-50" id="text" rows="10"
                      required>@if (\Illuminate\Support\Facades\Session::has('text')){{ \Illuminate\Support\Facades\Session::get('text') }}@endif</textarea>
            <br>
            <input class="btn btn-secondary mr-2" type="submit" value="{{__('Calculate')}}">
            <input class="btn btn-flat btn-default" id="_reset" type="reset" value="{{__('Clear')}}">
        </form>
        <br>
        <div>
            <div id="all-text">
                <b>{{__('Total characters')}}: </b>
                @if (\Illuminate\Support\Facades\Session::has('length'))
                    {{ \Illuminate\Support\Facades\Session::get('length') }}
                @endif
            </div>
            <div id="spaces">
                <b>{{__('Total spaces')}}: </b>
                @if (\Illuminate\Support\Facades\Session::has('countSpaces'))
                    {{ \Illuminate\Support\Facades\Session::get('countSpaces') }}
                @endif
            </div>
            <div id="no-spaces">
                <b>{{__('Total characters without spaces')}}: </b>
                @if (\Illuminate\Support\Facades\Session::has('lengthWithOutSpaces'))
                    {{ \Illuminate\Support\Facades\Session::get('lengthWithOutSpaces') }}
                @endif
            </div>
            <div id="words">
                <b>{{__('Total words')}}: </b>
                @if (\Illuminate\Support\Facades\Session::has('countWord'))
                    {{ \Illuminate\Support\Facades\Session::get('countWord') }}
                @endif
            </div>
        </div>
    </div>
@endcomponent
