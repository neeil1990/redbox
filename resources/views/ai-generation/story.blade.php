@php
    $isAllHistory = request()->is('*/all-history');
@endphp

@component('component.card', ['title' => 'История'])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    @endslot

    <div class="card">
        <div class="card-header d-flex p-0">
            @include('ai-generation.blocks.nav')
        </div>
        <div class="card-body">
            <table id="history-table" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th></th>
                        @if($isAllHistory) <th>Пользователь</th> @endif
                        <th>Использовано токенов</th>
                        <th>Источник данных</th>
                        <th>Статус</th>
                        <th>Дата</th>
                        <th>Действия</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

        <script>
            function format(row) {
                let prompt = row.data('prompt') || '—';
                let result = row.data('result') || '—';

                return `
                    <div style="padding: 10px;">
                        <b>Промпт:</b>
                        <div style="white-space: pre-wrap;" class="mb-3">${prompt}</div>
                        <hr>
                        <b>Результат:</b>
                        <div style="white-space: pre-wrap;">${result}</div>
                    </div>
                `;
            }

            $(function () {
                const isAllHistory = {{ $isAllHistory ? 'true' : 'false' }};

                let columns = [
                    { 
                        data: null, 
                        className: 'details-control', 
                        orderable: false, 
                        defaultContent: 'Показать информацию',
                        render: function() { return '<span style="cursor:pointer; color: #007bff;">Показать информацию</span>'; }
                    }
                ];

                if (isAllHistory) {
                    columns.push({ 
                        data: 'user_info', 
                        render: function(data) {
                            return `<small>ID: ${data.id}<br>Name: ${data.name}<br>Email: ${data.email}</small>`;
                        } 
                    });
                }

                columns.push(
                    { data: 'used_tokens' },
                    { 
                        data: 'source', 
                        render: function(data) {
                            return data === 'parse_html' ? 'Парсинг HTML' : 'База данных AI';
                        } 
                    },
                    { 
                        data: 'status', 
                        render: function(data) {
                            if (data === 'completed') return '<span class="badge badge-success">Завершено</span>';
                            if (data === 'failed') return '<span class="badge badge-danger">Не удалось</span>';
                            return '<span class="badge badge-warning">В ожидании</span>';
                        } 
                    },
                    { data: 'date' },
                    { 
                        data: null, 
                        orderable: false, 
                        render: function(data) {
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" data-toggle="dropdown">Действия</button>
                                    <div class="dropdown-menu">
                                        <a href="#" class="dropdown-item copy-result">Скопировать результат</a>
                                        <a href="#" class="dropdown-item apply-history-full">Применить конфиг</a>
                                    </div>
                                </div>`;
                        } 
                    }
                );

                let table = $('#history-table').DataTable({
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: "{{ route('ai.generation.history.json') }}",
                        type: "POST",
                        data: { 
                            _token: "{{ csrf_token() }}",
                            scope: isAllHistory ? 'all' : 'user'
                        }
                    },
                    columns: columns,
                    order: [[isAllHistory ? 5 : 4, "desc"]],
                });

                $('#history-table tbody').on('click', 'td.details-control', function () {
                    let tr = $(this).closest('tr');
                    let row = table.row(tr);
                    let rowData = row.data();

                    if (row.child.isShown()) {
                        row.child.hide();
                        $(this).find('span').text('Показать информацию');
                    } else {
                        row.child(`
                            <div class="p-3 bg-light">
                                <b>Промпт:</b> <div class="mb-2" style="white-space:pre-wrap">${rowData.prompt}</div>
                                <hr>
                                <b>Результат:</b> <div style="white-space:pre-wrap">${rowData.result}</div>
                            </div>
                        `).show();
                        $(this).find('span').text('Скрыть информацию');
                    }
                });

                $(document).on('click', '.apply-history-full', function(e) {
                    e.preventDefault();
                    let data = table.row($(this).closest('tr')).data();
                    
                    $('#prompt-text').val(data.prompt);
                    $('#category-link').val(data.link);
                    $(`input[name="parsing_method"][value="${data.source}"]`).prop('checked', true);

                    if (window.applyWordsFromHistory) {
                        window.applyWordsFromHistory(data.keywords, data.stopwords);
                    }
                    toastr.success('Конфигурация восстановлена');
                });
            });
        </script>
    @endslot
@endcomponent

