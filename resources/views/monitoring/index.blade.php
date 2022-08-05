@component('component.card', ['title' => __('Monitoring position')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

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

        </style>
    @endslot

    <div class="row mb-1">
        @include('monitoring.partials._buttons')
    </div>

    <div class="row">
        @include('monitoring.partials._table')
    </div>

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

        <script>
            toastr.options = {
                "preventDuplicates": true,
                "timeOut": "1500"
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
                lengthMenu: [10, 20, 30, 50, 100],
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
                    processing: '<img src="/img/1485.gif" style="width: 50px; height: 50px;">',
                },
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/monitoring/projects/get',
                },
                order: [
                    [1, 'asc'],
                    [2, 'asc'],
                ],
                columns: [
                    {
                        orderable: false,
                        data: null,
                        defaultContent: '<div class="form-check"><input class="form-check-input" type="checkbox"></div>',
                    },
                    {
                        orderable: false,
                        data: null,
                        defaultContent: '<a href="#" class="dt-control text-muted"><i class="fas fa-plus-circle"></i></a>',
                    },
                    {
                        title: 'Название проекта',
                        data: 'name'
                    },
                    {
                        title: 'Домен',
                        data: 'url'
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
                        title: 'Ср.Позиция',
                        data: 'middle_position',
                    },
                    {
                        width: '120px',
                        title: 'Отчеты в pdf',
                        data: null,
                        class: 'project-actions text-right',
                        defaultContent: '<a class="btn btn-info btn-sm" href="#"><i class="fas fa-save"></i> View</a> <a class="btn btn-danger btn-sm" href="#"><i class="fas fa-trash"></i> View</a>',
                    },
                    {
                        width: '120px',
                        data: null,
                        class: 'project-actions text-right',
                        defaultContent: '<a class="btn btn-success btn-sm" href="#"><i class="fas fa-plus"></i></a> <a class="btn btn-info btn-sm" href="#"><i class="fas fa-save"></i></a> <a class="btn btn-danger btn-sm" href="#"><i class="fas fa-trash"></i></a>',
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

                            axios.get(`/monitoring/${data.id}/child-rows/get`).then(function(response){
                                row.child(response.data).show();
                            });

                            tr.addClass('shown');

                            icon.removeClass('fa-plus-circle');
                            icon.addClass('fa-minus-circle')
                        }

                        return false;
                    });

                    this.closest('.card').find('.card-header .card-title').html("Управление проектами.");
                    this.closest('.card').find('.card-header label').css('margin-bottom', 0);

                    let updateCacheIcon = $('<i />', {class: "fas fa-sync-alt"});
                    let updateCacheButton = $('<a />', {
                        class: "text-muted",
                        href: "/monitoring/project/remove/cache"
                    }).html(updateCacheIcon);

                    let updateCacheText = $('<div />', {class: "card-title ml-2"})
                        .html("{{ __('Actual data for') }}: " + json.cache.date + " ");
                    updateCacheText.append(updateCacheButton);
                    let updateCacheContainer = $('<div />', {class: "float-left"}).html(updateCacheText);
                    this.closest('.card').find('.card-header .card-title').after(updateCacheContainer);
                },
                drawCallback: function(){
                    this.find('tbody tr').addClass('main');
                    $('.pagination').addClass('pagination-sm');
                },
            });

            $('.checkbox-delete').click(function(){
                let rows = table.rows('.' + HIGHLIGHT_TR_CLASS);
                let data = rows.data();

                $.each(data, function(index, row){
                    axios.delete(`monitoring/${row.id}`);
                    rows.remove().draw(false);
                });
            });

        </script>
    @endslot


@endcomponent
