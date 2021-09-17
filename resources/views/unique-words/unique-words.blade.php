@component('component.card', ['title' => __('Highlighting unique words in the text')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/unique-words/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/unique-words/css/style.css') }}"/>
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
            <div class="toast-message">{{ __('The list of keywords should not be empty') }}</div>
        </div>
    </div>
    <div class="modal fade" id="modal-default" style="display: none;" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p>Ваш файл готов, вы можете его скачать</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('download-file') }}" method="POST" class="justify-content-between">
                        @csrf
                        <input class="file-path-input" type="hidden" name="fileName" value="">
                        <input class="btn btn-secondary" type="submit" value="Скачать">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <form>
        @csrf
        <h2 class="mt-3 mb-3">{{__('Get a list of unique words from the list of keywords')}}</h2>
        <div class="d-flex flex-column unique-words">
            <div class="d-flex flex-row justify-content-between col-12 pl-0 pr-0">
                <label>{{__('List of phrases')}}</label>
                <div class="count-phrases text-right">{{__('count phrases')}}:
                    <span id="countPhrases">0</span>
                </div>
            </div>
            <textarea class="form-control col-12"
                      name="phrases"
                      rows="10"
                      id="phrases"
                      required></textarea>
        </div>
        <input class="btn btn-secondary mt-3 mr-2 d-flex align-items-center" type="button" value="{{__('Processing')}}">
        <div id="progress-bar" class="mt-3 mb-3">
            Обработка данных
            <div class="progress-bar" role="progressbar"></div>
        </div>
        <div id="progress-bar-table" class="mt-3 mb-3">
            Генерация таблицы
            <div class="progress-bar-table" role="progressbar"></div>
        </div>
    </form>
    <fieldset class="unique-words-filter mt-4 mb-3">
        <legend>{{__('Additionally')}}</legend>
        <div class="d-flex mt-2 mb-2">
            <div class="w-auto">
                <p class="mr-3">{{__('Delete lines where the number of occurrences:')}}</p>
            </div>
            <div class="d-flex flex-column w-auto mr-1 ml-4">
                <p>{{__('greater than or equal to:')}}</p>
                <input type="number" min="1" id="greaterOrEqual" class="form-control">
            </div>
            <div class="d-flex flex-column w-auto ml-1 mr-4">
                <p>{{__('less than or equal to:')}}</p>
                <input type="number" min="1" id="lessOrEqual" class="form-control">
            </div>
            <div class="w-auto d-flex flex-column-reverse ml-3">
                <input type="button" class="btn btn-secondary btn-flat" value="{{__('Remove')}}"
                       onclick="deleteItems()">
            </div>
        </div>
        <div class="d-flex row mt-3 mb-3">
            <form class="col-sm-12 d-flex flex-column" method="POST" action="{{ route('download.unique.words') }}">
                @csrf
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           id="unique-word"
                           name="uniqueWord"
                           class="custom-control-input"
                           checked>
                    <label for="unique-word" class="custom-control-label">
                        {{__('Word')}}
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           id="unique-word-forms"
                           name="uniqueWordForms"
                           class="custom-control-input"
                           checked>
                    <label for="unique-word-forms" class="custom-control-label">
                        {{__('Word forms')}}
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           id="number-occurrences"
                           name="numberOccurrences"
                           class="custom-control-input"
                           checked>
                    <label for="number-occurrences" class="custom-control-label">
                        {{__('Number of occurrences')}}
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           id="key-phrases"
                           name="keyPhrases"
                           class="custom-control-input"
                           checked>
                    <label for="key-phrases" class="custom-control-label">
                        {{__('Key phrases')}}
                    </label>
                </div>
                <div class="flex">
                    <span class="__helper-link ui_tooltip_w btn btn-default mt-2" onclick="saveInBuffer()">
                    <i aria-hidden="true" class="fa fa-clipboard"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('Copy to Clipboard')}}
                            </span>
                        </span>
                    </span>
                    <input type="hidden" id="extraId" name="extraId">
                    <input type="hidden"
                           name="phrases"
                           value="@if (isset($oldPhrases)){{$oldPhrases}}@endif"
                           checked>
                    <button class="btn btn-default mt-2 __helper-link ui_tooltip_w">
                        <i aria-hidden="true" class="fa fa-download"></i>
                        <span class="ui_tooltip __right __l">
                                <span class="ui_tooltip_content">
                                    {{__('Upload as a file')}}
                                </span>
                            </span>
                    </button>
                </div>
            </form>
        </div>
    </fieldset>
    <div class="card mt-3 mb-3 unique-words-result">
        <div class="card-header border-bottom">
            <h2 class="card-title">{{__('Result')}}</h2>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-striped table-valign-middle" id="result-table">
                <thead>
                <tr>
                    <th></th>
                    <th>{{__('Word')}}</th>
                    <th>{{__('Word forms')}}</th>
                    <th>{{__('Number of occurrences')}}</th>
                    <th style="width: 50%">{{__('Key phrases')}}</th>
                </tr>
                </thead>
                <tbody class="table-body">
                {{--                @foreach($listWords as $key => $list)--}}
                {{--                    <tr id="unique-words-id-" class="unique-result">--}}
                {{--                        <td>--}}
                {{--                            <i class="fa fa-trash" onclick="deleteItem({{$key}})"></i>--}}
                {{--                        </td>--}}
                {{--                        <td class="unique-word">{{$list['word']}}</td>--}}
                {{--                        <td class="unique-word-form">{{$list['wordForms']}}</td>--}}
                {{--                        <td class="number-occurrences">{{$list['numberOccurrences']}}</td>--}}
                {{--                        <td class="d-flex flex-column unique-key-phrases">--}}
                {{--                                            @if(isset($list['keyPhrases'][1]))--}}
                {{--                <form action="{{ route('download.unique.phrases') }}"--}}
                {{--                      method="POST"--}}
                {{--                      id="unique-form"--}}
                {{--                      class="unique-form">--}}
                {{--                    @csrf--}}
                {{--                    <div class="flex-column">--}}
                {{--                        <span class="__helper-link ui_tooltip_w mr-1 btn btn-default mb-1"--}}
                {{--                              onclick="savePhrasesInBuffer()">--}}
                {{--                            <i aria-hidden="true"--}}
                {{--                               class="fa fa-clipboard"></i>--}}
                {{--                            <span class="ui_tooltip __left __l">--}}
                {{--                                <span class="ui_tooltip_content">--}}
                {{--                                    {{__('Copy to Clipboard')}}--}}
                {{--                                </span>--}}
                {{--                            </span>--}}
                {{--                        </span>--}}
                {{--                        <span class="__helper-link ui_tooltip_w">--}}
                {{--                            <button class="btn btn-default  mb-1">--}}
                {{--                                <i aria-hidden="true"--}}
                {{--                                   class="fa fa-download"></i>--}}
                {{--                            </button>--}}
                {{--                            <span class="ui_tooltip __right __l">--}}
                {{--                            <span class="ui_tooltip_content">--}}
                {{--                                {{__('Upload as a file')}}--}}
                {{--                            </span>--}}
                {{--                        </span>--}}
                {{--                        </span>--}}
                {{--                    </div>--}}
                {{--                    <textarea name="keyPhrases" id="key-phrases-" rows="3"--}}
                {{--                              class="form-control key-phrases-result unique-element-key-phrases"></textarea>--}}
                {{--                </form>--}}
                </tbody>
            </table>
        </div>
    </div>
    @slot('js')
        <script src="{{ asset('plugins/unique-words/js/unique-words.js') }}"></script>
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $(".btn.btn-secondary.mt-3.mr-2.d-flex.align-items-center").click(function () {
                    let phrases = $('#phrases').val();
                    let errorMsg = $('.error-message')
                    let uniqueRes = $('div.unique-words-result')
                    if (phrases === '') {
                        errorMsg.show(300)
                        uniqueRes.hide(300)
                        setTimeout(() => {
                            errorMsg.hide(300)
                        }, 5000)

                        return
                    }
                    $('.table-row').remove()
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('unique.words') }}",
                        data: {
                            phrases: phrases,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        xhr: function () {
                            let progressBar = $('.progress-bar');
                            let progressBarId = $("#progress-bar")
                            let xhr = $.ajaxSettings.xhr();
                            xhr.upload.addEventListener('progress', function (evt) {
                                progressBar.css({
                                    opacity: 1
                                });
                                progressBarId.show(400)
                                if (evt.lengthComputable) {
                                    let percent = Math.floor((evt.loaded / evt.total) * 100);
                                    setProgressBarStyles(percent)
                                    if (percent === 100) {
                                        setTimeout(() => {
                                            progressBarId.hide(400)
                                            progressBar.css({
                                                opacity: 0,
                                                width: 0 + '%'
                                            }, 2000);
                                        })
                                    }
                                }
                            }, false);
                            return xhr;
                        },
                        success: function (response) {
                            $('#progress-bar-table').show(400);
                            $('fieldset.unique-words-filter.mt-4.mb-3').show(400)
                            $('div.unique-words-result').show(400)
                            let step = calculatePercentTableGeneration(response.length)
                            let percent = 0
                            let progressBarTable = $('.progress-bar-table')
                            for (const [key, value] of Object.entries(response.list)) {
                                percent += step
                                progressBarTable.text(Math.round(percent) + '%');
                                progressBarTable.css({
                                    width: percent + '%'
                                })
                                createRow(key, value)
                            }
                            setTimeout(() => {
                                $('#progress-bar-table').hide(400);
                                progressBarTable.css({
                                    opacity: 0,
                                    width: 0 + '%'
                                });
                            }, 2000)
                        },
                    });
                });
            });

            function createClipboardIcon(key) {
                let span = document.createElement('span')
                span.className = '__helper-link ui_tooltip_w mr-1 btn btn-default clipboard'
                span.onclick = function () {
                    savePhrasesInBuffer(key)
                }
                let clipboard = document.createElement('i')
                clipboard.className = 'fa fa-clipboard'
                let helperSpan = document.createElement('span')
                helperSpan.className = 'ui_tooltip __left __l'
                let underHelperSpan = document.createElement('span')
                underHelperSpan.className = 'ui_tooltip_content'
                underHelperSpan.innerText = '{{__('Copy to Clipboard')}}'
                helperSpan.appendChild(underHelperSpan)
                span.appendChild(clipboard)
                span.appendChild(helperSpan)

                return span;
            }

            function createDownloadIcon(key) {
                let span = document.createElement('span')
                span.className = '__helper-link ui_tooltip_w btn btn-default'
                span.setAttribute('data-toggle', 'modal');
                span.setAttribute('data-target', '#modal-default');
                span.id = key
                span.onclick = function () {
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('createFile.unique.phrases') }}",
                        data: {
                            keyPhrases: $('#unique-words-textarea-' + key).val(),
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            $('.file-path-input').attr('value', response.fileName)
                        },
                    });
                }
                let download = document.createElement('i')
                download.className = 'fa fa-download'
                let helperSpan = document.createElement('span')
                helperSpan.className = 'ui_tooltip __right __l'
                let underHelperSpan = document.createElement('span')
                underHelperSpan.className = 'ui_tooltip_content'
                underHelperSpan.innerText = '{{__('Upload as a file')}}'
                helperSpan.appendChild(underHelperSpan)
                span.appendChild(download)
                span.appendChild(helperSpan)

                return span;
            }

            function createHelpIcons() {
                // <div class="mb-1">
                //     <i aria-hidden="true"
                //        class="fa fa-plus-square-o"
                //        id="unique-plus"
                //        onclick="$('#test-res').show(); $('.fa.fa-minus-square-o').show(); $('.fa.fa-plus-square-o').hide()"></i>
                //     <span id="unique-span"></span>
                //     <i aria-hidden="true"
                //        class="fa fa-minus-square-o"
                //        id="unique-minus"
                //        onclick="$('#test-res').hide();  $('.fa.fa-plus-square-o').show(); $('.fa.fa-minus-square-o').hide()"></i>
                // </div>
            }

        </script>
    @endslot
@endcomponent
