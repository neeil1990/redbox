@component('component.card', ['title' => __('Http headers')])

    @slot('css')
        <!-- CodeMirror -->
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/codemirror.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/theme/monokai.css') }}">
        <!-- jQuery ui -->
        <link rel="stylesheet" href="{{ asset('plugins/jquery-ui/jquery-ui.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

        <style>
            .dt-buttons {
                padding: 5px 0;
            }
        </style>
    @endslot

    @if(Auth()->check())
        <div class="row mb-4">
            <div class="col-md-6">
                {!! Form::open(['method' => 'GET', 'route' => 'pages.headers']) !!}
                <label>{{ __('To check one link') }}</label>
                <div class="input-group input-group-sm">
                    {!! Form::text('url', request('url', $default = null), ['class' => 'form-control' . ($errors->has('url') ? ' is-invalid' : ''), 'placeholder' => __('URL')]) !!}
                    <span class="input-group-append">
                    {!! Form::submit(__('Check URL'), ['class' => 'btn btn-secondary btn-flat']) !!}
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    @endif

    <response-http-code submit="{{ __('Send') }}"
                        url-title="{{ __('url') }}"
                        code-title="{{ __('code') }}"
                        text-title="{{ __('Bulk check up to 500 pieces at a time') }}"
                        timeout-title="{{ __('Timeout between requests in ms') }}"
                        export-btn="{{ __('Export') }}"
                        open-new-page="{{ __('Open in a new window') }}"
                        more="{{ __('More') }}"
    ></response-http-code>

    @if($response)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __('Copy link') }}:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="inputCopy" value="{{ request()->getHost() }}/public/http-headers/{{$id}}"
                               class="form-control">
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="copy()" style="cursor: pointer"><i
                                    class="fas fa-copy"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach($response as $arItems)
                <div class="col-md-12">
                    <div class="card card-outline @if($arItems['status'] == 200) card-success @else card-danger @endif">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('HTTP Code') }}: {{ $arItems['status'] }}</h3>
                        </div>
                        <div class="card-body p-0 overflow-auto">
                            <table class="table table-striped">
                                <tbody>
                                @foreach($arItems['headers'] as $name => $val)
                                    <tr>
                                        <td><strong>{{ $name }}</strong></td>
                                        <td>@if(is_array($val)) {{implode(', ', $val)}} @else {{ $val }} @endif</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('HTML Code') }}</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <textarea id="code">{{last($response)['content']}}</textarea>
                    </div>
                </div>
            </div>
        </div>

        @slot('js')
            <!-- CodeMirror -->
            <script src="{{ asset('plugins/codemirror/codemirror.js') }}"></script>
            <script src="{{ asset('plugins/codemirror/mode/css/css.js') }}"></script>
            <script src="{{ asset('plugins/codemirror/mode/xml/xml.js') }}"></script>
            <script src="{{ asset('plugins/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script>

            <script>
                $(function () {
                    // CodeMirror
                    CodeMirror.fromTextArea(document.getElementById("code"), {
                        mode: "htmlmixed",
                        //theme: "monokai",
                        lineNumbers: true,
                    });
                })
            </script>

            <script src="{{ asset('plugins/jquery-ui/jquery-ui.js') }}"></script>
            <script>
                $(function () {
                    $(".CodeMirror").resizable();
                });

                function copy() {
                    var copyText = document.getElementById("inputCopy");

                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand("copy");

                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: "{{ __('Copied link') }}",
                        subtitle: "{{ __('Close') }}",
                        body: copyText.value,
                        autohide: true,
                        delay: 2000,
                    });
                }
            </script>
        @endslot
    @endif

    @slot('js')
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/jszip/jszip.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.print.js') }}"></script>
    @endslot


@endcomponent
