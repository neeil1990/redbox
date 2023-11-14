@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- BS Stepper -->
        <link rel="stylesheet" href="{{ asset('plugins/bs-stepper/css/bs-stepper.min.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

        <style>
            .pg-loading-screen .pg-loading-html {
                margin-top: 30px;
            }
            :root {
                --sk-size: 60px!important;
                --sk-color: #E53631!important;
            }
            .input-group-text {
                cursor: pointer;
            }
            table td label {
                display: none;
            }
            table td .DTE_Field {
                margin-bottom: 0;
            }

        </style>
    @endslot

    <div class="bs-stepper">
        <div class="bs-stepper-header" role="tablist">
            <!-- your steps here -->
            @include('monitoring.partials.stepper._titles', ['item' => '<i class="fas fa-user"></i>', 'target' => 'project', 'name' => 'Создать проект'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => 2, 'target' => 'keywords', 'name' => 'Добавление запросов'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => 3, 'target' => 'competitors', 'name' => 'Настройка конкурентов'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => 4, 'target' => 'regions', 'name' => 'Поисковые системы и регионы'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => 5, 'target' => 'scan', 'name' => 'Настройка снятия'])

            <div class="line"></div>

            @include('monitoring.partials.stepper._titles', ['item' => '<i class="fas fa-save"></i>', 'target' => 'save', 'name' => 'Сохранить!'])

        </div>
        <div class="bs-stepper-content">
            <form class="needs-validation" method="post" action="{{ route('monitoring.store') }}" novalidate>
                @csrf
                <!-- your steps content here -->
                @include('monitoring.partials.stepper._content', ['target' => 'project', 'buttons' => ['next', 'back']])

                @include('monitoring.partials.stepper._content', ['target' => 'keywords', 'buttons' => ['previous', 'next']])

                @include('monitoring.partials.stepper._content', ['target' => 'competitors', 'buttons' => ['previous', 'next']])

                @include('monitoring.partials.stepper._content', ['target' => 'regions', 'buttons' => ['previous', 'next']])

                @include('monitoring.partials.stepper._content', ['target' => 'scan', 'buttons' => ['previous', 'next']])

                @include('monitoring.partials.stepper._content', ['target' => 'save', 'buttons' => ['previous', 'action']])
            </form>
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- BS-Stepper -->
        <script src="{{ asset('plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
        <!-- Papa parse -->
        <script src="{{ asset('plugins/papaparse/papaparse.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-editor/js/datatables_editor.min.js') }}"></script>
        <!-- InputMask -->
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.bundle.js') }}"></script>
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>

        <script>
            const DATA_TABLE_ID = "#myTable";
            const REGIONS_CLASS = ".Select2";
            let dataTable;
            let dataTableEditor;

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            /*window.onbeforeunload = function ()
            {
                return "";
            };

            $('button[type="submit"]').click(function () {
                window.onbeforeunload = null;
            });*/

            let Modes = {
                template: null,
                data: {},

                render: function(){
                    if(!Object.keys(this.data).length)
                        return;

                    let card = $('<div />');
                    $.each(this.data, (i, data) => {
                        if(data.length)
                            card.append(this.card(data, i));
                    });

                    $('.mode-scan').html(card);

                    card.find('.times').inputmask("hh:mm", {
                            placeholder: moment().format('H:mm'),
                        }
                    );

                    card.find('.time-reset').click(function(){
                        $(this).closest('.input-group').find('.times').val("");

                        if($(this).closest('.form-group').find('.select-days').length)
                            $(this).closest('.form-group').find('.select-days')[0].selectedIndex = -1;
                    });

                    this.params = [];
                },
                card: function(data, key){
                    let card = $('<div />', {
                        class: 'card'
                    });

                    let cardHeader = $('<div />', {
                        class: 'card-header'
                    });

                    let cardBody = $('<div />', {
                        class: 'card-body'
                    });

                    cardHeader.append(this.setHeader(key));
                    cardBody.append(this.setContent(data));

                    return card.append(cardHeader, cardBody);
                },
                setHeader: function(title) {

                    return $('<h3 />', {
                        class: "card-title"
                    }).css('text-transform', 'capitalize').text(title);
                },
                setContent: function(data) {
                    let template = $('<div />');

                    $.each(data, (i, item) => {

                        let label = `${item.name} [${item.lr}]`;
                        let inputData = {
                            id: item.id,
                            val: {
                                time: item.time,
                                weekdays: {
                                  time: (item.weekdays) ? item.time : null,
                                  days: item.weekdays,
                                },
                                monthday: item.monthday,
                            },
                            label: label,
                            lr: item.lr,
                        };

                        if(item.weekdays || item.monthday)
                            inputData.val.time = null;

                        template.append(this.timeTemplate(inputData).addClass('d-none'));
                        template.append(this.weeksTemplate(inputData).addClass('d-none'));
                        template.append(this.rangeTemplate(inputData).addClass('d-none'));
                    });

                    return this.template = template;
                },
                timeTemplate: function(data){

                    let form = this.formGroupTemplate('times', data.lr);
                    let label = $('<label />').text(data.label);
                    let input = this.inputTime(data, data.val.time);

                    return form.append(label, input);
                },
                weeksTemplate: function(data){

                    let form = this.formGroupTemplate('weeks', data.lr);
                    let label = $('<label />').text(data.label);
                    let input = this.inputTime(data, data.val.weekdays.time);

                    let options = [
                        {text: "Понедельник", val: "1", selected: false},
                        {text: "Вторник", val: "2", selected: false},
                        {text: "Среда", val: "3", selected: false},
                        {text: "Четверг", val: "4", selected: false},
                        {text: "Пятница", val: "5", selected: false},
                        {text: "Суббота", val: "6", selected: false},
                        {text: "Воскресенье", val: "0", selected: false}
                    ];

                    let selectContent = $('<select />', {
                        name: 'weekdays',
                        class: 'form-control select-days mb-2',
                        multiple: 'true',
                        size: 7,
                    }).attr('data-id', data.id);

                    $.each(options, (i, obj) => {
                        if(data.val.weekdays.days)
                            if(data.val.weekdays.days.indexOf(obj.val) !== -1)
                                obj.selected = true;

                        let optionItems = $('<option />', {
                            selected: obj.selected,
                            value: obj.val,
                        }).text(obj.text);

                        selectContent.append(optionItems);
                    });

                    return form.append(label, selectContent, input);
                },
                rangeTemplate: function(data) {

                    let form = this.formGroupTemplate('ranges', data.lr);
                    let label = $('<label />').text(data.label);
                    let input = $('<input />', {
                        class: 'form-control',
                        type: 'number',
                        min: '1',
                        max: '31',
                        name: 'monthday',
                        value: data.val.monthday,
                        placeholder: 'Выберите от 1 до 31. (Пример: 1 - это съём позиций каждый день, 5 - каждые пять дней)'
                    }).attr('data-id', data.id);

                    return form.append(label, input);
                },
                formGroupTemplate: function(mode, lr){

                    return $('<div />', {
                        class: 'form-group',
                        "data-mode": mode,
                        "data-lr": lr,
                    });
                },
                inputTime: function(data, val) {
                    let group = $('<div />', {
                        class: 'input-group'
                    });

                    let input = $('<input />', {
                        class: 'form-control times',
                        type: 'text',
                        name: 'time',
                        value: val,
                        placeholder: 'Время снятия (24 Hr)',
                    }).attr('data-id', data.id);

                    let icon = $('<div />', {
                        class: 'input-group-append'
                    }).append($('<span />', {class: 'input-group-text time-reset'}).append($('<i />', {class: 'far fa-times-circle'})));

                    return group.append(input, icon);
                },
                setData: function(data) {
                    if(Object.keys(data).length > 0)
                        this.data = data;

                    return this;
                },
            };

            let Parts = {
                part: null,
                projectId: false,

                project: function (event) {
                    let pattern = /^[a-z-а-я-0-9-\.]+\.[a-z-а-я]{2,4}$/;

                    let name = this.part.find('input[name="name"]');
                    let url = this.part.find('input[name="url"]');

                    let nameValue = name.val();
                    let urlValue = url.val();

                    if(!nameValue){
                        event.preventDefault();
                        toastr.error('Заполните название проекта');

                        return false;
                    }

                    if(!urlValue){
                        event.preventDefault();
                        toastr.error('Заполните URL проекта');

                        return false;
                    }

                    if(!pattern.test(urlValue)){
                        event.preventDefault();
                        toastr.error('Неправильный формат URL');

                        return false;
                    }

                    let request = '/monitoring/creator/create';
                    let data = {
                        name: nameValue,
                        url: urlValue,
                    };

                    if(this.projectId){
                        // update project
                        request = '/monitoring/creator/update';
                        data.id = this.projectId;
                    }

                    axios.post(request, data).then(function (response) {
                        window.hash.setHash({
                            id: [response.data],
                        });

                        initDataTable(response.data);

                        toastr.success('Сохранено!');
                    });
                },
                keywords: function (event) {},
                competitors: function (event) {
                    let status = true;
                    let id = this.projectId;
                    let textarea = this.part.find('#textarea-competitors');
                    let list = _.compact(textarea.val().split(/[\r\n]+/));

                    $.each(list, function (index, value) {
                        let domain = value.replace(/\s/g, '');
                        let pattern = /^[a-z-а-я-0-9-\.]+\.[a-z-а-я]{2,4}$/;

                        if(!pattern.test(domain)){
                            event.preventDefault();
                            status = false;
                            toastr.error('Не верный формат: ' + domain);
                        }
                    });

                    if(status && textarea.val().trim().length > 0){
                        axios.post('/monitoring/creator/competitors', {
                            id: id,
                            domains: textarea.val(),
                        });
                    }
                },
                regions: function(event){

                    if(!$(REGIONS_CLASS).find('option').length){
                        event.preventDefault();
                        toastr.error('Выберите регины поиска.');
                    }

                    axios.post('/monitoring/creator/regions', {
                        action: 'get',
                        id: this.projectId,
                    }).then(function (response) {

                        let data = {
                            google: [],
                            yandex: [],
                        };

                        $.each(response.data, function(i, item){
                            data[item.engine].push(item);
                        });

                        Modes.setData(data).render();
                        $('#mode-scan').trigger('change');
                    });
                },
                scan: function(event){
                    let inputs = this.part.find('input, select');
                    let data = [];

                    $.each(inputs, function (i, input) {
                        let el = $(input);
                        if(el.val().length > 0 && el.data('id'))
                            data.push({id: el.data('id'), name: el.attr('name'), val: el.val()});
                    });

                    axios.post('/monitoring/creator/regions', {
                        action: 'update',
                        id: this.projectId,
                        data: data,
                    });
                },
            };

            document.addEventListener('DOMContentLoaded', function () {
                let stepper = document.querySelector('.bs-stepper');
                window.stepper = new Stepper(stepper);

                if(window.hash.getParam('id')){
                    axios.post('/monitoring/creator/edit', { id: window.hash.getParam('id')[0] }).then(function (response) {

                        if(!response.data)
                            window.location.hash = "";

                        let project = $(window.stepper._stepsContents[0]);

                        project.find('input[name="name"]').val(response.data.name);
                        project.find('input[name="url"]').val(response.data.url);
                    });
                }

                stepper.addEventListener('show.bs-stepper', function (event) {

                    let nextStep = event.detail.indexStep;
                    let currentStep = nextStep;

                    if (currentStep > 0) {
                        currentStep--
                    }

                    let panels = $('.bs-stepper-content .content');

                    let currentPanel = panels.eq(currentStep);
                    let nextPanel = panels.eq(nextStep);

                    nextPanel.projectId = currentPanel.projectId = (window.hash.getParam('id')) ? window.hash.getParam('id')[0] : false;

                    currentPanelTrigger(currentPanel, event);
                    nextPanelTrigger(nextPanel, event);
                });
            });

            function nextPanelTrigger(panel, event)
            {
                if(panel.attr('id') === 'competitors-part'){
                    let textarea = panel.find('#textarea-competitors');

                    axios.get('/monitoring/creator/competitors', {
                        params: {id: panel.projectId}
                    }).then(function (response) {
                        textarea.val(response.data);
                    });
                }

                if(panel.attr('id') === 'regions-part'){
                    let selects = panel.find( REGIONS_CLASS );

                    selects.val(null).trigger('change');
                    selects.find('option').remove();

                    axios.post('/monitoring/creator/regions', {
                        action: 'get',
                        id: panel.projectId,
                    }).then(function (response) {

                        $.each(response.data, function (i, data) {
                            let engine = data.engine;

                            selects.each(function (j, el) {
                                let elem = $(el);
                                if(elem.data('search') === engine){
                                    let option = new Option(data.name + ' [' + data.lr + ']', data.lr, true, true);
                                    elem.append(option).trigger('change');
                                }
                            });
                        });
                    });
                }
            }

            function currentPanelTrigger(panel, event)
            {
                Parts.projectId = panel.projectId;
                Parts.part = panel;
                switch (panel.attr('id')) {
                    case 'project-part':
                        Parts.project(event);
                        break;
                    case 'keywords-part':
                        Parts.keywords(event);
                        break;
                    case 'competitors-part':
                        Parts.competitors(event);
                        break;
                    case 'regions-part':
                        Parts.regions(event);
                        break;
                    case 'scan-part':
                        Parts.scan(event);
                        break;
                    default:
                        console.log('next...');
                }
            }

            function initDataTable(projectId)
            {
                if($.fn.dataTable.isDataTable( DATA_TABLE_ID ))
                    return;

                dataTableEditor = new $.fn.dataTable.Editor( {
                    ajax: {
                        url: '/monitoring/creator/queries',
                        data: {
                            id: projectId
                        },
                    },
                    table: DATA_TABLE_ID,
                    fields: [
                        {
                            name: "query",
                        },
                        {
                            name: "page",
                        },
                        {
                            name: "group",
                        },
                        {
                            name: "target",
                            type:  "select",
                            options: [
                                { label: "1", value: "1" },
                                { label: "3", value: "3" },
                                { label: "5", value: "5" },
                                { label: "10", value: "10" },
                                { label: "20", value: "20" },
                                { label: "50", value: "50" },
                                { label: "100", value: "100" },
                            ]
                        },
                    ],
                });

                $( DATA_TABLE_ID ).on( 'click', 'tbody td:not(:last-child) i', function (e) {
                    dataTableEditor.inline( $(this).parent(), {
                        onBlur: 'submit',
                        submit: 'allIfChanged'
                    } );
                } );

                // Delete a record
                $( DATA_TABLE_ID ).on('click', 'td.editor-delete', function (e) {
                    e.preventDefault();

                    dataTableEditor.remove( $(this).closest('tr'), {
                        title: 'Удалить запись',
                        message: 'Вы уверены, что хотите удалить запись?',
                        buttons: 'Удалить'
                    } );
                });

                let editIcon = function ( data, type, row ) {
                    if ( type === 'display' ) {
                        return data + ' <i class="fa fa-pencil" style="opacity: 0.5;font-size: 12px;cursor: pointer;"/>';
                    }
                    return data;
                };

                dataTable = $(DATA_TABLE_ID).DataTable({
                    rowId: 'id',
                    autoWidth: false,
                    ordering: false,
                    searching: false,
                    lengthMenu: [10, 30, 50, 100, 500],
                    dom: '<"card-header"<"card-title"><"float-right"f><"float-right"l>><"card-body p-0"rt><"card-footer clearfix"p><"clear">',
                    pagingType: "simple_numbers",
                    language: {
                        lengthMenu: "_MENU_",
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        paginate: {
                            "first":      "«",
                            "last":       "»",
                            "next":       "»",
                            "previous":   "«"
                        },
                    },
                    serverSide: true,
                    ajax: {
                        url: '/monitoring/creator/queries',
                        type: 'GET',
                        data: {
                            id: projectId ?? 0
                        },
                    },
                    columns:[
                        {
                            data: "query",
                            title: "Запрос",
                            render: editIcon,
                        },
                        {
                            data: "page",
                            title: "Релевантная страница",
                            render: editIcon,
                        },
                        {
                            data: "group",
                            title: "Группа",
                        },
                        {
                            data: "target",
                            title: "Цель",
                            render: editIcon,
                        },
                        {
                            title: "",
                            width: "40px",
                            className: "dt-center editor-delete",
                            render: function(){
                                let icon = '<i class="fas fa-trash" />';
                                return '<a href="javascript:void(0)" class="btn btn-sm btn-default" title="Удалить">'+ icon +'</a>';
                            }
                        }
                    ],
                    initComplete: function(){
                        let self = this;

                        let title = $("div.card-title");
                        title.text('Ваш список запросов');

                        title.after($('<div />', {
                            class: "card-tools",
                        }).html($('<a />', {
                            class: "btn btn-tool btn-sm",
                            href: "#",
                            title: "Удалить все"
                        }).html($('<i />', {
                            class: "fas fa-trash",
                        })).click(function () {

                            let items = self.find('tbody tr');
                            dataTableEditor.remove( items, {
                                title: 'Удалить записи',
                                message: `Вы уверены, что хотите удалить записи? (${items.length})`,
                                buttons: 'Удалить'
                            } );

                            return false;
                        })));

                        self.on( 'processing.dt', function ( e, settings, processing ) {
                            if(processing) {
                                if(window.pleaseWait === undefined || window.pleaseWait.finishing)
                                    window.loading();
                            } else{
                                if(window.pleaseWait)
                                    window.pleaseWait.finish();
                            }
                        });

                        $("#myTable_length label, #myTable_filter label").css('margin-bottom', 0);
                    },
                    drawCallback: function(settings){

                        $('.dataTables_paginate  > .pagination').addClass('pagination-sm');
                    }
                });
            }

            $('#mode-scan').change(function(){
                let self = $(this);
                let option = self.val();
                let modes = $('.mode-scan').find('.form-group');

                modes.addClass('d-none');
                modes.find('code').remove();
                modes.find('input, select').removeAttr('disabled');

                let selected = modes.filter(function () {
                    return $(this).data('mode') === option
                }).removeClass('d-none');

                let hidden = modes.filter(function () {
                    return $(this).hasClass('d-none');
                });

                $.each(selected, function(i, elem){
                    let el = $(elem);
                    let lr = el.data('lr');

                    let hiddenRegion = hidden.filter(function () {
                        return $(this).data('lr') === lr
                    });

                    $.each(hiddenRegion, function (inc, region) {

                        let values = $(region).find('input, select').val();
                        if(values.length > 0){

                            el.find('label').append($('<code />').text(' Режим уже задан: ' + self.find(`option[value="${$(region).data('mode')}"]`).text()));
                            el.find('input, select').attr('disabled', 'disabled');
                        }
                    });
                });
            });

            $( REGIONS_CLASS ).select2({
                theme: 'bootstrap4',
                placeholder: 'Select a regions',
                minimumInputLength: 2,
                language: {
                    inputTooShort: function () {
                        return "Пожалуйста, введите название региона.";
                    }
                },
                ajax: {
                    delay: 500,
                    url: '/api/location',
                    dataType: 'json',
                    data: function (params) {

                        let query = {
                            name: params.term,
                            searchEngine: this.data('search')
                        };

                        return query;
                    },
                    processResults: function(data)
                    {
                        return {
                            results: $.map(data, function(obj) {
                                return {
                                    id: obj.lr,
                                    source: obj.source,
                                    name: obj.name,
                                    text: obj.name + ' [' + obj.lr + ']',
                                };
                            })
                        };
                    },
                }
            });

            $( REGIONS_CLASS ).on('select2:select', function (e) {
                let id = window.hash.getParam('id')[0];
                let data = e.params.data;

                axios.post('/monitoring/creator/regions', {
                    action: 'create',
                    id: id,
                    engine: data.source,
                    lr: data.id,
                });
            });

            $( REGIONS_CLASS ).on('select2:unselect', function (e) {
                let id = window.hash.getParam('id')[0];
                let data = e.params.data;

                axios.post('/monitoring/creator/regions', {
                    action: 'remove',
                    id: id,
                    engine: data.source ?? $(e.params.data.element).parent().data('search'),
                    lr: data.id,
                });

                $(data.element).remove();
            });

            $('#add-keywords').click(function () {

                let csv = $('#csv-keywords');
                let textarea = $('#textarea-keywords');
                let duplicates = $('#remove-duplicates');
                let groupInput = $('#keyword-groups');
                let target = $('select[name="target"]');
                let delimiter = $('#csv-delimiter').val();

                if(csv[0].files.length){

                    if(csv[0].files[0].type !== 'text/csv'){
                        toastr.error('Загрузите файл формата .csv');
                        return false;
                    }

                    csv.parse({
                        config: {
                            delimiter: delimiter,
                            skipEmptyLines: 'greedy',
                            complete: function (result) {
                                if(duplicates.prop('checked'))
                                    result.data = $.unique(result.data);

                                let data = [];
                                $.each(result.data, function (i, value) {
                                    let group = groupInput.find('option:selected').text();

                                    if(value[1] && value[1].trim())
                                        group = value[1];

                                    group = group.replace(/[!\[\]]/g, '');

                                    data.push({query: value[0], page: value[2], group: group, target: target.val()});
                                });

                                if(data.length > 0 && $.fn.dataTable.isDataTable( DATA_TABLE_ID )){
                                    csv.val('');
                                    textarea.val('');
                                    createQueries(data);
                                }
                            },
                            download: 0
                        }
                    });
                    return false;
                }

                if(textarea.val().trim().length > 0){
                    let relevant = $('#relevant-url');
                    let list = _.compact(textarea.val().split(/[\r\n]+/));

                    if(duplicates.prop('checked'))
                        list = $.unique(list);

                    let data = [];
                    $.each(list, function (i, value) {
                        data.push({query: value, page: relevant.val(), group: groupInput.find('option:selected').text(), target: target.val()});
                    });

                    if(data.length > 0 && $.fn.dataTable.isDataTable( DATA_TABLE_ID )){
                        textarea.val('');
                        createQueries(data);
                        return false;
                    }
                }

                toastr.error('Заполните или загрузите список запросов.');

                return false;
            });

            function createQueries(data)
            {
                let dataSet = {};
                $.each(data, function (i, v) {
                    dataSet.query = $.extend( dataSet.query, {[i] : v.query} );
                    dataSet.page = $.extend( dataSet.page, {[i] : v.page} );
                    dataSet.group = $.extend( dataSet.group, {[i] : v.group} );
                    dataSet.target = $.extend( dataSet.target, {[i] : v.target} );
                });

                dataTableEditor.create( data.length, false );
                dataTableEditor.multiSet( dataSet );
                dataTableEditor.submit();
                window.loading();
            }

            let keywordSelect2 = $('#keyword-groups');
            keywordSelect2.select2({
                theme: 'bootstrap4'
            });

            let newOption = new Option("Основная", "Основная", false, false);
            keywordSelect2.append(newOption).trigger('change');

            $('#create-group').click(function(){
                let el = $(this);
                let input = el.closest('.input-group').find('input');

                if(input.val()){
                    let newOption = new Option(input.val(), input.val(), false, true);
                    keywordSelect2.append(newOption).trigger('change');

                    toastr.success('Добавленно');

                    input.val(null);
                }
            });

        </script>
    @endslot


@endcomponent
