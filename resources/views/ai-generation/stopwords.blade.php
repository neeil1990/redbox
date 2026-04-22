@component('component.card', ['title' => 'Управление стоп-словами'])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    @endslot

    <div class="card">
        <div class="card-header d-flex p-0">
            @include('ai-generation.blocks.nav')
        </div>
        
        <div class="card-header p-2">
            <ul class="nav nav-pills" id="stopwords-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-words-link" data-toggle="pill" href="#tab-words" role="tab">Слова</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-categories-link" data-toggle="pill" href="#tab-categories" role="tab">Категории</a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">
                
                <div class="tab-pane fade show active" id="tab-words" role="tabpanel">
                    <div class="d-flex justify-content-between mb-4">
                        <form id="create-word-form" action="{{ route('ai.stopwords.store') }}" method="POST" class="form-inline">
                            @csrf
                            <select name="category_id" class="form-control mr-2">
                                <option value="">Без категории</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="word" class="form-control mr-2" placeholder="Новое слово" required>
                            <button type="submit" class="btn btn-primary">Добавить слово</button>
                        </form>

                        <div class="w-25">
                            <input type="text" id="search-stopwords" class="form-control" placeholder="Поиск слова...">
                        </div>
                    </div>

                    <table class="table table-sm table-hover w-100" id="stopwords-main-table">
                        <thead>
                            <tr>
                                <th>Слово</th>
                                <th>Категория</th>
                                <th width="150" class="text-right">Действие</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="tab-categories" role="tabpanel">
                    <div class="d-flex justify-content-between mb-4">
                        <form id="create-category-form" action="{{ route('ai.stopwords.categories.store') }}" method="POST" class="form-inline">
                            @csrf
                            <input type="text" name="name" class="form-control mr-2" placeholder="Название категории" required>
                            <button type="submit" class="btn btn-primary">Создать категорию</button>
                        </form>

                        <div class="w-25">
                            <input type="text" id="search-categories" class="form-control" placeholder="Поиск категории...">
                        </div>
                    </div>

                    <table class="table table-sm table-hover w-100" id="categories-main-table">
                        <thead>
                            <tr>
                                <th>Название категории</th>
                                <th width="150" class="text-right">Действие</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
         </div>
    </div>

    <div class="modal fade" id="editWordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="edit-word-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Редактировать слово</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Категория</label>
                            <select name="category_id" id="edit-category-select" class="form-control">
                                <option value="">Без категории</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Слово</label>
                            <input type="text" name="word" id="edit-word-input" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="edit-category-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Редактировать категорию</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Название</label>
                            <input type="text" name="name" id="edit-category-input" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('a[data-toggle="pill"]').on('show.bs.tab', function (e) {
            localStorage.setItem('activeStopwordsTab', $(e.target).attr('href'));
        });
        let activeTab = localStorage.getItem('activeStopwordsTab');
        if(activeTab) $('#stopwords-tabs a[href="' + activeTab + '"]').tab('show');
        let wordsTable = $('#stopwords-main-table').DataTable({
            ajax: "{{ route('ai.stopwords.datatable') }}",
            processing: true,
            pageLength: 25,
            language: { search: "", searchPlaceholder: "Поиск слова...", emptyTable: "Слов пока нет" },
            dom: '<"d-none"f>rt<"d-flex justify-content-between mt-2"ip>', 
            columns: [
                { data: 'word' },
                { 
                    data: 'category',
                    render: function (data) {
                        return data ? `<span class="badge badge-secondary">${data.name}</span>` : '<span class="text-muted small">Нет</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-right',
                    render: function (data) {
                        return `
                            <button class="btn btn-sm btn-outline-info mr-1 edit-word-btn" data-id="${data.id}" data-word="${data.word}" data-category="${data.category_id || ''}"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger delete-word-btn" data-id="${data.id}"><i class="fa fa-trash"></i></button>
                        `;
                    }
                }
            ]
        });

        // Таблица КАТЕГОРИЙ
        let categoriesTable = $('#categories-main-table').DataTable({
            ajax: "{{ route('ai.stopwords.categories.datatable') }}",
            processing: true,
            pageLength: 25,
            language: { search: "", searchPlaceholder: "Поиск категории...", emptyTable: "Категорий пока нет" },
            dom: '<"d-none"f>rt<"d-flex justify-content-between mt-2"ip>',
            columns: [
                { data: 'name', className: 'font-weight-bold' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-right',
                    render: function (data) {
                        return `
                            <button class="btn btn-sm btn-outline-info mr-1 edit-category-btn" data-id="${data.id}" data-name="${data.name}"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger delete-category-btn" data-id="${data.id}"><i class="fa fa-trash"></i></button>
                        `;
                    }
                }
            ]
        });

        // Кастомный поиск
        $('#search-stopwords').on('keyup input', function() { wordsTable.search(this.value).draw(); });
        $('#search-categories').on('keyup input', function() { categoriesTable.search(this.value).draw(); });

        // ==========================================
        // 2. ФУНКЦИЯ ОБНОВЛЕНИЯ SELECTОВ С КАТЕГОРИЯМИ
        // ==========================================
        function reloadCategorySelects() {
            $.get("{{ route('ai.stopwords.categories.datatable') }}", function(res) {
                let options = '<option value="">Без категории</option>';
                res.data.forEach(cat => { options += `<option value="${cat.id}">${cat.name}</option>`; });
                $('select[name="category_id"]').html(options);
            });
        }

        // ==========================================
        // 3. AJAX ДЕЙСТВИЯ (СОЗДАНИЕ / РЕДАКТИРОВАНИЕ)
        // ==========================================

        // Функция-обертка для отправки форм
        function handleAjaxForm(formSelector, tableToReload, modalToClose = null, updateSelects = false) {
            $(document).on('submit', formSelector, function(e) {
                e.preventDefault();
                let $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method') || 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        tableToReload.ajax.reload(null, false); // Перезагрузка таблицы без сброса страницы
                        if (formSelector.includes('create')) $form.trigger('reset'); // Очищаем инпуты
                        if (modalToClose) $(modalToClose).modal('hide'); // Закрываем модалку
                        if (updateSelects) reloadCategorySelects(); // Обновляем списки категорий
                        // Здесь можно добавить Toastr.success('Успешно');
                    },
                    error: function(err) {
                        alert('Ошибка сохранения. Проверьте правильность данных.');
                        console.error(err);
                    }
                });
            });
        }

        // Привязываем формы (ВНИМАНИЕ: добавь ID 'create-word-form' и 'create-category-form' к своим тегам <form> добавления!)
        handleAjaxForm('#create-word-form', wordsTable);
        handleAjaxForm('#edit-word-form', wordsTable, '#editWordModal');
        handleAjaxForm('#create-category-form', categoriesTable, null, true); // true = обновить select'ы
        handleAjaxForm('#edit-category-form', categoriesTable, '#editCategoryModal', true);

        // ==========================================
        // 4. AJAX УДАЛЕНИЕ
        // ==========================================

        // Универсальная функция удаления
        function handleAjaxDelete(btnSelector, urlPrefix, tableToReload, updateSelects = false) {
            $(document).on('click', btnSelector, function() {
                if (!confirm('Вы уверены, что хотите удалить эту запись?')) return;
                
                let id = $(this).data('id');
                $.ajax({
                    url: `${urlPrefix}/${id}`,
                    type: 'DELETE',
                    success: function() {
                        tableToReload.ajax.reload(null, false);
                        if(updateSelects) {
                            wordsTable.ajax.reload(null, false); // Перезагрузим слова, т.к. категория могла пропасть
                            reloadCategorySelects();
                        }
                    }
                });
            });
        }

        handleAjaxDelete('.delete-word-btn', '/ai-generation/stopwords', wordsTable);
        handleAjaxDelete('.delete-category-btn', '/ai-stopwords-categories', categoriesTable, true);

        // ==========================================
        // 5. ОТКРЫТИЕ МОДАЛОК
        // ==========================================
        $(document).on('click', '.edit-word-btn', function() {
            $('#edit-word-form').attr('action', `/ai-stopwords/${$(this).data('id')}`);
            $('#edit-word-input').val($(this).data('word'));
            $('#edit-category-select').val($(this).data('category'));
            $('#editWordModal').modal('show');
        });

        $(document).on('click', '.edit-category-btn', function() {
            $('#edit-category-form').attr('action', `/ai-stopwords-categories/${$(this).data('id')}`);
            $('#edit-category-input').val($(this).data('name'));
            $('#editCategoryModal').modal('show');
        });

    });
    </script>
    @endslot
@endcomponent