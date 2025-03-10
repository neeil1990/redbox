@component('component.card', ['title' => __('Выделение уникальных слов в тексте')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

        <style>
            th {
                cursor: pointer;
            }

            .hint {
                font-size: 0.8em;
                color: gray;
                margin-left: 5px;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            th:hover .hint {
                opacity: 1;
            }
        </style>
    @endslot

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Список ключевых слов:</label>
                <textarea class="form-control" rows="5" id="content"></textarea>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-success" id="processing">Обработать</button>
            </div>
        </div>
    </div>

    <label class="fade">Удалить строки, где кол-во вхождений:</label>

    <div class="row fade">
        <div class="col-sm-2">
            <div class="form-group">
                <input type="number" min="1" class="form-control" placeholder="больше или равно" id="range-from">
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <input type="number" min="1" class="form-control" placeholder="меньше или равно" id="range-to">
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <button type="button" class="btn btn-success" id="range-remove">Удалить</button>
            </div>
        </div>
    </div>

    <label class="fade">Включить в отчет:</label>

    <div class="row fade">
        <div class="col-sm-12">
            <div class="form-inline mb-2">
                @foreach (['Слово', 'Словоформы', 'Кол-во вхождений', 'Ключевые фразы'] as $idx => $checkbox)
                    <div class="form-group mr-4">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input toggle-vis" type="checkbox" id="vis-{{ $idx }}" data-column="{{ $idx }}" checked="">
                            <label for="vis-{{ $idx }}" class="custom-control-label">{{ $checkbox }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row fade">
        <div class="col-md-12">
            <table class="table table-sm table-bordered table-hover" id="list-words" style="width:100%">
                <thead>
                    <tr>
                        <th>Слово <span class="hint">(Сортировка)</span></th>
                        <th>Словоформы <span class="hint">(Сортировка)</span></th>
                        <th>Кол-во вхождений <span class="hint">(Сортировка)</span></th>
                        <th>Ключевые фразы</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>

        <script>
            $('#processing').click(function () {
                $('.fade').addClass('show');
                axios.post('{{ route('unique.dataTableView') }}', {
                    content : $('#content').val()
                }).then((response) => {
                    let table = $('#list-words').DataTable({
                        destroy: true,
                        dom: 'Bt',
                        autoWidth: false,
                        searching: false,
                        paging: false,
                        info: false,
                        processing: true,
                        data: response.data,
                        order: [[2, 'desc']],
                        columnDefs: [
                            {
                                data: null,
                                defaultContent: '<button class="btn btn-outline-danger btn-xs remove"><i class="fas fa-trash"></i></button>',
                                targets: -1,
                                className: 'text-center'
                            },
                            {
                                targets: 3,
                                width: '35%',
                                render: function(data) {
                                    return `<textarea class="form-control text-nowrap" rows="3">${data}</textarea>`;
                                }
                            }
                        ],
                        buttons: [
                            { extend: 'copy', text: '<i class="far fa-copy"></i>', className: 'btn btn-success mb-2', exportOptions: { columns: ':visible' } },
                            { extend: 'csv', text: '<i class="far fa-save"></i>', className: 'btn btn-success mb-2', exportOptions: { columns: ':visible' } }
                        ],
                    });

                    table.buttons().container().on('click', '.buttons-copy', function() {
                        toastr.success('Данные успешно скопированы!')
                    });

                    $('#list-words tbody, #range-remove, .toggle-vis').off('click');

                    $('#list-words tbody').on('click', 'button.remove', function () {
                        table.row($(this).parents('tr')).remove().draw(false);
                    });

                    $('#range-remove').click(function () {
                        let $from = parseInt($('#range-from').val());
                        let $to = parseInt($('#range-to').val());

                        if ($from > 0) {
                            table.rows((idx, data) => data[2] >= $from).remove().draw(false);
                        }

                        if ($to > 0) {
                            table.rows((idx, data) => data[2] <= $to).remove().draw(false);
                        }
                    });

                    $('.toggle-vis').click(function () {
                        let column = table.column($(this).attr('data-column'));
                        column.visible($(this).prop('checked'));
                    });
                });
            });
        </script>
    @endslot


@endcomponent
