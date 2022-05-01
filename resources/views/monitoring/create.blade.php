@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- BS Stepper -->
        <link rel="stylesheet" href="{{ asset('plugins/bs-stepper/css/bs-stepper.min.css') }}">
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

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
                    if(this.part.find('input[name^="keywords"]').length < 4){
                        event.preventDefault();

                        toastr.error('Добавте не менее 4 запросов чтобы продолжить.');
                    }
                },
                competitors: function (event) {
                    let textarea = this.part.find('#textarea-competitors');
                    let list = _.compact(textarea.val().split(/[\r\n]+/));

                    if(!list.length){
                        event.preventDefault();

                        toastr.error('Заполните список.');
                    }

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

                    selects.each(function(i, v){
                        let option = $(v);

                        if(option.val().length)
                            isEmpty = false;
                    });

                    if(isEmpty){
                        event.preventDefault();
                        toastr.error('Выберите регины поиска.');
                    }
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
                            //Parts.project(event);
                            break;
                        case 'keywords-part':
                            //Parts.keywords(event);
                            break;
                        case 'competitors-part':
                            //Parts.competitors(event);
                            break;
                        case 'regions-part':
                            Parts.regions(event);
                            break;
                        default:
                            console.log('next...');
                    }
                });
            });

            $('.Select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select a regions',
                ajax: {
                    delay: 500,
                    url: '/api/location',
                    dataType: 'json',
                    data: function (params) {

                        var query = {
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

            let Table = {
                id: '#keywords',
                data: [],

                page: 1,
                count: 5,

                pagination: function(){
                    let self = this;

                    let ul = $(this.id).find('.card-footer ul');

                    let pages = Math.ceil(this.data.length / this.count);

                    ul.find('li').remove();
                    for (let i = 1; i <= pages; i++) {

                        let li = $('<li />', {
                            class: 'page-item'
                        }).html($('<a />', {
                            href: '',
                            class: 'page-link'
                        }).text(i).click(function () {
                            self.page = i;
                            self.display();

                            return false;
                        }));

                        ul.append(li);
                    }
                },
                render: function () {
                    let self = this;

                    self.data = _.filter(self.data, function (item) {
                        return item[0];
                    });

                    $(self.id).find('table tbody tr').remove();

                    $.each(self.data, function (index, value) {

                        let tr = $('<tr />').attr('data-eq', index);

                        tr.append($('<td />').text(index + 1));

                        tr.append($('<td />').html(self.templateText(value[0], {placeholder: 'Запрос', name: 'keywords[query][]'})));
                        tr.append($('<td />').html(self.templateText(value[1], {placeholder: 'Страница', name: 'keywords[page][]'})));

                        tr.append($('<td />').html(self.templateBtn()));

                        tr.hide();

                        $(self.id).find('table tbody').append(tr);
                    });

                    this.pagination();

                    toastr.success('Добавлено');
                },
                display: function() {

                    let start = (this.page - 1) * this.count;
                    let end = start + this.count;

                    let tr = $(this.id).find('table tbody tr');

                    tr.hide();

                    for (let i = start; i < end; i++) {
                        tr.eq(i).show();
                    }
                },
                templateText: function (val, obj) {
                    if(!val)
                        val =  null;

                    if(!obj)
                        obj = {
                            placeholder: '',
                            name: ''
                        };

                    return $('<input />', obj).addClass('form-control form-control-border p-0').css({
                        background: 'none',
                        height: 'auto'
                    }).val(val);
                },
                templateBtn: function () {
                    let self = this;

                    let a = $('<a />', {
                        href: '#'
                    });
                    let span = $('<span />', {
                        class: 'badge bg-danger'
                    });
                    let i = $('<i />', {
                        class: 'fas fa-trash'
                    });

                    return a.html(span.html(i)).click(function () {
                        let item = $(this).closest('tr');

                        item.remove();

                        self.data.splice(item.data('eq'), 1);

                        toastr.success('Удалено');

                        self.pagination();

                        return false;
                    });
                }

            };

            $('#add-keywords').click(function () {

                let csv = $('#csv-keywords');
                let textarea = $('#textarea-keywords');

                if(csv[0].files.length){

                    if(csv[0].files[0].type !== 'text/csv'){
                        toastr.error('Загрузите файл формата .csv');
                        return false;
                    }

                    csv.parse({
                        config: {
                            skipEmptyLines: 'greedy',
                            complete: function (result) {
                                Table.data = result.data;
                                Table.render();
                                Table.display();

                                textarea.val('');
                            },
                            download: 0
                        }
                    });

                    return false;
                }

                if(textarea.val()){
                    let list = _.compact(textarea.val().split(/[\r\n]+/));
                    let data = [];

                    $.each(list, function (index, value) {
                        data.push([value, '']);
                    });

                    if(data.length > 0){
                        Table.data = data;
                        Table.render();
                        Table.display();

                        return false;
                    }
                }

                toastr.error('Заполните или загрузите список запросов.');

                return false;
            });
        </script>
    @endslot


@endcomponent
