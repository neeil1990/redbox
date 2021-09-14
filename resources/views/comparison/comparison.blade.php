@component('component.card', ['title' => __('List comparison')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/list-comparison/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/list-comparison/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
    @endslot
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div id="toast-container" class="toast-top-right success-message">
        <div class="toast toast-success" aria-live="polite">
            <div class="toast-message">{{ __('The result was successfully copied to the clipboard') }}</div>
        </div>
    </div>
    <div id="toast-container" class="toast-top-right error-message">
        <div class="toast toast-error" aria-live="assertive">
            <div class="toast-message">{{ __('Both lists should not be empty') }}</div>
        </div>
    </div>
    <form id="list-comparison">
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
                <textarea class="form-control" name="firstList" rows="7" id="firstList" required></textarea>
            </div>
            <div class="col-sm-6 d-flex flex-column">
                <div class="d-flex flex-row justify-content-between">
                    <label>{{__('Second list')}}</label>
                    <div class="count-phrases">{{__('count phrases')}}: <span id="secondPhrases">0</span></div>
                </div>
                <textarea class="form-control" name="secondList" rows="7" id="secondList" required></textarea>
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
                <input class="btn btn-secondary" type="button" value="{{__('Processing')}}">
            </div>
        </div>
    </form>
    <div id="progress-bar">
        <div class="progress-bar mt-3 mb-3" role="progressbar"></div>
    </div>
    <form action="{{route('download.comparison.file')}}" method="GET" class="result-form">
        @csrf
        <div class="result mt-3">
            <div class="d-flex flex-row justify-content-between">
                <label>{{__('Comparison result')}}</label>
                <div class="count-phrases">{{__('count phrases')}}: <span id="numberPhrasesInResult">0</span></div>
            </div>
            <textarea name="result" id="comparison-result" class="form-control" rows="10"></textarea>
            <div class="d-flex">
                <span class="__helper-link ui_tooltip_w btn btn-default mt-2 mr-2" onclick="saveOfBuffer()">
                    <i aria-hidden="true" class="fa fa-clipboard"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('Copy to Clipboard')}}
                            </span>
                        </span>
                </span>
                <button class="btn btn-default mt-2 __helper-link ui_tooltip_w">
                    <i aria-hidden="true" class="fa fa-download"></i>
                    <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('Upload as a file')}}
                            </span>
                        </span>
                </button>
            </div>
        </div>
    </form>
    @slot('js')
        <script src="{{ asset('plugins/list-comparison/js/list-comparison.js') }}"></script>
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $(".btn.btn-secondary").click(function () {
                    var firstLists = $('#firstList').val();
                    var secondList = $('#secondList').val();
                    if (firstLists === '' || secondList === '') {
                        $('.error-message').show(300)
                        $('.result-form').hide(300)
                        setTimeout(() => {
                            $('.error-message').hide(300)
                        }, 3000)
                        return
                    }
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('counting.list.comparison') }}",
                        data: {
                            firstList: firstLists,
                            secondList: secondList,
                            option: $('.custom-control-input:checked').val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        xhr: function () {
                            let xhr = $.ajaxSettings.xhr();
                            xhr.upload.addEventListener('progress', function (evt) {
                                $('.progress-bar').css({
                                    opacity: 1
                                });
                                $("#progress-bar").show(300)
                                if (evt.lengthComputable) {
                                    let percent = Math.floor((evt.loaded / evt.total) * 100);
                                    setProgressBarStyles(percent)
                                    if (percent === 100) {
                                        setTimeout(() => {
                                            $('.progress-bar').css({
                                                opacity: 0,
                                                width: 0 + '%'
                                            });
                                            $("#progress-bar").hide(300)
                                        }, 2000)
                                    }
                                }
                            }, false);
                            return xhr;
                        },
                        success: function (response) {
                            $('.result-form').show(400)
                            $('#comparison-result').val(response.data.result)
                            comparisonResult()
                        },
                    });
                });
            });

            function setProgressBarStyles(percent) {
                $('.progress-bar').css({
                    width: percent + '%'
                })
                document.querySelector('.progress-bar').innerText = percent + '%'
            }
        </script>
    @endslot
@endcomponent
