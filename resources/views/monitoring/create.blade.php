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
            .input-group-text {
                cursor: pointer;
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
                @include('monitoring.partials.stepper._content', ['target' => 'project', 'buttons' => ['next']])

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
        <!-- InputMask -->
        <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('plugins/inputmask/jquery.inputmask.bundle.js') }}"></script>

        <script>

            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
            };

            window.onbeforeunload = function ()
            {
                return "";
            };

            $('button[type="submit"]').click(function () {
                window.onbeforeunload = null;
            });

            let Modes = {
                template: null,
                time: 'cron[time]',
                weekdays: 'cron[weekdays]',
                monthday: 'cron[monthday]',
                params: [],

                render: function(){

                    if(this.params.length){
                        let card = $('<div />');
                        $.each(this.params, (i, param) => {
                            card.append(this.card(param));
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
                    }
                    this.params = [];
                },
                card: function(param){
                    let card = $('<div />', {
                        class: 'card'
                    });

                    let cardHeader = $('<div />', {
                        class: 'card-header'
                    });

                    let cardBody = $('<div />', {
                        class: 'card-body'
                    });

                    cardHeader.append(this.setHeader(param.search));
                    cardBody.append(this.setContent(param));

                    return card.append(cardHeader, cardBody);
                },
                setHeader: function(title) {

                    return $('<h3 />', {
                        class: "card-title"
                    }).css('text-transform', 'capitalize').text(title);
                },
                setContent: function(param) {
                    let template = $('<div />');

                    $.each(param.region, (i, el) => {

                        template.append(this.timeTemplate(el, param.lr[i], param.search).addClass('d-none'));
                        template.append(this.weeksTemplate(el, param.lr[i], param.search).addClass('d-none'));
                        template.append(this.rangeTemplate(el, param.lr[i], param.search).addClass('d-none'));
                    });

                    return this.template = template;
                },
                timeTemplate: function(name, val, search){

                    let form = this.formGroupTemplate('times', val);
                    let label = $('<label />').text(name);
                    let input = this.inputTextTemplate(`${this.time}[${search}][${val}]`, '', 'times', 'Время снятия (24 Hr)');

                    return form.append(label, input);
                },
                weeksTemplate: function(name, val, search){

                    let form = this.formGroupTemplate('weeks', val);
                    let label = $('<label />').text(name);
                    let input = this.inputTextTemplate(`${this.time}[${search}][${val}]`, '', 'times', 'Время снятия (24 Hr)');

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
                        name: `${this.weekdays}[${search}][${val}][]`,
                        class: 'form-control select-days mb-2',
                        multiple: 'true',
                        size: 7,
                    });

                    $.each(options, (i, obj) => {
                        let optionItems = $('<option />', {
                            selected: obj.selected,
                            value: obj.val,
                        }).text(obj.text);

                        selectContent.append(optionItems);
                    });

                    return form.append(label, selectContent, input);
                },
                rangeTemplate: function(name, val, search) {

                    let form = this.formGroupTemplate('ranges', val);
                    let label = $('<label />').text(name);
                    let input = $('<input />', {
                        class: 'form-control',
                        type: 'number',
                        min: '1',
                        max: '31',
                        name: `${this.monthday}[${search}][${val}]`,
                        placeholder: 'Выберите от 1 до 31. (Пример: 1 - это съём позиций каждый день, 5 - каждые пять дней)'
                    });

                    return form.append(label, input);
                },
                formGroupTemplate: function(mode, lr){

                    return $('<div />', {
                        class: 'form-group',
                        "data-mode": mode,
                        "data-lr": lr,
                    });
                },
                inputTextTemplate: function(name, val, cls, pls) {
                    let group = $('<div />', {
                        class: 'input-group'
                    });

                    let input = $('<input />', {
                        class: `form-control ${cls}`,
                        type: 'text',
                        name: name,
                        value: val,
                        placeholder: pls
                    });

                    let icon = $('<div />', {
                        class: 'input-group-append'
                    }).append($('<span />', {class: 'input-group-text time-reset'}).append($('<i />', {class: 'far fa-times-circle'})));

                    if(cls === 'times')
                        return group.append(input, icon);

                    return group.append(input);
                },
                setParams: function(params) {
                    if(params.length > 0)
                        this.params = params;

                    return this;
                },
            };

            let Parts = {
                part: null,

                project: function (event) {
                    let pattern = /^[a-z-а-я-0-9-\.]+\.[a-z-а-я]{2,4}$/;
                    let name = this.part.find('input[name="name"]');
                    let url = this.part.find('input[name="url"]');

                    if(!name.val()){
                        event.preventDefault();
                        toastr.error('Заполните название проекта');

                        return false;
                    }

                    if(!url.val()){
                        event.preventDefault();
                        toastr.error('Заполните URL проекта');

                        return false;
                    }

                    if(!pattern.test(url.val())){
                        event.preventDefault();
                        toastr.error('Неправильный формат URL');

                        return false;
                    }
                },
                keywords: function (event) {

                    let inputs = this.part.find('.input-keywords');
                    let selectedGroup = this.part.find('#keyword-groups');
                    let html = "";
                    let data = table.rows().data();

                    $.each(data, function(index, value){

                        if(!value.group.trim().length)
                            value.group = selectedGroup.val();

                        let query = $('<input />', {
                            type: "hidden",
                            name: `keywords[${value.group}][query][]`,
                        }).val(value.query);

                        let page = $('<input />', {
                            type: "hidden",
                            name: `keywords[${value.group}][page][]`,
                        }).val(value.page);

                        let target = $('<input />', {
                            type: "hidden",
                            name: `keywords[${value.group}][target][]`,
                        }).val(value.target);

                        html += query[0].outerHTML + page[0].outerHTML + target[0].outerHTML;
                    });

                    inputs.html(html);

                    if(inputs.find('input').length === 0){
                        event.preventDefault();

                        toastr.error('Добавте запросы чтобы продолжить');
                    }
                },
                competitors: function (event) {
                    let textarea = this.part.find('#textarea-competitors');
                    let list = _.compact(textarea.val().split(/[\r\n]+/));

                    $.each(list, function (index, value) {
                        let domain = value.replace(/\s/g, '');
                        let pattern = /^[a-z-а-я-0-9-\.]+\.[a-z-а-я]{2,4}$/;
                        if(pattern.test(domain)){
                            console.log('check: ' + domain);
                        }else{
                            event.preventDefault();
                            toastr.error('Не верный формат: ' + domain);
                        }
                    });
                },
                regions: function(event){
                    let selects = this.part.find('.Select2');
                    let isEmpty = true;
                    let modes = [];

                    selects.each(function(i, v){
                        let select = $(v);

                        if(select.val().length){

                            isEmpty = false;

                            let search = select.data('search');
                            let lr = select.val();
                            let region = select.find('option').map(function(){
                                return $(this).text();
                            });

                            modes.push({
                                search: search,
                                region: region,
                                lr: lr
                            });
                        }
                    });

                    if(isEmpty){
                        event.preventDefault();
                        toastr.error('Выберите регины поиска.');
                    }else{
                        Modes.setParams(modes).render();
                        $('#mode-scan').trigger('change');
                    }
                },
                scan: function(event){
                    let inputs = this.part.find('input, select');

                    $.each(inputs, function (i, input) {
                        let el = $(input);

                        if(!el.val().length)
                            $(input).attr('disabled', 'disabled');
                    });
                },
            };

            document.addEventListener('DOMContentLoaded', function () {
                let stepper = document.querySelector('.bs-stepper');
                window.stepper = new Stepper(stepper);

                stepper.addEventListener('show.bs-stepper', function (event) {

                    let nextStep = event.detail.indexStep;
                    let currentStep = nextStep;

                    if (currentStep > 0) {
                        currentStep--
                    }

                    let panels = $('.bs-stepper-content .content');
                    let panel = panels.eq(currentStep);

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
                });
            });

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

            $('.Select2').select2({
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
                                    text: obj.name + ' [' + obj.lr + ']'
                                };
                            })
                        };
                    },
                }
            });

            $('.Select2').on('select2:unselect', function (e) {
                let data = e.params.data;
                $(data.element).remove();
            });

            let table = $('#myTable').DataTable({
                rowId: 'id',
                autoWidth: false,
                ordering: false,
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
                columns:[
                    {
                        width: "10px",
                        data: "id",
                        title: "#",
                        visible: false
                    },
                    {
                        data: "query",
                        title: "Запрос",
                        render: function(data){

                            let span_for_search = $('<span />', {
                                class: "d-none"
                            }).text(data);

                            let input = $('<input />', {
                                class: "form-control form-control-border p-0 my-query",
                                placeholder: "Запрос",
                                value: data,
                            }).css({
                                background: "none",
                                height: "auto"
                            });

                            return input[0].outerHTML + span_for_search[0].outerHTML;
                        }
                    },
                    {
                        data: "page",
                        title: "Релевантная страница",
                        render: function(data){

                            let span_for_search = $('<span />', {
                                class: "d-none"
                            }).text(data);

                            let input = $('<input />', {
                                class: "form-control form-control-border p-0 my-page",
                                placeholder: "Релевантная страница",
                                value: data,
                            }).css({
                                background: "none",
                                height: "auto"
                            });

                            return input[0].outerHTML + span_for_search[0].outerHTML;
                        }
                    },
                    {
                        data: "group",
                        title: "Группа",
                        render: function(data){

                            let span_for_search = $('<span />', {
                                class: "d-none"
                            }).text(data);

                            let input = $('<input />', {
                                class: "form-control form-control-border p-0 my-group",
                                placeholder: "Группа",
                                value: data,
                            }).css({
                                background: "none",
                                height: "auto"
                            });

                            return input[0].outerHTML + span_for_search[0].outerHTML;
                        }
                    },
                    {
                        data: "target",
                        title: "Цель",
                    },
                    {
                        title: "",
                        width: "40px",
                        render: function(){

                            let a = $('<a />', {
                                href: '#',
                                class: "icon-delete"
                            });
                            let span = $('<span />', {
                                class: 'badge bg-danger'
                            });
                            let i = $('<i />', {
                                class: 'fas fa-trash'
                            });

                            return a.html(span.html(i))[0].outerHTML;
                        }
                    }
                ],
                data: [],
                initComplete: function(){
                    var api = this.api();

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
                        table.rows().remove().draw(true);
                        return false;
                    })));

                    this.on( 'click', 'a.icon-delete', function () {
                        table.row( $(this).parents('tr') ).remove().draw(true);

                        return false;
                    });

                    this.on( 'keyup', 'input.my-query', function () {
                        let self = $(this);
                        let val = self.val();

                        self.closest('td').find('span').text(val);

                        let data = table.row('#' + self.closest('tr').attr('id')).data();
                        data.query = val;
                    });

                    this.on( 'keyup', 'input.my-page', function () {
                        let self = $(this);
                        let val = self.val();

                        self.closest('td').find('span').text(val);

                        let data = table.row('#' + self.closest('tr').attr('id')).data();
                        data.page = val;
                    });

                    $( 'input[type="search"]', this.closest('.card')).focus(function() {
                        let data = api.data();

                        api.rows().remove();

                        api.rows.add(data).draw();
                    });

                    $("#myTable_length label, #myTable_filter label").css('margin-bottom', 0);
                },
                drawCallback: function(settings){

                    $('.dataTables_paginate  > .pagination').addClass('pagination-sm');
                }
            });

            $('#add-keywords').click(function () {

                let csv = $('#csv-keywords');
                let textarea = $('#textarea-keywords');
                let duplicates = $('#remove-duplicates');
                let groupInput = $('#keyword-groups');
                let target = $('select[name="target"]');

                let indexes = Math.max.apply(Math, table.rows().data().map(function(o) { return o.id ?? 0; }));
                let index = (indexes === -Infinity) ? 0 : indexes;

                if(csv[0].files.length){

                    if(csv[0].files[0].type !== 'text/csv'){
                        toastr.error('Загрузите файл формата .csv');
                        return false;
                    }

                    csv.parse({
                        config: {
                            skipEmptyLines: 'greedy',
                            complete: function (result) {

                                let data = [];

                                $.each(result.data, function (i, value) {
                                    index = index + 1;

                                    let group = groupInput.find('option:selected').text();
                                    if(value[1].trim())
                                        group = value[1];

                                    group = group.replace(/[!\[\]]/g, '');

                                    if(duplicates.prop('checked')){
                                        let existed = $.grep(data, function(v) {
                                            return v.query === value[0];
                                        });

                                        if(existed.length === 0)
                                            data.push({id: index, query: value[0], page: value[2], group: group, target: target.val()});
                                    }else{
                                        data.push({id: index, query: value[0], page: value[2], group: group, target: target.val()});
                                    }
                                });

                                if(data.length > 0){
                                    table.rows.add(data).draw();

                                    csv.val('');
                                    textarea.val('');
                                }
                            },
                            download: 0
                        }
                    });

                    return false;
                }

                if(textarea.val()){
                    let relevant = $('#relevant-url');
                    let list = _.compact(textarea.val().split(/[\r\n]+/));
                    let data = [];

                    $.each(list, function (i, value) {
                        index = index + 1;

                        if(duplicates.prop('checked')){
                            let existed = $.grep(data, function(v) {
                                return v.query === value;
                            });

                            if(existed.length === 0)
                                data.push({id: index, query: value, page: relevant.val(), group: groupInput.find('option:selected').text(), target: target.val()});
                        }else{
                            data.push({id: index, query: value, page: relevant.val(), group: groupInput.find('option:selected').text(), target: target.val()});
                        }
                    });

                    if(data.length > 0){
                        table.rows.add(data).draw();

                        textarea.val('');

                        return false;
                    }
                }

                toastr.error('Заполните или загрузите список запросов.');

                return false;
            });

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
