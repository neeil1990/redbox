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
            <button class="btn btn-danger btn-sm" id="clear-stopwords">Очистить</button>
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
                if(word.includes(query)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
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

        $('#clear-stopwords').on('click', function() {
            $('#stopwords-table tbody').html(`
                <tr>
                    <td><input type="text" class="form-control" name="stopwords[]"></td>
                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>
            `); 
        });

        $('#stopwords-search').on('input', function() {
            let query = $(this).val().toLowerCase();

            $('#stopwords-table tbody tr').each(function() {
                let word = $(this).find('input[name="stopwords[]"]').val().toLowerCase();
                if(word.includes(query)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
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

                if (countSortAsc) {
                    return countA - countB;
                } else {
                    return countB - countA;
                }
            });

            tbody.append(rows); 
        });

        loadSavedStopWords();
    });

    function loadSavedStopWords() {
        $.get('/ai-generation/stopwords-list', function (words) {
            if (words.length > 0) {
                let tbody = $('#stopwords-table tbody');
                tbody.empty();

                words.forEach(word => {
                    tbody.append(`
                        <tr>
                            <td><input type="text" class="form-control" name="stopwords[]" value="${word}"></td>
                            <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                        </tr>
                    `);
                });
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

        $('#stopwords-table tbody tr').each(function () {
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
            keywordsBody.append(`
                <tr>
                    <td><input type="text" class="form-control" name="keywords[]"></td>
                    <td><input type="number" class="form-control" name="counts[]" value="1"></td>
                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>
            `);
        }

        let stopwordsBody = $('#stopwords-table tbody');
        stopwordsBody.empty();

        if (stopwordsArray && stopwordsArray.length > 0) {
            stopwordsArray.forEach(word => {
                stopwordsBody.append(`
                    <tr>
                        <td><input type="text" class="form-control" name="stopwords[]" value="${word}"></td>
                        <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                    </tr>
                `);
            });
        } else {
            stopwordsBody.append(`
                <tr>
                    <td><input type="text" class="form-control" name="stopwords[]"></td>
                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>
            `);
        }
    };

</script>