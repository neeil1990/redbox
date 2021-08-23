@component('component.card', ['title' => __('List comparison')])

    <form action="{{  route('counting.list.comparison') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-sm-12 textpart">
                <h3>{{__('Compare the lists of keywords and get a common unique list.')}}</h3>
            </div>
        </div>
        <div class="row mt-3 mb-3">
            <div class="col-sm-6 d-flex flex-column">
                <label class="font-weight-light">{{__('First list')}}</label>
                <textarea class="form-control" name="firstList" rows="7"
                          required>{{\Illuminate\Support\Facades\Input::old('firstList')}}</textarea>
            </div>
            <div class="col-sm-6 d-flex flex-column">
                <label class="font-weight-light">{{__('Second list')}}</label>
                <textarea class="form-control" name="secondList" rows="7"
                          required>{{\Illuminate\Support\Facades\Input::old('secondList')}}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-8 d-flex flex-column">
                <label class="mt-3 mb-3">{{__('Comparison type:')}}</label>
                <label
                    title="{{__('a list of keywords that were found in both the first and second list (intersection)')}}"
                    class="radio font-weight-light">
                    <input type="radio" name="option" value="unique" id="first-radio-option" checked
                           onclick="saveOptionState('first')">{{__('Unique phrases that are in each of the two lists')}}
                    <a href=""
                       title="{{__('a list of keywords that were found in both the first and second list (intersection)')}}"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
                <label title="{{__('a list of keywords that were found in any of the lists (combining)')}}"
                       class="radio font-weight-light">
                    <input type="radio" name="option" value="union" id="second-radio-option"
                           onclick="saveOptionState('second')">{{__('Unique phrases that are in either of the two lists')}}
                    <a href="" title="{{__('a list of keywords that were found in any of the lists (combining)')}}"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
                <label title="{{__('a list of keywords that are in the first list, but not in the second')}}"
                       class="radio font-weight-light">
                    <input type="radio" name="option" value="uniqueInFirstList" id="third-radio-option"
                           onclick="saveOptionState('third')">
                    {{__('Unique phrases that are only in the first list')}}
                    <a href="" title="{{__('a list of keywords that are in the first list, but not in the second')}}"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
                <label title="a list of keywords that are in the second list, but not in the first"
                       class="radio font-weight-light">
                    <input type="radio" name="option" value="uniqueInSecondList" id="fourth-radio-option"
                           onclick="saveOptionState('fourth')">
                    {{__('Unique phrases that are only in the second list')}}
                    <a href="" title="a list of keywords that are in the second list, but not in the first"
                       class="help-link">
                        <i aria-hidden="true" class="fa fa-question-circle"></i>
                    </a>
                </label>
            </div>
            <div class="col-sm-8 mt-3 mb-3 mt-3">
                <input class="btn btn-secondary" type="submit" value="{{__('Processing')}}">
            </div>
        </div>
    </form>
    @if (\Illuminate\Support\Facades\Session::has('result'))
        <div class="result mt-3">
            <textarea name="result" id="comparison-result" class="form-control"
                      rows="10">{{\Illuminate\Support\Facades\Session::get('result')}}</textarea>
            <div class="col-sm-12 d-flex">
                <a title="{{__('Copy result')}}">
                    <button onclick="saveOfBuffer()" class="btn btn-default mt-2 mr-2">
                        <i aria-hidden="true" class="fa fa-clipboard"></i>
                    </button>
                </a>
                <form action="{{route('download-comparison-file')}}" method="GET">
                    @csrf
                    <input type="hidden" value="{{\Illuminate\Support\Facades\Session::get('result')}}" name="result">
                    <a title="{{__('Download file')}}" class="pull-left ml-2 mr-2">
                        <button class="btn btn-default mt-2">
                            <i aria-hidden="true" class="fa fa-download"></i>
                        </button>
                    </a>
                </form>
            </div>
        </div>
    @endif
    @slot('js')
        <script>
            window.onload = function () {
                if (localStorage.getItem('radioOptionState') !== null) {
                    let index = localStorage.getItem('radioOptionState');
                    document.getElementById(index + '-radio-option').checked = true;
                }
            }

            function saveOptionState(index) {
                localStorage.setItem('radioOptionState', index)
            }

            function saveOfBuffer() {
                document.getElementById('comparison-result').select();
                document.execCommand('copy');
            }
        </script>
    @endslot
@endcomponent
