@component('component.card', ['title' => 'История'])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    @endslot

    <div class="card">
        <div class="card-header d-flex p-0">
            @include('ai-generation.nav')
        </div>
        <div class="card-body">
            <table id="history-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>Тип</th>
                    <th>Статус</th>
                    <th>Дата</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($generationHistory as $item)
                    <tr data-prompt='{!! $item->prompt !!}'
                        data-result='{!! $item->result !!}'>
                        <td class="details-control" style="cursor:pointer;">Показать информацию</td>
                        <td>@if($item->type === \App\AiGenerationHistory::TYPE_CATEGORY) Текст категории @else ыыы @endif</td>
                        <td>
                            @if($item->status === \App\AiGenerationHistory::COMPLETED)
                                <span class="badge badge-success">Завершено</span>
                            @elseif($item->status === \App\AiGenerationHistory::FAILED)
                                <span class="badge badge-danger">Не удалось</span>
                            @else
                                <span class="badge badge-warning">В ожидании</span>
                            @endif
                        </td>
                        <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                                    Действия
                                </button>
                                <div class="dropdown-menu">
                                    <a href="#" class="dropdown-item copy-result">Скопировать результат</a>
                                    <a href="#" class="dropdown-item copy-prompt">Скопировать промпт</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
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
                let table = $('#history-table').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "order": [[3, "desc"]],
                    "pageLength": 10,
                    "columnDefs": [
                        { "orderable": false, "targets": [0, 4] }
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json"
                    }
                });

                $('#history-table tbody').on('click', 'td.details-control', function () {
                    let tr = $(this).closest('tr');
                    let row = table.row(tr);

                    if (row.child.isShown()) {
                        row.child.hide();
                        tr.find('td.details-control').text('Показать информацию');
                    } else {
                        row.child(format(tr)).show();
                        tr.find('td.details-control').text('Скрыть информацию');
                    }
                });

                function copyToClipboard(text) {
                    toastr.success('Скопировано в буфер обмена');
                    
                    if (navigator.clipboard && window.isSecureContext) {
                        return navigator.clipboard.writeText(text);
                    } else {
                        return new Promise(function (resolve, reject) {
                            let textarea = document.createElement("textarea");
                            textarea.value = text;

                            textarea.style.position = "fixed";
                            textarea.style.left = "-999999px";

                            document.body.appendChild(textarea);
                            textarea.focus();
                            textarea.select();

                            try {
                                let successful = document.execCommand('copy');
                                document.body.removeChild(textarea);

                                if (successful) {
                                    resolve();
                                } else {
                                    reject();
                                }
                            } catch (err) {
                                document.body.removeChild(textarea);
                                reject(err);
                            }
                        });
                    }

                }

                $(document).on('click', '.copy-result', function (e) {
                    e.preventDefault();

                    let tr = $(this).closest('tr');
                    let result = tr.data('result');

                    try { result = JSON.parse(result); } catch (e) {}

                    copyToClipboard(result, $(this));
                });

                $(document).on('click', '.copy-prompt', function (e) {
                    e.preventDefault();

                    let tr = $(this).closest('tr');
                    let prompt = tr.data('prompt');

                    try { prompt = JSON.parse(prompt); } catch (e) {}

                    copyToClipboard(prompt, $(this));
                });
            });
        </script>
    @endslot
@endcomponent

