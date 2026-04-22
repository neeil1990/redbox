<div class="row">
    <div class="col-lg-6 col-md-12 mb-4">
        <h5>Добавляемые слова №1</h5>

        <div class="d-flex mb-2 justify-content-between align-items-center">
            <input type="text" id="keywords-search" class="form-control form-control-sm me-2" style="max-width: 300px;" placeholder="Поиск слова в таблице №1...">
            <button class="btn btn-danger btn-sm" id="clear-keywords">Очистить</button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="keywords-table">
                <thead>
                    <tr>
                        <th>Слово / Предложение</th>
                        <th width="140" style="cursor: pointer;" id="sort-keywords-count" class="text-nowrap user-select-none">
                            Количество <i class="fas fa-sort text-muted ms-1"></i>
                        </th>
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
        </div>
        <button class="btn btn-secondary btn-sm" id="add-keyword">
            <i class="fas fa-plus"></i> Добавить слово
        </button>
    </div>

    <div class="col-lg-6 col-md-12">
        <h5>Запрещённые слова №2</h5>

        <div class="d-flex mb-2 justify-content-between align-items-center">
            <input type="text" id="stopwords-search" class="form-control form-control-sm me-2" style="max-width: 300px;" placeholder="Поиск слова в таблице №2...">
            <div>
                <button class="btn btn-info btn-sm me-1" id="reload-stopwords" title="Загрузить заново из базы">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn btn-danger btn-sm" id="clear-stopwords">Очистить</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="stopwords-table">
                <thead>
                    <tr>
                        <th>Слово / Предложение</th>
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
        </div>
        <button class="btn btn-secondary btn-sm" id="add-stopword">
            <i class="fas fa-plus"></i> Добавить слово
        </button>
    </div>
</div>

<script>
    var keywords = [];
    var stopwords = [];

    $(document).ready(function () {
        // --- Блок "Добавляемые слова №1" остался без изменений ---
        $('#clear-keywords').on('click', function() {
            $('#keywords-table tbody').html(`
                <tr>
                    <td><input type="text" class="form-control" name="keywords[]"></td>
                    <td><input type="number" class="form-control" name="counts[]" value="1"></td>
                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>
            `); 
        });

        $('#keywords-search').on('input', function() {
            let query = $(this).val().toLowerCase();
            $('#keywords-table tbody tr').each(function() {
                let word = $(this).find('input[name="keywords[]"]').val().toLowerCase();
                $(this).toggle(word.includes(query));
            });
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

        let countSortAsc = true; 
        $('#sort-keywords-count').on('click', function() {
            let tbody = $('#keywords-table tbody');
            let rows = tbody.find('tr').toArray();
            let icon = $(this).find('i');

            countSortAsc = !countSortAsc;
            icon.removeClass('fa-sort fa-sort-up fa-sort-down');
            
            if (countSortAsc) {
                icon.addClass('fa-sort-up text-dark').removeClass('text-muted');
            } else {
                icon.addClass('fa-sort-down text-dark').removeClass('text-muted');
            }

            rows.sort(function(a, b) {
                let countA = parseInt($(a).find('input[name="counts[]"]').val()) || 0;
                let countB = parseInt($(b).find('input[name="counts[]"]').val()) || 0;
                return countSortAsc ? (countA - countB) : (countB - countA);
            });

            tbody.append(rows); 
        });

        $('#reload-stopwords').click(function() {
            let btn = $(this);
            let icon = btn.find('i');
            icon.addClass('fa-spin');
            
            loadSavedStopWords(function() {
                setTimeout(() => icon.removeClass('fa-spin'), 300);
            });
        });

        $('#add-stopword').click(function () {
            $('#stopwords-table tbody').append(`
                <tr class="stopword-row">
                    <td><input type="text" class="form-control" name="stopwords[]"></td>
                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>
            `);
        });

        $(document).on('click', '.remove-row', function () {
            let row = $(this).closest('tr');
            let tableId = row.closest('table').attr('id');
            let prevHeader = row.prevAll('.category-header:first');
            
            row.remove();

            if (tableId === 'stopwords-table' && prevHeader.length) {
                let nextRow = prevHeader.next();
                if (!nextRow.length || nextRow.hasClass('category-header')) {
                    prevHeader.remove();
                }
            }
        });

        $(document).on('click', '.remove-category-btn', function() {
            let header = $(this).closest('.category-header');
            // Удаляем все строки (.stopword-row) до следующего заголовка
            header.nextUntil('.category-header').remove();
            // Удаляем сам заголовок
            header.remove();
        });

        $('#clear-stopwords').on('click', function() {
            $('#stopwords-table tbody').html(`
                <tr class="stopword-row">
                    <td><input type="text" class="form-control" name="stopwords[]"></td>
                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>
            `); 
        });

        $('#stopwords-search').on('input', function() {
            let query = $(this).val().toLowerCase();
            $('#stopwords-table tbody tr.stopword-row').each(function() {
                let word = $(this).find('input[name="stopwords[]"]').val().toLowerCase();
                $(this).toggle(word.includes(query));
            });
        });

        loadSavedStopWords();
    });

    function loadSavedStopWords(callback = null) {
        $.get('/ai-generation/stopwords-list', function (data) {
            let tbody = $('#stopwords-table tbody');
            tbody.empty();

            if (data && Object.keys(data).length > 0) {
                let uncategorizedWords = null;
                if (data['Без категории']) {
                    uncategorizedWords = data['Без категории'];
                    delete data['Без категории']; 
                }

                function renderCategory(category, words) {
                    if (words.length > 0) {
                        let deleteBtnHtml = category === 'Без категории' 
                            ? '' 
                            : `<button class="btn btn-sm text-danger py-0 px-1 remove-category-btn" title="Удалить категорию из таблицы">
                                   <i class="fas fa-times"></i>
                               </button>`;

                        tbody.append(`
                            <tr class="table-secondary category-header">
                                <td colspan="2" class="font-weight-bold text-muted small text-uppercase" style="background-color: #f4f6f9;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-folder-open mr-1"></i> ${category}</span>
                                        ${deleteBtnHtml}
                                    </div>
                                </td>
                            </tr>
                        `);
                        
                        words.forEach(word => {
                            tbody.append(`
                                <tr class="stopword-row">
                                    <td><input type="text" class="form-control" name="stopwords[]" value="${word}"></td>
                                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                                </tr>
                            `);
                        });
                    }
                }

                for (const [category, words] of Object.entries(data)) {
                    renderCategory(category, words);
                }

                if (uncategorizedWords !== null) {
                    renderCategory('Без категории', uncategorizedWords);
                }

            } else {
                tbody.append(`
                    <tr class="stopword-row">
                        <td><input type="text" class="form-control" name="stopwords[]"></td>
                        <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                    </tr>
                `);
            }

            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function getWords() {
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
        return keywords;
    }

    function getStopWords() {
        let stopwords = [];
        $('#stopwords-table tbody tr.stopword-row').each(function () {
            let word = $(this).find('input[name="stopwords[]"]').val();
            if (word) stopwords.push(word);
        });
        return stopwords;
    }

    window.applyWordsFromHistory = function(keywordsArray, stopwordsArray) {
        let keywordsBody = $('#keywords-table tbody');
        keywordsBody.empty();

        if (keywordsArray && keywordsArray.length > 0) {
            keywordsArray.forEach(item => {
                keywordsBody.append(`
                    <tr>
                        <td><input type="text" class="form-control" name="keywords[]" value="${item.word || ''}"></td>
                        <td><input type="number" class="form-control" name="counts[]" value="${item.count || 1}"></td>
                        <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                    </tr>
                `);
            });
        } else {
            $('#clear-keywords').trigger('click');
        }

        let stopwordsBody = $('#stopwords-table tbody');
        stopwordsBody.empty();

        if (stopwordsArray && stopwordsArray.length > 0) {
            stopwordsBody.append(`
                <tr class="table-info category-header">
                    <td colspan="2" class="font-weight-bold text-muted small text-uppercase">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-history mr-1"></i> Восстановлено из истории</span>
                            <button class="btn btn-sm text-danger py-0 px-1 remove-category-btn" title="Удалить категорию">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);

            stopwordsArray.forEach(word => {
                stopwordsBody.append(`
                    <tr class="stopword-row">
                        <td><input type="text" class="form-control" name="stopwords[]" value="${word}"></td>
                        <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                    </tr>
                `);
            });
        } else {
            $('#clear-stopwords').trigger('click');
        }
    };
</script>