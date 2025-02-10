@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <!-- daterange picker -->
        <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
        <!-- CodeMirror -->
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/codemirror.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/theme/monokai.css') }}">
    @endslot

    <div class="row">
        <div class="col-6">
            @include('monitoring.admin._btn')

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Форма</h3>
                </div>

                <div class="card-body">

                    <div class="form-group">
                        <label>Проект</label>
                        <select class="form-control select2" id="project">
                            <option value="">Выберите проект</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }} - {{ $project->url }} [{{ $project->id }}]</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Поисковая система</label>
                        <select class="form-control select2" id="search-engine">
                            <option value="">Сначала выберите проект</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Дата</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text">
                                <i class="far fa-calendar-alt"></i>
                              </span>
                            </div>

                            <input type="text" class="form-control float-right" id="range" disabled="disabled">
                        </div>
                        <!-- /.input group -->
                    </div>

                </div>
            </div>

        </div>
        <div class="col-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        Процесс
                    </h3>
                </div>

                <div class="card-body p-0">
                    <textarea id="codeMirror" class="p-3"></textarea>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <!-- moment -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
        <!-- CodeMirror -->
        <script src="{{ asset('plugins/codemirror/codemirror.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/css/css.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/xml/xml.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            let $project = $('#project');
            let $engine = $('#search-engine');
            let $range = $('#range');
            let $textarea = $('#codeMirror');

            $range.daterangepicker();

            $('.select2').select2({
                theme: 'bootstrap4'
            });

            $project.on('change', function() {
                let id = $(this).val();

                $engine.empty().append('<option value="">Загрузка...</option>').prop('disabled', true);
                $range.prop('disabled', true);

                if (id) {
                    $.ajax({
                        url: '{{ route('get.search.engines') }}',
                        type: 'GET',
                        data: { id: id },
                        dataType: 'json',
                        success: function(data) {
                            $engine.empty().append('<option value="">Выберите поисковую систему</option>');

                            $.each(data, function(key, value) {
                                $engine.append('<option value="' + value.id + '">' + value.location.name + ' - ' + value.location.lr + '</option>');
                            });

                            $engine.prop('disabled', false);
                        }
                    });
                } else {
                    $engine.empty().append('<option value="">Сначала выберите категорию</option>').prop('disabled', true);
                }
            });

            $engine.on('change', function() {
                let project = $project.val();
                let engine = $(this).val();

                if (project && engine) {
                    $range.prop('disabled', false);
                } else {
                    $range.prop('disabled', true);
                }
            });

            $range.on('apply.daterangepicker', function(ev, picker) {
                $.ajax({
                    url: '{{ route('insert.positions') }}',
                    type: 'GET',
                    data: {
                        projectId: $project.val(),
                        engineId: $engine.val(),
                        startDate: picker.startDate.format('YYYY-MM-DD'),
                        endDate: picker.endDate.format('YYYY-MM-DD'),
                    },
                    dataType: 'json',
                    success: function(data) {
                        $engine.empty().append('<option value="">Выберите поисковую систему</option>');

                        $.each(data, function(key, value) {
                            $engine.append('<option value="' + value.id + '">' + value.location.name + ' - ' + value.location.lr + '</option>');
                        });

                        $engine.prop('disabled', false);
                    }
                });
            });

            let editor = CodeMirror.fromTextArea(document.getElementById("codeMirror"), {
                mode: "htmlmixed",
                theme: "monokai",
                lineNumbers: true,
            });

            window.Echo.channel("monitoring").listen("MonitoringPositionInsert", (event) => {
                let text = event.position.created_at + " " + event.position.keyword.query + " - " + event.position.position + " \n";
                editor.replaceRange("Добавлено: " + text, CodeMirror.Pos(editor.lastLine()));
                editor.scrollTo(null, editor.getScrollInfo().height);
            });

            window.Echo.channel("monitoring").listen("MonitoringPositionPassed", (event) => {
                let text = "Пропущено: " + event.date + " " + event.key.query + " \n";
                editor.replaceRange(text, CodeMirror.Pos(editor.lastLine()));
                editor.scrollTo(null, editor.getScrollInfo().height);
            });

        </script>
    @endslot


@endcomponent
