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
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
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

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-default" data-toggle="modal" data-target=".modal">Экспортировать</button>
                </div>
            </div>

        </div>

    </div>

    @include('monitoring.keywords.modal.main')

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
        <!-- Moment js -->
        <script src="{{ asset('plugins/moment/moment-with-locales.min.js') }}"></script>
        <!-- Tempusdominus Bootstrap 4 -->
        <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            let $project = $('#project');

            $('.select2').select2({
                theme: 'bootstrap4'
            });

            $('.modal').on('show.bs.modal', function (event) {

                let modal = $(this);

                let ID = $project.val();

                axios.get(`/monitoring/${ID}/export/edit`).then(function (response) {
                    let content = response.data;
                    modal.find('.modal-content').html(content);

                    modal.find('select[name="mode"]').change(function(){
                        if($(this).val() === 'finance')
                            modal.find('#finance').removeClass('d-none');
                        else
                            modal.find('#finance').addClass('d-none');
                    });

                    //Date picker
                    modal.find('#startDatePicker, #endDatePicker').datetimepicker({
                        format: 'L',
                        locale: 'ru',
                    });

                    let $group = $("<div />", { "class" : "form-group" });
                    let $label = $("<label />").text("Корректировать позиции");
                    let $from = $("<input />", { "type" : "number", "min" : "1", "class" : "form-control mb-2", "name" : "from", "placeholder" : "С" });
                    let $to = $("<input />", { "type" : "number", "min" : "1", "class" : "form-control mb-2", "name" : "to", "placeholder" : "До" });
                    let $count = $("<input />", { "type" : "number", "min" : "1", "class" : "form-control mb-2", "name" : "count", "placeholder" : "Кол-во позиций" });

                    let $operator = $("<select />", { "name" : "operator", "class" : "custom-select mb-2" }).append([$("<option />").val("+").text("+"), $("<option />").val("-").text("-")]);

                    $group.append([$label, $from, $to, $operator, $count]);

                    modal.find('.form-group').eq(1).after($group);


                }).catch(function (error) {
                    modal.find('.modal-content').html(error);
                });
            });

        </script>
    @endslot


@endcomponent
