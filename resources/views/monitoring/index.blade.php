@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

        <style>
            .table-hover tbody tr:hover {
                background-color: #FFF;
            }
            .table-hover tbody tr.main:hover {
                color: #212529;
                background-color: rgba(0,0,0,.075);
            }
            .dataTables_processing {
                margin: 10px auto;
                z-index: 4;
            }
            .grow-color {
                background-color: rgb(153, 228, 185);
            }
            .shrink-color {
                background-color: rgb(251, 225, 223);
            }

        </style>
    @endslot

    @hasanyrole('Super Admin|admin')
    <div class="row mb-2">
        <div class="col-6">
            @include('monitoring.admin._btn')
        </div>
    </div>
    @endhasanyrole

    <div class="row mb-1">
        @include('monitoring.partials._buttons')
    </div>

    <div class="row">
        @include('monitoring.partials._table')
    </div>

    @include('monitoring.keywords.modal.main')

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <!-- Moment js -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <!-- Papa parse -->
        <script src="{{ asset('plugins/papaparse/papaparse.min.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

        <script>
            const LENGTH_MENU = JSON.parse('{{ $lengthMenu }}');
            const PAGE_LENGTH = '{{ $length }}';

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "5000"
            };

            const HIGHLIGHT_TR_CLASS = "table-success";

            //Enable check and uncheck all functionality
            $('.checkbox-toggle').click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $('.table tbody tr.main').removeClass(HIGHLIGHT_TR_CLASS);
                    $('.table tbody tr.main').find('.form-check-input').prop('checked', false);
                    $('.checkbox-toggle .far.fa-check-square').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check all checkboxes
                    $('.table tbody tr.main').addClass(HIGHLIGHT_TR_CLASS);
                    $('.table tbody tr.main').find('.form-check-input').prop('checked', true);
                    $('.checkbox-toggle .far.fa-square').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks)
            });

            $('[data-toggle="tooltip"]').tooltip();

            let table = $('#projects').DataTable({
                dom: '<"card-header"<"card-title"><"float-right"f><"float-right"l>><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                lengthMenu: LENGTH_MENU,
                pageLength: PAGE_LENGTH,
                pagingType: "simple_numbers",
                language: {
                    lengthMenu: "_MENU_",
                    search: "_INPUT_",
                    searchPlaceholder: "Search project",
                    paginate: {
                        "first":      "«",
                        "last":       "»",
                        "next":       "»",
                        "previous":   "«"
                    },
                    processing: '<img src="/img/1485.gif" style="width: 50px; height: 50px;">',
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/monitoring/projects/get',
                    type: 'POST',
                },
                order: [
                    [2, 'asc'],
                ],
                columnDefs: [
                    { orderable: true, targets: 2 },
                    { orderable: true, targets: 3 },
                    { orderable: false, targets: '_all' },
                ],
                columns: [
                    {
                        orderable: false,
                        data: function (row, type, val, meta){

                            let form = $('<div />', {
                                class: 'form-check'
                            });

                            let input = $('<input />', {
                                class: 'form-check-input'
                            });

                            input.attr({
                                type: 'checkbox',
                                value: row.id,
                            });

                            return form.append(input)[0].outerHTML;
                        },
                    },
                    {
                        orderable: false,
                        data: null,
                        defaultContent: '<a href="#" class="dt-control text-muted"><i class="fas fa-plus-circle"></i></a>',
                    },
                    {
                        title: 'Название проекта',
                        data: 'name',
                        name: 'name',
                    },
                    {
                        title: 'Домен',
                        data: 'url',
                        name: 'url',
                    },
                    {
                        title: 'Поисковики',
                        data: 'searches'
                    },
                    {
                        title: 'Слов',
                        data: 'count'
                    },
                    {
                        title: 'Ср.Позиция',
                        data: 'middle_position',
                    },
                    {
                        title: '% в ТОП 3',
                        data: 'top_three',
                    },
                    {
                        title: '% в ТОП 5',
                        data: 'top_fifth',
                    },
                    {
                        title: '% в ТОП 10',
                        data: 'top_ten',
                    },
                    {
                        title: '% в ТОП 30',
                        data: 'top_thirty',
                    },
                    {
                        title: '% в ТОП 100',
                        data: 'top_one_hundred',
                    },
                    {
                        width: '120px',
                        title: 'Отчеты',
                        data: null,
                        class: 'project-actions text-right',
                        defaultContent: '<a class="btn btn-info btn-sm" href="#">{{ __('In progress') }}</a>',
                    },
                    {
                        width: '120px',
                        data: function(row) {

                            let create = $('<a />', { class: 'btn btn-sm btn-success tooltip-on'}).append($('<i />', { class: 'fas fa-plus'}));

                            create.attr({
                                "data-toggle": 'modal',
                                "data-target": '.modal',
                                "data-type": 'create_keywords',
                                "data-id": row.id,
                                title: 'Добавить запрос',
                            });

                            let edit = $('<a />', { class: 'btn btn-sm btn-info'}).append($('<i />', { class: 'fas fa-save'}));
                            let trash = $('<a />', { class: 'btn btn-sm btn-danger'}).append($('<i />', { class: 'fas fa-trash'}));

                            trash.attr('onclick', `onClickDeleteProject(${row.id})`);

                            return create[0].outerHTML + " " + edit[0].outerHTML + " " + trash[0].outerHTML;
                        },
                        class: 'project-actions text-right',
                    },
                ],
                initComplete: function () {
                    let api = this.api();

                    let json = api.ajax.json();

                    this.find('tbody').on('click', 'tr.main', function(){
                        $(this).toggleClass(HIGHLIGHT_TR_CLASS);

                        if($(this).hasClass(HIGHLIGHT_TR_CLASS)){
                            $(this).find('.form-check-input').prop('checked', true);
                        }else{
                            $(this).find('.form-check-input').prop('checked', false);
                        }
                    });

                    this.find('tbody').on('click', 'td .dt-control', function () {
                        let icon = $(this).find('i');
                        let tr = $(this).closest('tr');
                        let row = api.row(tr);

                        if (row.child.isShown()) {
                            // This row is already open - close it
                            row.child.hide();
                            tr.removeClass('shown');

                            icon.removeClass('fa-minus-circle');
                            icon.addClass('fa-plus-circle');
                        } else {
                            // Open this row
                            let data = row.data();
                            let loading = $('#projects_processing');

                            loading.css('display', 'block');
                            axios.get(`/monitoring/${data.id}/child-rows/get`).then(function(response){
                                loading.css('display', 'none');

                                let content = $(response.data);

                                $.each(content.find('.top'), function(i, el){

                                    let str = $(el).text();

                                    if(str.indexOf('+') > 0)
                                        $(el).addClass('grow-color');

                                    if(str.indexOf('-') > 0)
                                        $(el).addClass('shrink-color');
                                });

                                row.child(content).show();
                            });

                            tr.addClass('shown');

                            icon.removeClass('fa-plus-circle');
                            icon.addClass('fa-minus-circle')
                        }

                        return false;
                    });

                    // header card
                    this.closest('.card').find('.card-header .card-title').html("");
                    this.closest('.card').find('.card-header label').css('margin-bottom', 0);

                    let dataTimeCache = $('<span />', {class: "data-time-cache"}).text(json.cache.date);
                    let CacheText = `Сводные данные в таблице актуальны на дату: ${dataTimeCache[0].outerHTML} `;
                    let updateCacheIcon = $('<i />', {class: "fas fa-sync-alt"});
                    let updateCacheButton = $('<a />', {
                        class: "text-muted",
                        href: "javascript:void(0)"
                    }).html(updateCacheIcon);

                    updateCacheButton.click(function(){
                        let btn = $(this);
                        axios.get('/monitoring/project/remove/cache')
                            .then(function () {
                                table.draw(false);
                                btn.prev('.data-time-cache').text(moment().format("DD.MM.YYYY H:m"))
                            });
                        return false;
                    });

                    let updateCacheText = $('<div />', {class: "card-title ml-2"}).html(CacheText);
                    updateCacheText.append(updateCacheButton);
                    let updateCacheContainer = $('<div />', {class: "float-left"}).html(updateCacheText);
                    this.closest('.card').find('.card-header .card-title').after(updateCacheContainer);
                },
                drawCallback: function(){
                    this.find('tbody tr').addClass('main');
                    $('.pagination').addClass('pagination-sm');
                },
            });

            $('.parse-positions').click(function () {

                let rows = table.rows('.' + HIGHLIGHT_TR_CLASS);
                let data = rows.data();

                $.each(data, function(index, row){
                    axios.post('/monitoring/parse/positions/project', {
                        projectId: row.id
                    });
                });

                if(data.length)
                    toastr.success("{{ __('Task add in queue') }}");
                else
                    toastr.error("{{ __('Selected project') }}");
            });

            $('.checkbox-delete').click(function(){

                let rows = table.rows('.' + HIGHLIGHT_TR_CLASS);
                let data = rows.data();

                if(!data.length){
                    toastr.error("{{ __('Selected project') }}");
                    return false;
                }

                if (!window.confirm("{{__('Do you really want to delete?')}}"))
                    return false;

                $.each(data, function(index, row){
                    deleteProject(row.id);
                });
            });

            function onClickDeleteProject(id){

                if (!window.confirm("{{__('Do you really want to delete?')}}"))
                    return false;

                deleteProject(id);
            }

            function deleteProject(id)
            {
                if(id)
                    axios.delete(`monitoring/${id}`)
                        .then(function () {
                            table.draw(false);
                    });
                else
                    alert('Delete error');
            }

            $('.modal').on('show.bs.modal', function (event) {

                let modal = $(this);
                let button = $(event.relatedTarget);
                let type = button.data('type');
                let projectId = button.data('id');

                if(type === 'create_keywords'){

                    axios.get(`/monitoring/keywords/${projectId}/create`).then(function (response) {

                        let content = response.data;

                        modal.find('.modal-content').html(content);
                        modal.find('#upload-queries').click(function () {

                            let self = $(this);
                            let csv = self.closest('.input-group').find('#upload');

                            if(csv[0].files.length && csv[0].files[0].type === 'text/csv'){

                                csv.parse({
                                    config: {
                                        skipEmptyLines: 'greedy',
                                        complete: function (result) {

                                            let value = '';
                                            $.each(result.data, function(i, item){

                                                if(item[0])
                                                    value += item[0] + '\r\n';
                                            });

                                            modal.find('textarea[name="query"]').val(value);
                                        },
                                        download: 0
                                    }
                                });

                            }else{

                                toastr.error('Загрузите файл формата .csv');
                            }
                        });
                    }).then(function () {

                        let group = modal.find('.custom-select[name="monitoring_group_id"]');
                        if(group.length){

                            group.select2({
                                theme: 'bootstrap4'
                            });

                            modal.find('#create-group').click(function(){
                                let el = $(this);
                                let input = el.closest('.input-group').find('input');

                                if(input.val()){

                                    let id_project = input.data('id');

                                    axios.post('/monitoring/groups', {
                                        monitoring_project_id: id_project,
                                        type: "keyword",
                                        name: input.val(),
                                    }).then(function (response) {

                                        let newOption = new Option(response.data.name, response.data.id, false, true);
                                        group.append(newOption).trigger('change');

                                        input.val(null);
                                    }).catch(function (error) {

                                        toastr.error('Something is going wrong');
                                    });
                                }
                            });
                        }

                        modal.find('.save-modal').click(function () {
                            let self = $(this);
                            let form = self.closest('.modal-content').find('form');
                            let action = form.attr('action');
                            let method = form.attr('method');
                            let data = {};

                            $.each(form.serializeArray(), function (inc, item) {
                                $.extend( data, {[item.name]: item.value} );
                            });

                            axios({
                                method: method,
                                url: action,
                                data: data
                            }).then(function (response) {

                                table.draw(false);
                                self.closest('.modal').modal('hide');
                                toastr.success('Запросы добавлены');
                            }).catch(function (error) {
                                console.log(error);
                            });
                        });
                    });
                }
            });

        </script>
    @endslot


@endcomponent
