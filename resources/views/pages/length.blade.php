@component('component.card', ['title' => __('Counting text length')])
    <div>
        <p class="w-50 mb-3">
            {{__('This tool will instantly calculate how many characters and spaces are in your text, as well as the number of characters without spaces and the number of words in the text.')}}
        </p>
        <p class="w-50 mt-3 mb-3">
            {{__('If you are typing not in a text editor, but in a notepad or browser, then this tool will become your faithful assistant.')}}
        </p>
        <h2>{{__("Enter text")}}</h2>
        <form id="text-length" action="{{  route('counting.text.length') }}" method="POST">
            @csrf
            <textarea name="text" class="form-control w-50" id="text" rows="10"
                      required>@if (\Illuminate\Support\Facades\Session::has('text')){{ \Illuminate\Support\Facades\Session::get('text') }}@endif</textarea>
            <br>
            <input class="btn btn-secondary mr-2" type="submit" value="{{__('Calculate')}}">
            <input class="btn btn-flat btn-default" id="reset" type="reset" value="{{__('Clear')}}"
                   onclick="clearCountingResult();">
        </form>
        <br>
        <div id="text-length-result">
            @if (\Illuminate\Support\Facades\Session::has('length'))
                <div id="all-text">
                    <b>{{__('Total characters')}}: </b>
                    <span class="counting-result">
                        {{ \Illuminate\Support\Facades\Session::get('length') }}
                    </span>
                </div>
            @endif
            @if (\Illuminate\Support\Facades\Session::has('countSpaces'))
                <div id="spaces">
                    <b>{{__('Total spaces')}}: </b>
                    <span class="counting-result">
                        {{ \Illuminate\Support\Facades\Session::get('countSpaces') }}
                    </span>
                </div>
            @endif
            @if (\Illuminate\Support\Facades\Session::has('lengthWithOutSpaces'))
                <div id="no-spaces">
                    <b>{{__('Total characters without spaces')}}: </b>
                    <span class="counting-result">
                        {{ \Illuminate\Support\Facades\Session::get('lengthWithOutSpaces') }}
                    </span>
                </div>
            @endif
            @if (\Illuminate\Support\Facades\Session::has('countWord'))
                <div id="words">
                    <b>{{__('Total words')}}: </b>
                    <span class="counting-result">
                    {{ \Illuminate\Support\Facades\Session::get('countWord') }}
                    </span>
                </div>
            @endif
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/text-length/js/text-length.js') }}"></script>
    @endslot
@endcomponent
