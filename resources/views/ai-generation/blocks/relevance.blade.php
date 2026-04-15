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
                    <option value="">Загрузка проектов...</option>
                </select>
            </div>

            <div class="form-group mt-3 mb-3">
                <label>Фраза / страница</label>
                <select id="relevance-select" class="form-control" disabled>
                    <option value="">Сначала выберите проект</option>
                </select>
            </div>

            <div class="border p-3 mb-3 rounded bg-light">
                <h6>Настройки выгрузки слов</h6>
                
                <div class="mb-2">
                    <strong>1. Что загружать:</strong><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input filter-trigger" type="checkbox" id="load-unigram" checked>
                        <label class="form-check-label" for="load-unigram">Слова (Униграммы)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input filter-trigger" type="checkbox" id="load-phrases" checked>
                        <label class="form-check-label" for="load-phrases">Словосочетания (Фразы)</label>
                    </div>
                </div>

                <div>
                    <strong>2. Правило фильтрации:</strong><br>
                    <div class="form-check">
                        <input class="form-check-input filter-trigger" type="radio" name="filter_rule" id="rule-diff" value="diff" checked>
                        <label class="form-check-label" for="rule-diff">Только недостающие <small class="text-muted">(Среднее кол-во повторений конкурента > Среднее кол-во повторений посадочной страницы)</small></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input filter-trigger" type="radio" name="filter_rule" id="rule-zero" value="zero">
                        <label class="form-check-label" for="rule-zero">Только отсутствующие <small class="text-muted">(Кол-во повторений посадочной страницы = 0)</small></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input filter-trigger" type="radio" name="filter_rule" id="rule-all" value="all">
                        <label class="form-check-label" for="rule-all">Все по 1 разу</label>
                    </div>
                </div>
            </div>
            <div class="form-text text-muted mb-3">
                Выбор проекта не обязателен — вы можете указать ссылку вручную.
            </div>
        </div>
    </div>
</div>

<script>
    let currentPhrasesData = null;

    $(document).ready(function() {
        $.get('/relevance-projects', function(data) {
            let options = '<option value="">Выберите проект</option>';
            
            data.forEach(function(project) {
                options += `<option value="${project.id}">${project.name}</option>`;
            });

            $('#project-select').html(options);
            $('#project-select').trigger('change');
            
        }).fail(function() {
            $('#project-select').html('<option value="">Ошибка загрузки проектов</option>');
        });

        $('#relevance-select').select2({
            placeholder: "Выберите фразу",
            allowClear: true,
            width: '100%'
        });

        $('#project-select').select2({
            placeholder: "Выберите проект",
            allowClear: true,
            width: '100%'
        });

        $('#project-select').on('change', function () {
            let projectId = $(this).val();

            if (projectId) {
                $('#category-link')
                    .prop('readonly', true)
                    .addClass('bg-light');
            } else {
                $('#category-link')
                    .prop('readonly', false)
                    .removeClass('bg-light');
            }

            $('#relevance-select').prop('disabled', true).html('<option>Выберите проект</option>');

            if (!projectId) return;

            $.get(`/relevance-history/${projectId}`, function (data) {
                data.sort((a, b) => {
                    return a.phrase.localeCompare(b.phrase);
                });

                let options = '<option value="">Выберите</option>';
                data.forEach(item => {
                    options += `<option value="${item.id}" data-link="${item.main_link}">
                        ${item.phrase} | ${item.main_link} | ${item.created_at}
                    </option>`;
                });

                $('#relevance-select').html(options).prop('disabled', false);
            });
        });

        $('#relevance-select').on('change', function () {
            let projectId = $(this).val();
            let link = $(this).find(':selected').data('link');

            if(link) {
                $('#category-link').val(link).trigger('input');
            }

            $.get(`/relevance-history/getPhrases/${projectId}`, function (response) {
                if(Object.keys(response.phrases).length === 0 && Object.keys(response.unigram).length === 0) {
                    toastr.error('Фразы не найдены');
                    return;
                }
                currentPhrasesData = response; 
                
                applyFiltersAndRender();
            });
        });

        $('.filter-trigger').on('change', function() {
            if (currentPhrasesData) {
                applyFiltersAndRender();
            }
        });
        
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
        });
    });

    function applyFiltersAndRender() {
        $('#keywords-table tbody').html('');

        let loadUnigrams = $('#load-unigram').is(':checked');
        let loadPhrases = $('#load-phrases').is(':checked');

        if (loadPhrases && currentPhrasesData.phrases) {
            renderRows(currentPhrasesData.phrases);
        }
        if (loadUnigrams && currentPhrasesData.unigram) {
            renderRows(currentPhrasesData.unigram);
        }
    }

    function renderRows(words) {
        let rows = '';
        let filterRule = $('input[name="filter_rule"]:checked').val();

        for (let word in words) {
            if (word !== '') {
                let item = words[word];
                let = avg = parseFloat(item.total && item.total.avgInTotalCompetitors ? item.total.avgInTotalCompetitors : 0);
                let total = parseFloat(item.total && item.total.totalRepeatMainPage ? item.total.totalRepeatMainPage : 0);

                let shouldAdd = false;
                let countValue = 0;

                if (filterRule === 'diff') {
                    if (avg > total) {
                        shouldAdd = true;
                        countValue = Math.ceil(avg - total);
                    }
                } else if (filterRule === 'zero') {
                    if (total === 0) {
                        shouldAdd = true;
                        countValue = Math.ceil(avg) > 0 ? Math.ceil(avg) : 1; 
                    }
                } else if (filterRule === 'all') {
                    shouldAdd = true;
                    countValue = 1;
                }

                if (shouldAdd) {
                    rows += `
                        <tr>
                            <td><input type="text" class="form-control" name="keywords[]" value="${word}"></td>
                            <td><input type="number" class="form-control" name="counts[]" value="${countValue}"></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-row">×</button></td>
                        </tr>
                    `;
                }
            }
        }

        $('#keywords-table tbody').append(rows);
    }
</script>