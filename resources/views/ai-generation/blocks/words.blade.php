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

        <div class="d-flex mb-2 justify-content-between">
            <input type="text" id="stopwords-search" class="form-control form-control-sm me-2 w-50" placeholder="Поиск слова...">
            <button class="btn btn-danger btn-sm" id="clear-stopwords">Очистить таблицу</button>
        </div>

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

        <button class="btn btn-secondary btn-sm" id="add-stopword">Добавить слово</button>
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
            updatePrompt();
        });

        $('#clear-stopwords').on('click', function() {
            $('#stopwords-table tbody').html(`
                <tr>
                    <td><input type="text" class="form-control" name="stopwords[]"></td>
                    <td><button class="btn btn-danger btn-sm remove-row">×</button></td>
                </tr>
            `); 
            updatePrompt();
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
                updatePrompt();
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
</script>