@component('component.card', ['title' => __('Highlighting unique words in the text')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/unique-words/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/unique-words/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>

        <style>
            .UniqueWords {
                background: oldlace;
            }
        </style>
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
                    <p>{{ __('Your file is ready, you can download it') }}</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('download-file') }}" method="POST" class="justify-content-between">
                        @csrf
                        <input class="file-path-input" type="hidden" name="fileName" value="">
                        <input class="btn btn-secondary" type="submit" value="{{ __('Download') }}">
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
            <p>{{ __('Data processing') }}</p>
            <div class="progress-bar" role="progressbar"></div>
        </div>
        <div id="progress-bar-table" class="mt-3 mb-3">
            <p>{{ __('Generating a table') }}</p>
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only"></span>
            </div>
        </div>
    </form>
    <div class="unique-words-filter mt-4 mb-3 card bg-gradient-light">
        <h3 class="m-3">{{__('Additionally')}}</h3>
        <div class="m-3">
            <div class="w-25">
                <p class="mr-3">{{__('Delete lines where the number of occurrences:')}}</p>
            </div>
            <div class="d-flex flex-column w-25 mt-2">
                <p>{{__('greater than or equal to:')}}</p>
                <input type="number" min="1" id="greaterOrEqual" class="form-control">
            </div>
            <div class="d-flex flex-column w-25 mt-2">
                <p>{{__('less than or equal to:')}}</p>
                <input type="number" min="1" id="lessOrEqual" class="form-control">
            </div>
            <div class="w-25 d-flex flex-column-reverse mt-2">
                <input type="button" class="btn btn-secondary btn-flat" value="{{__('Remove')}}" onclick="deleteItems()">
            </div>
        </div>
        <div class="d-flex row m-3">
            <div>
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
                    <span class="__helper-link ui_tooltip_w btn btn-default mt-2"
                          onclick="confirmTextForCopy()">
                    <i aria-hidden="true" class="fa fa-clipboard"></i>
                        <span class="ui_tooltip __right __l">
                            <span class="ui_tooltip_content">
                                {{__('Copy to Clipboard')}}
                            </span>
                        </span>
                    </span>
                    <button class="btn btn-default mt-2 __helper-link ui_tooltip_w"
                            data-toggle="modal"
                            data-target="#modal-default"
                            onclick="confirmTextForDownload()">
                        <i aria-hidden="true" class="fa fa-download"></i>
                        <span class="ui_tooltip __right __l">
                                <span class="ui_tooltip_content">
                                    {{__('Upload as a file')}}
                                </span>
                            </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                            let progressBarClass = $('.progress-bar');
                            let progressBarId = $("#progress-bar")
                            let xhr = $.ajaxSettings.xhr();
                            xhr.upload.addEventListener('progress', function (evt) {
                                progressBarClass.css({
                                    opacity: 1
                                });
                                progressBarId.show(400)
                                if (evt.lengthComputable) {
                                    let percent = Math.floor((evt.loaded / evt.total) * 100);
                                    setProgressBarStyles(percent)
                                    if (percent === 100) {
                                        $('#progress-bar-table').show(400)
                                        setTimeout(() => {
                                            progressBarClass.css({
                                                opacity: 0,
                                                width: 0 + '%'
                                            });
                                            progressBarId.hide(400)
                                        }, 2000)
                                    }
                                }
                            }, false);
                            return xhr;
                        },
                        success: function (response) {
                            $('fieldset.unique-words-filter.mt-4.mb-3').show(400)
                            $('div.unique-words-result').show(400)
                            let progressBarTableId = $('#progress-bar-table')
                            let progressBarTable = $('.progress-bar-table')
                            for (const [key, value] of Object.entries(response.list)) {
                                createRow(key, value)
                            }
                            setTimeout(() => {
                                progressBarTableId.hide(400);
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
                        url: "{{ route('create.file.unique.phrases') }}",
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

            function confirmTextForCopy() {
                var text = '';
                var result = confirmSeparatorAndTittle()
                let separator = result[0]
                document.querySelectorAll('.table-row').forEach((el) => {
                    if (document.getElementById('unique-word').checked) {
                        text += el.children[1].innerText + ';'
                    }
                    if (document.getElementById('unique-word-forms').checked) {
                        text += el.children[2].innerText + ';'
                    }
                    if (document.getElementById('number-occurrences').checked) {
                        text += el.children[3].innerText + ';'
                    }
                    if (document.getElementById('key-phrases').checked) {
                        let id = el.id.substr(16)
                        if (document.getElementById('unique-words-textarea-' + id)) {
                            text += document.getElementById('unique-words-textarea-' + id).value.replace('\n\n', separator)
                        } else {
                            text += document.getElementById('unique-words-td-id-' + id).innerHTML + separator
                        }
                    }
                })

                createElementForCopyInformationInBuffer(text)
            }

            function confirmTextForDownload() {
                var text = '';
                var result = confirmSeparatorAndTittle()
                let separator = result[0]
                text += result[1]
                document.querySelectorAll('.table-row').forEach((el) => {
                    if (document.getElementById('unique-word').checked) {
                        text += el.children[1].innerText + ';'
                    }
                    if (document.getElementById('unique-word-forms').checked) {
                        text += el.children[2].innerText + ';'
                    }
                    if (document.getElementById('number-occurrences').checked) {
                        text += el.children[3].innerText + ';'
                    }
                    if (document.getElementById('key-phrases').checked) {
                        let id = el.id.substr(16)
                        if (document.getElementById('unique-words-textarea-' + id)) {
                            let keyPhrases = document.getElementById('unique-words-textarea-' + id).innerHTML.split('\n\n')
                            for (let i = 0; i < keyPhrases.length; i++) {
                                text += keyPhrases[i] + '\n' + separator.slice(0, -1);
                            }
                        } else {
                            text += document.getElementById('unique-words-td-id-' + id).innerHTML + separator
                        }
                    }
                    text += '\n'
                })

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('create.file.unique.words') }}",
                    data: {
                        text: text,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('.file-path-input').attr('value', response.fileName)
                    }
                });
            }

            function confirmSeparatorAndTittle() {
                let separator = '';
                let title = '';
                if (document.getElementById('unique-word').checked) {
                    title += '{{ __('Word') }};'
                    separator += ';'
                }
                if (document.getElementById('unique-word-forms').checked) {
                    title += '{{ __('Word forms') }};';
                    separator += ';'
                }
                if (document.getElementById('number-occurrences').checked) {
                    title += '{{ __('Number of occurrences') }};';
                    separator += ';'
                }
                if (document.getElementById('key-phrases').checked) {
                    title += '{{ __('Key phrases') }};';
                    separator += ';'
                }
                title += '\n\n';

                return [separator, title]
            }

        </script>
    @endslot
@endcomponent
