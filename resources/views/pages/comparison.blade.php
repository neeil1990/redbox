@component('component.card', ['title' => __('List comparison')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/list-comparison/css/style.css') }}"/>
    @endslot
    <form action="{{  route('counting.list.comparison') }}" method="POST" id="list-comparison">
        @csrf
        <div class="row">
            <div class="col-sm-12 textpart">
                <h3>{{__('Compare the lists of keywords and get a common unique list.')}}</h3>
            </div>
        </div>
        <div class="row mt-3 mb-3">
            <div class="col-sm-6 d-flex flex-column">
                <div class="d-flex flex-row justify-content-between">
                    <label>{{__('First list')}}</label>
                    <div class="count-phrases">{{__('count phrases')}}: <span id="firstPhrases">0</span></div>
                </div>
                <textarea class="form-control" name="firstList" rows="7" id="firstList"
                          required>{{\Illuminate\Support\Facades\Input::old('firstList')}}</textarea>
            </div>
            <div class="col-sm-6 d-flex flex-column">
                <div class="d-flex flex-row justify-content-between">
                    <label>{{__('Second list')}}</label>
                    <div class="count-phrases">{{__('count phrases')}}: <span id="secondPhrases">0</span></div>
                </div>
                <textarea class="form-control" name="secondList" rows="7" id="secondList"
                          required>{{\Illuminate\Support\Facades\Input::old('secondList')}}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-8 d-flex flex-column">
                <label class="mt-3 mb-3">{{__('Comparison type:')}}</label>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input"
                           type="radio"
                           name="option"
                           value="unique"
                           id="first-radio-option"
                           checked
                           onclick="saveOptionState('first')">
                    <label for="first-radio-option"
                           class="custom-control-label">{{__('Unique phrases that are in each of the two lists')}}
                        <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('a list of keywords that were found in both the first and second list (intersection)')}}
                            </span>
                        </span>
                    </span>
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input"
                           type="radio"
                           name="option"
                           value="union"
                           id="second-radio-option"
                           onclick="saveOptionState('second')">
                    <label for="second-radio-option"
                           class="custom-control-label">{{__('Unique phrases that are in either of the two lists')}}
                        <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('a list of keywords that were found in any of the lists (combining)')}}
                            </span>
                        </span>
                    </span>
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input"
                           type="radio"
                           name="option"
                           value="uniqueInFirstList"
                           id="third-radio-option"
                           onclick="saveOptionState('third')">
                    <label for="third-radio-option"
                           class="custom-control-label">{{__('Unique phrases that are only in the first list')}}
                        <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('a list of keywords that are in the first list, but not in the second')}}
                            </span>
                        </span>
                    </span>
                    </label>
                </div>
                <div class="custom-control custom-radio">
                    <input class="custom-control-input"
                           type="radio"
                           name="option"
                           value="uniqueInSecondList"
                           id="fourth-radio-option"
                           onclick="saveOptionState('fourth')">
                    <label for="fourth-radio-option"
                           class="custom-control-label">{{__('Unique phrases that are only in the second list')}}
                        <span class="__helper-link ui_tooltip_w">
                    <i class="fa fa-question-circle"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('a list of keywords that are in the second list, but not in the first')}}
                            </span>
                        </span>
                    </span>
                    </label>
                </div>
            </div>
            <div class="col-sm-8 mt-3 mb-3 mt-3">
                <input class="btn btn-secondary" type="submit" value="{{__('Processing')}}">
            </div>
        </div>
    </form>
    @if (\Illuminate\Support\Facades\Session::has('result'))
        <div class="result mt-3">
            <div class="d-flex flex-row justify-content-between">
                <label>{{__('Comparison result')}}</label>
                <div class="count-phrases">{{__('count phrases')}}: <span id="numberPhrasesInResult">0</span></div>
            </div>
            <textarea name="result" id="comparison-result" class="form-control"
                      rows="10">{{\Illuminate\Support\Facades\Session::get('result')}}</textarea>
            <div class="d-flex">
                <a title="{{__('Copy result')}}">
                    <button onclick="saveOfBuffer()" class="btn btn-default mt-2 mr-2">
                        <i aria-hidden="true" class="fa fa-clipboard"></i>
                    </button>
                </a>
                <form action="{{route('download.comparison.file')}}" method="GET">
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
        <script src="{{ asset('plugins/list-comparison/js/list-comparison.js') }}"></script>
    @endslot
@endcomponent
