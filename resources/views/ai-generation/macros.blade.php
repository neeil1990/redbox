@component('component.card', ['title' => 'Управление макросами'])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    @endslot

    <div class="card">
        <div class="card-header d-flex p-0">
            @include('ai-generation.blocks.nav')
        </div>

        <div class="card-body">

            <div class="alert alert-secondary alert-dismissible mb-4 shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5 class="mb-1"><i class="icon fas fa-info-circle mr-2"></i> Как использовать макросы</h5>
                Чтобы использовать макрос в промпте, вам необходимо написать 
                <code class="bg-white px-2 py-1 rounded text-info font-weight-bold mx-1 border border-info">--название макроса--</code>
                <br>
                (два дефиса до и два дефиса после названия).
            </div>

            <div class="bg-light p-3 rounded mb-4 border">
                <h6 class="font-weight-bold mb-3">Создать новый макрос</h6>
                <form id="create-macro-form" action="{{ route('ai.macros.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" style="margin-bottom: 25px;">
                                <label>Название <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Например: Вступление для статьи" required>
                            </div>
                            <div class="form-group">
                                <label>Описание</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Для чего нужен этот макрос..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group d-flex flex-column">
                                <label>Текст макроса <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control flex-grow-1" rows="7" placeholder="Содержимое макроса..." required></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus mr-1"></i> Сохранить макрос</button>
                </form>
            </div>

            <div class="d-flex justify-content-end mb-2">
                <div class="w-25">
                    <input type="text" id="search-macros" class="form-control form-control-sm" placeholder="Поиск макросов...">
                </div>
            </div>

            <table class="table table-sm table-hover w-100" id="macros-main-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Содержимое</th>
                        <th width="100" class="text-right">Действие</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="editMacroModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="edit-macro-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Редактировать макрос</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Название</label>
                                    <input type="text" name="name" id="edit-macro-name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Описание</label>
                                    <textarea name="description" id="edit-macro-description" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group h-100 d-flex flex-column">
                                    <label>Текст макроса</label>
                                    <textarea name="content" id="edit-macro-content" class="form-control flex-grow-1" style="min-height: 200px;" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @slot('js')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        let macrosTable = $('#macros-main-table').DataTable({
            ajax: "{{ route('ai.macros.datatable') }}",
            processing: true,
            pageLength: 25,
            language: {
                sEmptyTable: "Нет данных для отображения",
                sInfo: "Показано с _START_ по _END_ из _TOTAL_ записей",
                sInfoEmpty: "Показано 0 записей",
                sInfoFiltered: "(отфильтровано из _MAX_ записей)",
                sLengthMenu: "Показывать _MENU_ записей на странице",
                sSearch: "Поиск:",
                sZeroRecords: "Нет соответствующих записей",
                searchPlaceholder: 'Поиск',
                paginate: {
                    "first": "«",
                    "last": "»",
                    "next": "»",
                    "previous": "«"
                },
            },
            dom: '<"d-none"f>rt<"d-flex justify-content-between mt-2"ip>',
            columns: [
                { data: 'name', className: 'font-weight-bold' },
                { 
                    data: 'description', 
                    render: function(data) { return data ? `<span class="text-muted small">${data}</span>` : ''; }
                },
                { 
                    data: 'content',
                    render: function(data) { 
                        if (!data) return '';
                        
                        let escapedData = escapeHtml(data);
                        let formattedFull = escapedData.replace(/\n/g, '<br>');
                        
                        if (escapedData.length <= 60 && !escapedData.includes('\n')) {
                            return `<div class="text-muted small">${formattedFull}</div>`;
                        }

                        let truncated = escapedData.substring(0, 60).replace(/\n/g, ' ') + '...';
                        
                        return `
                            <div class="card shadow-none border mb-0 macro-card">
                                <div class="p-2 d-flex justify-content-between align-items-center bg-light">
                                    <span class="macro-short-text text-muted small">${truncated}</span>
                                    <i class="fa fa-eye text-primary macro-toggle-icon ml-2" style="cursor: pointer; font-size: 1.1rem;" title="Развернуть/Свернуть"></i>
                                </div>
                                <div class="card-body p-2 border-top d-none small text-dark" style="word-break: break-word; white-space: pre-wrap;">${formattedFull}</div>
                            </div>
                        `;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-right',
                    render: function (data) {
                        return `
                            <button class="btn btn-sm btn-outline-info mr-1 edit-macro-btn" 
                                data-id="${data.id}" 
                                data-name="${escapeHtml(data.name)}" 
                                data-desc="${escapeHtml(data.description || '')}"
                                data-content="${escapeHtml(data.content)}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-macro-btn" data-id="${data.id}"><i class="fa fa-trash"></i></button>
                        `;
                    }
                }
            ]
        });

        $('#search-macros').on('keyup input', function() { macrosTable.search(this.value).draw(); });

        function escapeHtml(text) {
            if(!text) return '';
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function handleAjaxForm(formSelector, tableToReload, modalToClose = null) {
            $(document).on('submit', formSelector, function(e) {
                e.preventDefault();
                let $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method') || 'POST',
                    data: $form.serialize(),
                    success: function() {
                        tableToReload.ajax.reload(null, false);
                        if (formSelector.includes('create')) $form.trigger('reset');
                        if (modalToClose) $(modalToClose).modal('hide');
                    },
                    error: function(err) {
                        alert('Ошибка сохранения.');
                        console.error(err);
                    }
                });
            });
        }

        handleAjaxForm('#create-macro-form', macrosTable);
        handleAjaxForm('#edit-macro-form', macrosTable, '#editMacroModal');

        $(document).on('click', '.delete-macro-btn', function() {
            if (!confirm('Вы уверены, что хотите удалить этот макрос?')) return;
            $.ajax({
                url: `/ai-macros/${$(this).data('id')}`,
                type: 'DELETE',
                success: function() { macrosTable.ajax.reload(null, false); }
            });
        });

        $(document).on('click', '.edit-macro-btn', function() {
            $('#edit-macro-form').attr('action', `/ai-macros/${$(this).data('id')}`);
            $('#edit-macro-name').val($(this).data('name'));
            $('#edit-macro-description').val($(this).data('desc'));
            $('#edit-macro-content').val($(this).data('content'));
            $('#editMacroModal').modal('show');
        });

        $(document).on('click', '.macro-toggle-icon', function() {
            let card = $(this).closest('.macro-card');
            let body = card.find('.card-body');
            let shortText = card.find('.macro-short-text');
            
            if (body.hasClass('d-none')) {
                body.removeClass('d-none');
                shortText.text('Полное содержимое:');
                $(this).removeClass('fa-eye text-primary').addClass('fa-eye-slash text-secondary');
            } else {
                body.addClass('d-none');
                let fullText = body.text().trim();
                let truncated = fullText.substring(0, 60).replace(/\n/g, ' ') + '...';
                shortText.text(truncated);
                
                $(this).removeClass('fa-eye-slash text-secondary').addClass('fa-eye text-primary');
            }
        });
    });
    </script>
    @endslot
@endcomponent