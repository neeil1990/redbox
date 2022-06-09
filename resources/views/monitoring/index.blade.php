@component('component.card', ['title' => __('Monitoring')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
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
                    $('.table tbody tr').removeClass(HIGHLIGHT_TR_CLASS);
                    $('.checkbox-toggle .far.fa-check-square').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check all checkboxes
                    $('.table tbody tr').addClass(HIGHLIGHT_TR_CLASS);
                    $('.checkbox-toggle .far.fa-square').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks)
            });

            $('[data-toggle="tooltip"]').tooltip();

            let table = $('#projects').DataTable({
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
                ajax: {
                    url: '/monitoring/projects/get',
                    dataSrc: '',
                },
                order: [[1, 'asc']],
                columns: [
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
                        data: null,
                        defaultContent: '3',
                    },
                ],
                initComplete: function () {
                    let api = this.api();

                    this.find('tbody tr').click(function(){
                        $(this).toggleClass(HIGHLIGHT_TR_CLASS);
                    });

                    this.find('tbody td .dt-control').click(function () {
                        let icon = $(this).find('i');
                        let tr = $(this).closest('tr');
                        let row = api.row(tr);

                        if (row.child.isShown()) {
                            // This row is already open - close it
                            row.child.hide();
                            tr.removeClass('shown');

                            icon.removeClass('fa-minus-circle');
                            icon.addClass('fa-plus-circle')
                        } else {
                            // Open this row
                            let data = row.data();

                            axios.get(`/monitoring/${data.id}/keywords/get`).then(function(response){
                                let keywords = response.data;
                                row.child(tableFormat(keywords)).show();
                            });

                            tr.addClass('shown');

                            icon.removeClass('fa-plus-circle');
                            icon.addClass('fa-minus-circle')
                        }

                        return false;
                    });

                    this.closest('.card').find('.card-header .card-title').html("Управление проектами");
                    this.closest('.card').find('.card-header label').css('margin-bottom', 0);
                },
                drawCallback: function(){
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

            function tableFormat(data) {
                let table = $('<table />');

                $.each(data, function (i, item) {
                    let tr = $('<tr />');

                    tr.append($('<td />').html($('<a />', {
                        href: `/monitoring/keywords/${item.id}`,
                        target: '_blank',
                    }).text(item.query)));

                    if(item.page)
                        tr.append($('<td />').text(item.page));

                    tr.append($('<td />').text(item.target));
                    tr.append($('<td />').text(item.created_at));

                    table.append(tr);
                });

                return table;
            }
        </script>
    @endslot


@endcomponent
