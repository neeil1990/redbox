@component('component.card', ['title' => 'Категории'])
    @slot('css')
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
        <style>
            #prompt-preview {
                max-height: 450px;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 10px;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                background: #f8f9fa;
            }

            .card-body::after, .card-footer::after, .card-header::after {
                display: none;
            }
        </style>
    @endslot

    <div class="card">
        <div class="card-header d-flex p-0">
            @include('ai-generation.nav')
        </div>
        <div class="card-body">
            <div id="prompt-preview"></div>
            <div class="form-group mt-3 mb-3">
                <label>Посадочная страница</label>
                <input type="text" class="form-control" id="category-link" placeholder="https://example.com/category/...">
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>Выбор проекта (Анализ релевантности)</div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-toggle="collapse" data-target="#project-card-body" aria-expanded="false" aria-controls="project-card-body">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <div id="project-card-body" class="collapse">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Проект</label>
                            <select id="project-select" class="form-control">
                                <option value="">Выберите проект</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mt-3 mb-3">
                            <label>Фраза / страница</label>
                            <select id="relevance-select" class="form-control" disabled>
                                <option value="">Сначала выберите проект</option>
                            </select>
                        </div>

                        <div class="form-text text-muted mb-3">
                            Выбор проекта не обязателен — вы можете указать ссылку вручную.
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <h5>Добавляемые слова</h5>

                    <div class="d-flex mb-2 justify-content-between">
                        <input type="text" id="keywords-search" class="form-control form-control-sm me-2 w-50" placeholder="Поиск слова...">
                        <button class="btn btn-danger btn-sm" id="clear-keywords">Очистить таблицу</button>
                    </div>
                    <table class="table table-bordered" id="keywords-table">
                        <thead>
                        <tr>
                            <th>Слово / Предложение</th>
                            <th width="10">Количество</th>
                            <th width="50"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" class="form-control" name="keywords[]"></td>
                            <td><input type="number" class="form-control" name="counts[]" value="1"></td>
                            <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                        </tr>
                        </tbody>
                    </table>

                    <button class="btn btn-secondary btn-sm" id="add-keyword">Добавить слово</button>
                </div>

                <div class="col-6">
                    <h5>Запрещённые слова</h5>

                    <table class="table table-bordered" id="stopwords-table">
                        <thead>
                        <tr>
                            <th>Слово\Предложение</th>
                            <th width="50"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" class="form-control" name="stopwords[]"></td>
                            <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                        </tr>
                        </tbody>
                    </table>

                    <button class="btn btn-secondary btn-sm" id="add-stopword">Добавить слово</button>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-success generate-button" data-mode="new">
                    Сгенерировать текст
                </button>
            </div>

            <div id="generation-result" class="d-none mt-4">
                <div id="generation-loading" class="text-muted">
                    <div class="d-flex align-items-center mt-4">
                        <span id="loading-text">Генерация текста</span>
                        <div class="spinner-border text-primary ml-2 me-2" role="status"></div>
                    </div>
                </div>

                <div id="generation-success" class="d-none">
                    <div class="row">
                        <div class="col-md-8">
                            <textarea id="result-text" class="form-control" rows="12"></textarea>

                            <button class="btn btn-primary mt-2" id="copy-result">
                                Скопировать
                            </button>
                        </div>

                        <div class="col-md-4">
                            <div class="card p-3">
                                <h6>Примечание</h6>

                                <textarea id="regenerate-note"
                                        class="form-control mb-2"
                                        rows="6"
                                        placeholder="Например: сделай текст более продающим, добавь преимущества, упростить стиль..."></textarea>

                                <button class="btn btn-warning w-100 mb-2 generate-button" data-mode="new">
                                    Новая генерация с примечанием
                                </button>

                                <button class="btn btn-info w-100 generate-button" data-mode="regenerate">
                                    Перегенерация полученного результата
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @slot('js')
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
        <script>
            $('#project-select').on('change', function () {
                let projectId = $(this).val();

                // Блокируем поле, если выбран проект
                if (projectId) {
                    $('#category-link')
                        .prop('readonly', true)
                        .addClass('bg-light'); // визуально показать что поле заблокировано
                } else {
                    $('#category-link')
                        .prop('readonly', false)
                        .removeClass('bg-light');
                }

                $('#relevance-select').prop('disabled', true).html('<option>Загрузка...</option>');

                if (!projectId) return;

                $.get(`/relevance-history/${projectId}`, function (data) {
                    let options = '<option value="">Выберите</option>';

                    data.forEach(item => {
                        options += `<option 
                            value="${item.main_link}" 
                            data-id="${item.id}">
                            ${item.phrase} (${item.main_link})
                        </option>`;
                    });

                    $('#relevance-select').html(options).prop('disabled', false);
                });
            });

            $('#relevance-select').on('change', function () {
                let projectId = $('#project-select').val(); // ✅ берём тут
                let link = $(this).val();

                if (!projectId) return;

                // подставляем ссылку в input
                $('#category-link').val(link).trigger('input');

                $.get(`/relevance-history/getPhrases/${projectId}`, function (response) {
                    console.log('Ответ phrases:', response);

                    let phrases = response.phrases;

                    $('#keywords-table tbody').html('');

                    for (let word in phrases) {
                        let item = phrases[word];

                        let avg = parseFloat(item.avgInTotalCompetitors) || 0;
                        let total = parseFloat(item.totalRepeatMainPage) || 0;

                        if (avg > total) {
                            let diff = Math.ceil(avg - total);

                            let row = `
                                <tr>
                                    <td><input type="text" class="form-control" name="keywords[]" value="${word}"></td>
                                    <td><input type="number" class="form-control" name="counts[]" value="${diff}"></td>
                                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                                </tr>
                            `;

                            $('#keywords-table tbody').append(row);
                        }
                    }

                    updatePrompt();
                });
            });
        </script>
        <script>
            let lastId = null;

            $('.generate-button').click(function () {
                let mode = $(this).data('mode');
                let link = $('#category-link').val().trim();
                let note = $('#regenerate-note').val().trim();

                let isRegenerate = (mode === 'regenerate');

                if (isRegenerate) {
                    $('#loading-text').text('Перегенерация текущего текста...');
                } else if (note) {
                    $('#loading-text').text('Новая генерация с примечанием...');
                } else {
                    $('#loading-text').text('Генерация текста...');
                }

                if (!isValidUrl(link)) {
                    toastr.error('Введите корректную ссылку');
                    return;
                }

                let keywords = [];
                $('#keywords-table tbody tr').each(function () {
                    let word = $(this).find('input[name="keywords[]"]').val();
                    let count = $(this).find('input[name="counts[]"]').val();

                    if (word) {
                        keywords.push({
                            word: word,
                            count: parseInt(count) || 1
                        });
                    }
                });

                let stopwords = [];
                $('#stopwords-table tbody tr').each(function () {
                    let word = $(this).find('input[name="stopwords[]"]').val();
                    if (word) stopwords.push(word);
                });

                $.ajax({
                    url: "{{ route('ai.generation.category.generate') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        link: link,
                        keywords: keywords,
                        stopwords: stopwords,
                        note: note,
                        mode: mode,
                        current_text: isRegenerate ? $('#result-text').val() : null,
                        id: lastId
                    },
                    beforeSend: function () {
                        $('.generate-button').prop('disabled', true);
                        $('#generation-loading').removeClass('d-none');
                    },
                    success: function (response) {
                        toastr.success('Запрос отправлен');
                        let recordId = response.record_id;

                        $('#generation-result').removeClass('d-none');
                        $('#generation-loading').removeClass('d-none');

                        startPolling(recordId);
                    },
                    error: function (xhr) {
                        toastr.error('Ошибка при генерации');
                        console.error(xhr.responseText);
                    },
                });

            });

            $('#copy-result').click(function () {
                let text = $('#result-text').val();

                navigator.clipboard.writeText(text).then(function () {
                    toastr.success('Скопировано!');
                });
            });

            $('#clear-keywords').on('click', function() {
                $('#keywords-table tbody').html(`
                    <tr>
                        <td><input type="text" class="form-control" name="keywords[]"></td>
                        <td><input type="number" class="form-control" name="counts[]" value="1"></td>
                        <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                    </tr>
                `); 
               updatePrompt();
            });

            $('#keywords-search').on('input', function() {
                let query = $(this).val().toLowerCase();

                $('#keywords-table tbody tr').each(function() {
                    let word = $(this).find('input[name="keywords[]"]').val().toLowerCase();
                    if(word.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $(document).ready(function () {
                $('#category-link').on('input', function () {
                    let val = $(this).val().trim();

                    if (!val) {
                        $('#preview-link')
                            .text('[ссылка не указана]')
                            .removeClass('text-success text-danger')
                            .addClass('text-muted');

                        $(this).removeClass('is-valid is-invalid');
                        return;
                    }

                    if (isValidUrl(val)) {
                        $('#preview-link')
                            .text(val)
                            .removeClass('text-danger text-muted')
                            .addClass('text-success');

                        $(this).removeClass('is-invalid').addClass('is-valid');
                    } else {
                        $('#preview-link')
                            .text('Некорректная ссылка')
                            .removeClass('text-success text-muted')
                            .addClass('text-danger');

                        $(this).removeClass('is-valid').addClass('is-invalid');
                    }
                });

                $('#add-keyword').click(function () {
                    $('#keywords-table tbody').append(`
                        <tr>
                            <td><input type="text" class="form-control" name="keywords[]"></td>
                            <td><input type="number" class="form-control" name="counts[]" value="1"></td>
                            <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                        </tr>
                    `);
                });

                $('#add-stopword').click(function () {
                    $('#stopwords-table tbody').append(`
                        <tr>
                            <td><input type="text" class="form-control" name="stopwords[]"></td>
                            <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                        </tr>
                    `);
                });

                $(document).on('click', '.remove-row', function () {
                    $(this).closest('tr').remove();
                });

                $('#category-link').on('input', updatePrompt);
                $('#keywords-table, #stopwords-table').on('input', 'input', updatePrompt);
                $(document).ready(updatePrompt);
            });
            
            function isValidUrl(string) {
                try {
                    let url = new URL(string);

                    // только http/https
                    return url.protocol === "http:" || url.protocol === "https:";
                } catch (_) {
                    return false;
                }
            }

            function updatePrompt() {
                let link = $('#category-link').val().trim() || '[ссылка не указана]';

                let addWords = '';
                $('#keywords-table tbody tr').each(function () {
                    let word = $(this).find('input[name="keywords[]"]').val();
                    let count = $(this).find('input[name="counts[]"]').val();
                    if (word) {
                        addWords += `<br><b>Нужно добавить слово ${word} или любое его склонение, число, падеж ${count} раз(а), чтобы слово естественно вписывалось в текст.</b>`;
                    }
                });
                if (addWords) addWords += '<br>';

                let cancelWords = '';
                $('#stopwords-table tbody tr').each(function () {
                    let word = $(this).find('input[name="stopwords[]"]').val();
                    if (word) {
                        cancelWords += `<b>Запрещено использовать слово и любое его склонение, число или падеж: ${word}</b><br>`;
                    }
                });
                if (cancelWords) cancelWords += '<br>';

                let prompt = `
                    Роль: <br>
                    Ты — профессиональный копирайтер.<br><br>

                    Задача:<br>
                    Составь уникальный текст для категории товаров, которая расположена по ссылке: <b>${link}</b>. <br>
                    Для составления текста используй реальное содержимое указанной страницы.<br><br>

                    Текст должен быть составлен таким образом, чтобы его можно было разместить на сайте в качестве SEO-текста для привлечения клиентов. Достаточно составить один вариант текста.<br>
                    Ты обязан выполнить следующие требования:<br>
                    ${addWords}
                    Если ты не можешь вписать в текст слово, пропусти его.<br><br>

                    ${cancelWords}
                    Уникальность и грамотность:<br>
                    Текст должен быть полностью уникальным (не скопирован с других сайтов).<br>
                    Предложения должны быть грамотными, правильными с точки зрения русского языка и легко читаться.<br>
                `;

                $('#prompt-preview').html(prompt);
            }

            function startPolling(recordId) {
                let interval = setInterval(function () {
                    $.ajax({
                        url: "/ai-generation/get-result/" + recordId,
                        method: "GET",
                        success: function (response) {
                            if (response.record) {
                                $('.generate-button').prop('disabled', false);

                                clearInterval(interval);

                                if (response.record.status === 'failed') {
                                    toastr.error('Ошибка генерации');
                                    return;
                                }

                                $('#generation-loading').addClass('d-none');
                                $('#generation-success').removeClass('d-none');

                                $('#result-text').val(response.record.result);

                                lastId = response.record.id;
                            }
                        },
                        error: function () {
                            console.log('Ошибка при проверке статуса');
                        }
                    });
                }, 3000);
            }
        </script>
    @endslot
@endcomponent

