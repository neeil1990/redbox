@component('component.card', ['title' => __('Выделение уникальных слов в тексте')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
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

    <label>Удалить строки, где кол-во вхождений:</label>

    <div class="row">
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

    <div class="row">
        <div class="col-md-12">
            <table class="table table-sm table-bordered table-hover" id="list-words" style="width:100%">
                <thead>
                    <tr>
                        <th>Слово</th>
                        <th>Словоформы</th>
                        <th>Кол-во вхождений</th>
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

        <script>
            $('#processing').click(function () {
                axios.post('{{ route('unique.dataTableView') }}', {
                    content : $('#content').val()
                }).then((response) => {
                    let table = $('#list-words').DataTable({
                        destroy: true,
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
                            }
                        ],
                    });

                    let $tbody = $('#list-words tbody');
                    let $rangeRemove = $('#range-remove');

                    $tbody.off('click');

                    $tbody.on('click', 'button.remove', function () {
                        table.row($(this).parents('tr')).remove().draw(false);
                    });

                    $rangeRemove.off('click');

                    $rangeRemove.click(function () {
                        let $from = parseInt($('#range-from').val());
                        let $to = parseInt($('#range-to').val());

                        if ($from > 0) {
                            table.rows((idx, data) => data[2] >= $from).remove().draw(false);
                        }

                        if ($to > 0) {
                            table.rows((idx, data) => data[2] <= $to).remove().draw(false);
                        }
                    });
                });
            });
        </script>
    @endslot


@endcomponent
